<?php

namespace App\Models;

use CodeIgniter\Model;

class PaketBundlingModel extends Model
{
    protected $table            = 'paket_bundling';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'nama_paket',
        'deskripsi',
        'harga_paket',
        'status_id',
        'url_gambar',
        'created_by',
        'created_at',
    ];

    protected $validationRules = [
        'nama_paket' => [
            'label'  => 'Nama Paket',
            'rules'  => 'required|max_length[150]',
            'errors' => [
                'required'   => 'Nama paket wajib diisi.',
                'max_length' => 'Nama paket maksimal 150 karakter.',
            ],
        ],
        'deskripsi' => [
            'label'  => 'Deskripsi',
            'rules'  => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'Deskripsi maksimal 500 karakter.',
            ],
        ],
        'harga_paket' => [
            'label'  => 'Harga Paket',
            'rules'  => 'required|numeric|greater_than_equal_to[0]',
            'errors' => [
                'required'              => 'Harga paket wajib diisi.',
                'numeric'               => 'Harga paket harus berupa angka.',
                'greater_than_equal_to' => 'Harga paket tidak boleh negatif.',
            ],
        ],
    ];

    protected $validationMessages = [
        'nama_paket' => [
            'required'   => 'Nama paket wajib diisi.',
            'max_length' => 'Nama paket maksimal 150 karakter.',
        ],
        'deskripsi' => [
            'max_length' => 'Deskripsi maksimal 500 karakter.',
        ],
        'harga_paket' => [
            'required'              => 'Harga paket wajib diisi.',
            'numeric'               => 'Harga paket harus berupa angka.',
            'greater_than_equal_to' => 'Harga paket tidak boleh negatif.',
        ],
    ];

    public function getValidationRules(array $options = []): array
    {
        return $this->validationRules;
    }

    public function getValidationMessages(): array
    {
        return $this->validationMessages;
    }

    public function getStatusOptions(): array
    {
        return [
            1 => 'Pending',
            5 => 'Active',
            8 => 'Cancel',
        ];
    }

    /**
     * Daftar menu aktif yang boleh dijadikan isi paket bundling.
     */
    public function getAvailableMenus(): array
    {
        return $this->db->table('menu')
            ->select('menu.id, menu.nama, menu.harga, menu.diskon, kategori.nama_kategori')
            ->join('kategori', 'kategori.id_kategori = menu.id_kategori', 'left')
            ->where('menu.status_id', 5)
            ->orderBy('menu.nama', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function withComputedTotals(array $paket): array
    {
        $items = $this->getItemsByPaketId((int) $paket['id']);

        $hargaNormal = 0;
        foreach ($items as $item) {
            $hargaNormal += ((float) $item['harga']) * ((int) $item['qty']);
        }

        $hargaPaket  = (float) $paket['harga_paket'];
        $hemat       = max(0, $hargaNormal - $hargaPaket);
        $persenHemat = $hargaNormal > 0 ? (int) round(($hemat / $hargaNormal) * 100) : 0;

        $paket['items']        = $items;
        $paket['harga_normal'] = $hargaNormal;
        $paket['hemat']        = $hemat;
        $paket['persen_hemat'] = $persenHemat;

        return $paket;
    }

    private function listWithTotals(int $statusId): array
    {
        $result = $this->where('status_id', $statusId)
            ->orderBy('id', 'DESC')
            ->findAll();

        return array_map(fn ($paket) => $this->withComputedTotals($paket), $result);
    }

    public function getActivePaketList(): array
    {
        return $this->listWithTotals(5);
    }

    public function getPendingPaketList(): array
    {
        return $this->listWithTotals(1);
    }

    public function getCancelledPaketList(): array
    {
        return $this->listWithTotals(8);
    }

    public function getPaketById(int $id): ?array
    {
        $paket = $this->find($id);

        if (! $paket) {
            return null;
        }

        return $this->withComputedTotals($paket);
    }

    public function getItemsByPaketId(int $idPaket): array
    {
        return $this->db->table('paket_bundling_item')
            ->select('paket_bundling_item.id, paket_bundling_item.id_menu, paket_bundling_item.qty, menu.nama, menu.harga, menu.url_gambar')
            ->join('menu', 'menu.id = paket_bundling_item.id_menu', 'left')
            ->where('paket_bundling_item.id_paket', $idPaket)
            ->get()
            ->getResultArray();
    }

    public function saveItems(int $idPaket, array $menuIds, array $qtys): void
    {
        $this->db->table('paket_bundling_item')->where('id_paket', $idPaket)->delete();

        $rows = [];
        foreach ($menuIds as $index => $idMenu) {
            $idMenu = (int) $idMenu;
            $qty    = max(1, (int) ($qtys[$index] ?? 1));

            if ($idMenu <= 0) {
                continue;
            }

            $rows[] = [
                'id_paket' => $idPaket,
                'id_menu'  => $idMenu,
                'qty'      => $qty,
            ];
        }

        if (! empty($rows)) {
            $this->db->table('paket_bundling_item')->insertBatch($rows);
        }
    }

    public function deleteItems(int $idPaket): void
    {
        $this->db->table('paket_bundling_item')->where('id_paket', $idPaket)->delete();
    }
}
