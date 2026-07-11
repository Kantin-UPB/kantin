<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuModel extends Model
{
    protected $table            = 'menu';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'id_kategori',
        'nama',
        'deskripsi',
        'harga',
        'diskon',
        'status_id',
        'url_gambar',
        'created_by',
        'created_at',
    ];

    protected $validationRules = [
        'id_kategori' => [
            'label'  => 'Kategori',
            'rules'  => 'required|integer',
            'errors' => [
                'required' => 'Kategori wajib dipilih.',
                'integer'  => 'Kategori harus berupa angka.',
            ],
        ],
        'nama' => [
            'label'  => 'Nama Menu',
            'rules'  => 'required|max_length[100]',
            'errors' => [
                'required'   => 'Nama menu wajib diisi.',
                'max_length' => 'Nama menu maksimal 100 karakter.',
            ],
        ],
        'deskripsi' => [
            'label'  => 'Deskripsi',
            'rules'  => 'required|max_length[255]',
            'errors' => [
                'required'   => 'Deskripsi wajib diisi.',
                'max_length' => 'Deskripsi maksimal 255 karakter.',
            ],
        ],
        'harga' => [
            'label'  => 'Harga',
            'rules'  => 'required|numeric|greater_than_equal_to[0]',
            'errors' => [
                'required'             => 'Harga wajib diisi.',
                'numeric'              => 'Harga harus berupa angka.',
                'greater_than_equal_to'=> 'Harga tidak boleh negatif.',
            ],
        ],
        'diskon' => [
            'label'  => 'Diskon',
            'rules'  => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
            'errors' => [
                'integer'               => 'Diskon harus berupa angka.',
                'greater_than_equal_to' => 'Diskon tidak boleh negatif.',
                'less_than_equal_to'    => 'Diskon maksimal 100%.',
            ],
        ],
        'url_gambar' => [
            'label'  => 'Gambar',
            'rules'  => 'permit_empty|max_length[255]',
            'errors' => [
                'max_length' => 'URL gambar maksimal 255 karakter.',
            ],
        ],
    ];

    protected $validationMessages = [
        'id_kategori' => [
            'required' => 'Kategori wajib dipilih.',
            'integer'  => 'Kategori harus berupa angka.',
        ],
        'nama' => [
            'required'   => 'Nama menu wajib diisi.',
            'max_length' => 'Nama menu maksimal 100 karakter.',
        ],
        'deskripsi' => [
            'required'   => 'Deskripsi wajib diisi.',
            'max_length' => 'Deskripsi maksimal 255 karakter.',
        ],
        'harga' => [
            'required' => 'Harga wajib diisi.',
            'numeric'  => 'Harga harus berupa angka.',
            'greater_than_equal_to' => 'Harga tidak boleh negatif.',
        ],
        'diskon' => [
            'integer'               => 'Diskon harus berupa angka.',
            'greater_than_equal_to' => 'Diskon tidak boleh negatif.',
            'less_than_equal_to'    => 'Diskon maksimal 100%.',
        ],
        'url_gambar' => [
            'max_length' => 'URL gambar maksimal 255 karakter.',
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
            1  => 'Pending',
            5  => 'Active',
            8  => 'Cancel',
        ];
    }

    public function getCategories(): array
    {
        return $this->db->table('kategori')
            ->select('id_kategori, nama_kategori')
            ->orderBy('nama_kategori', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getActiveMenuList(): array
    {
        return $this->select('menu.*, kategori.nama_kategori')
            ->join('kategori', 'kategori.id_kategori = menu.id_kategori', 'left')
            ->where('menu.status_id', 5)
            ->orderBy('menu.id', 'DESC')
            ->findAll();
    }

    public function getPendingMenuList(): array
    {
        return $this->select('menu.*, kategori.nama_kategori')
            ->join('kategori', 'kategori.id_kategori = menu.id_kategori', 'left')
            ->where('menu.status_id', 1)
            ->orderBy('menu.id', 'DESC')
            ->findAll();
    }

    public function getMenuList(): array
    {
        return $this->getActiveMenuList();
    }

    public function getCancelledMenuList(): array
    {
        return $this->select('menu.*, kategori.nama_kategori')
            ->join('kategori', 'kategori.id_kategori = menu.id_kategori', 'left')
            ->where('menu.status_id', 8)
            ->orderBy('menu.id', 'DESC')
            ->findAll();
    }

    public function getDiscountedMenuList(): array
    {
        return $this->select('menu.*, kategori.nama_kategori')
            ->join('kategori', 'kategori.id_kategori = menu.id_kategori', 'left')
            ->where('menu.status_id', 5)
            ->where('menu.diskon >', 0)
            ->orderBy('menu.diskon', 'DESC')
            ->findAll();
    }

    public function getMenuById(int $id): ?array
    {
        return $this->select('menu.*, kategori.nama_kategori, user.username as created_username')
            ->join('kategori', 'kategori.id_kategori = menu.id_kategori', 'left')
            ->join('user', 'user.id = menu.created_by', 'left')
            ->where('menu.id', $id)
            ->first();
    }

}
