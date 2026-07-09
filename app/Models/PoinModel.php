<?php

// Lokasi file ini di project: app/Models/PoinModel.php

namespace App\Models;

use CodeIgniter\Model;

class PoinModel extends Model
{
    protected $table            = 'pengaturan_poin';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'nama_aturan',
        'rasio_rupiah',
        'minimal_transaksi',
        'status_aktif',
    ];

    /**
     * Ambil aturan poin yang sedang aktif (dipakai saat hitung poin transaksi).
     */
    public function getAturanAktif(): ?array
    {
        return $this->where('status_aktif', 'aktif')
                    ->orderBy('id', 'DESC')
                    ->first();
    }

    /**
     * Hitung berapa poin yang didapat dari nominal transaksi tertentu,
     * berdasarkan aturan yang sedang aktif.
     */
    public function hitungPoin(int $nominalTransaksi): int
    {
        $aturan = $this->getAturanAktif();

        if (! $aturan) {
            return 0;
        }

        if ($nominalTransaksi < (int) $aturan['minimal_transaksi']) {
            return 0;
        }

        $rasio = (int) $aturan['rasio_rupiah'];
        if ($rasio <= 0) {
            return 0;
        }

        return intdiv($nominalTransaksi, $rasio);
    }
}
