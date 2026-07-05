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
        return (bool) $this->insert([
            'user_id'      => $userId,
            'transaksi_id' => $transaksiId,
            'jenis'        => 'masuk',
            'jumlah_poin'  => $jumlah,
            'keterangan'   => $keterangan ?: 'Poin dari transaksi',
        ]);
    }

    /**
     * Catat pemakaian/pengurangan poin (dipanggil saat pembeli pakai poin untuk potong tagihan,
     * atau saat rollback karena transaksi dibatalkan / status Cancel).
     */
    public function kurangiPoin(int $userId, int $jumlah, ?int $transaksiId = null, string $keterangan = ''): bool
    {
        return (bool) $this->insert([
            'user_id'      => $userId,
            'transaksi_id' => $transaksiId,
            'jenis'        => 'keluar',
            'jumlah_poin'  => $jumlah,
            'keterangan'   => $keterangan ?: 'Poin dipakai saat transaksi',
        ]);
    }
}
