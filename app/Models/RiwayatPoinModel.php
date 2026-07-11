<?php

// Lokasi file ini di project: app/Models/RiwayatPoinModel.php

namespace App\Models;

use CodeIgniter\Model;

class RiwayatPoinModel extends Model
{
    protected $table            = 'riwayat_poin';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = false;

    protected $allowedFields = [
        'user_id',
        'transaksi_id',
        'jenis',
        'jumlah_poin',
        'keterangan',
    ];

    /**
     * Ambil semua riwayat poin milik satu user, terbaru dulu.
     */
    public function getRiwayatByUser(int $userId): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Catat penambahan poin (dipanggil setelah transaksi sukses / status Active-Terposting).
     */
    public function tambahPoin(int $userId, int $jumlah, ?int $transaksiId = null, string $keterangan = ''): bool
    {
        $data = [
            'user_id'     => $userId,
            'jenis'       => 'masuk',
            'jumlah_poin' => $jumlah,
            'keterangan'  => $keterangan ?: 'Poin dari transaksi',
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        if ($transaksiId !== null) {
            $data['transaksi_id'] = $transaksiId;
        }

        // Pakai query builder langsung (bukan $this->insert()) untuk menghindari
        // bug internal Model::insert() yang terjadi di environment ini.
        return (bool) \Config\Database::connect()->table('riwayat_poin')->insert($data);
    }

    /**
     * Catat pemakaian/pengurangan poin (dipanggil saat pembeli pakai poin untuk potong tagihan,
     * atau saat rollback karena transaksi dibatalkan / status Cancel).
     */
    public function kurangiPoin(int $userId, int $jumlah, ?int $transaksiId = null, string $keterangan = ''): bool
    {
        $data = [
            'user_id'     => $userId,
            'jenis'       => 'keluar',
            'jumlah_poin' => $jumlah,
            'keterangan'  => $keterangan ?: 'Poin dipakai saat transaksi',
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        if ($transaksiId !== null) {
            $data['transaksi_id'] = $transaksiId;
        }

        return (bool) \Config\Database::connect()->table('riwayat_poin')->insert($data);
    }

    /**
     * ==========================================================================
     * INTEGRASI TRANSAKSI — dipanggil dari Controller Transaksi (Erick) nanti.
     * Method di bawah ini langsung update kolom saldo_poin di tabel `user`
     * lewat query builder (BUKAN lewat UserModel), supaya tidak perlu ubah
     * allowedFields di UserModel milik Jasmen.
     * ==========================================================================
     */

    /**
     * Ambil saldo poin user saat ini.
     */
    public function getSaldoPoin(int $userId): int
    {
        $row = \Config\Database::connect()
            ->table('user')
            ->select('saldo_poin')
            ->where('id', $userId)
            ->get()
            ->getRowArray();

        return (int) ($row['saldo_poin'] ?? 0);
    }

    /**
     * Proses poin didapat dari transaksi sukses.
     * Panggil ini SETELAH transaksi berhasil disimpan dengan status
     * 5 (Active) atau 20 (Terposting).
     *
     * Contoh pemakaian di Controller Transaksi:
     *   $poinModel    = new \App\Models\PoinModel();
     *   $riwayatModel = new \App\Models\RiwayatPoinModel();
     *   $poinDidapat  = $poinModel->hitungPoin($totalBelanja);
     *   if ($poinDidapat > 0) {
     *       $riwayatModel->prosesTambahPoin($userId, $poinDidapat, $transaksiId);
     *   }
     */
    public function prosesTambahPoin(int $userId, int $jumlahPoin, ?int $transaksiId = null, string $keterangan = ''): bool
    {
        if ($jumlahPoin <= 0) {
            return false;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $this->tambahPoin($userId, $jumlahPoin, $transaksiId, $keterangan ?: 'Poin didapat dari transaksi');

        $saldoBaru = $this->getSaldoPoin($userId) + $jumlahPoin;

        $db->table('user')
           ->where('id', $userId)
           ->update(['saldo_poin' => $saldoBaru]);

        $db->transComplete();

        return $db->transStatus();
    }

    /**
     * Proses pemakaian poin untuk potong tagihan (Multipayment).
     * Panggil ini SAAT checkout, kalau pembeli memilih pakai saldo poin.
     * Return false kalau saldo poin user tidak cukup (tidak akan memotong apapun).
     *
     * Contoh pemakaian:
     *   $berhasil = $riwayatModel->prosesPakaiPoin($userId, $poinDipakai, $transaksiId);
     *   if (! $berhasil) {
     *       // saldo poin tidak cukup, batalkan proses pakai poin
     *   }
     */
    public function prosesPakaiPoin(int $userId, int $jumlahPoin, ?int $transaksiId = null, string $keterangan = ''): bool
    {
        if ($jumlahPoin <= 0) {
            return false;
        }

        $saldoSekarang = $this->getSaldoPoin($userId);

        if ($saldoSekarang < $jumlahPoin) {
            return false; // saldo tidak cukup
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $this->kurangiPoin($userId, $jumlahPoin, $transaksiId, $keterangan ?: 'Poin dipakai untuk potong tagihan');

        $saldoBaru = $saldoSekarang - $jumlahPoin;

        $db->table('user')
           ->where('id', $userId)
           ->update(['saldo_poin' => $saldoBaru]);

        $db->transComplete();

        return $db->transStatus();
    }

    /**
     * Rollback poin kalau transaksi dibatalkan (status 8 = Cancel).
     * Kembalikan poin yang sempat dipakai, DAN batalkan poin yang sempat didapat
     * dari transaksi tersebut (kalau ada), berdasarkan transaksi_id.
     */
    public function rollbackPoinByTransaksi(int $transaksiId): bool
    {
        $riwayatList = $this->where('transaksi_id', $transaksiId)->findAll();

        if (empty($riwayatList)) {
            return true; // tidak ada poin terkait transaksi ini, tidak perlu rollback
        }

        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($riwayatList as $riwayat) {
            $userId = (int) $riwayat['user_id'];
            $jumlah = (int) $riwayat['jumlah_poin'];

            // Kalau dulu 'masuk' (dapat poin), sekarang dikurangi lagi (dibatalkan)
            // Kalau dulu 'keluar' (dipakai poin), sekarang dikembalikan
            $penyesuaian = $riwayat['jenis'] === 'masuk' ? -$jumlah : $jumlah;
            $saldoBaru   = $this->getSaldoPoin($userId) + $penyesuaian;
            $saldoBaru   = max(0, $saldoBaru); // jaga-jaga supaya saldo tidak minus

            $db->table('user')
               ->where('id', $userId)
               ->update(['saldo_poin' => $saldoBaru]);
        }

        \Config\Database::connect()->table('riwayat_poin')->insert([
            'user_id'      => $riwayatList[0]['user_id'],
            'transaksi_id' => $transaksiId,
            'jenis'        => 'keluar',
            'jumlah_poin'  => 0,
            'keterangan'   => 'Rollback poin karena transaksi dibatalkan (status Cancel)',
            'created_at'   => date('Y-m-d H:i:s'),
        ]);

        $db->transComplete();

        return $db->transStatus();
    }
}
