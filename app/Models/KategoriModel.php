<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table            = 'kategori';
    protected $primaryKey       = 'id_kategori';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'nama_kategori',
    ];

    protected $validationRules = [
        'nama_kategori' => [
            'label'  => 'Nama Kategori',
            'rules'  => 'required|max_length[100]',
            'errors' => [
                'required'   => 'Nama kategori wajib diisi.',
                'max_length' => 'Nama kategori maksimal 100 karakter.',
            ],
        ],
    ];

    protected $validationMessages = [
        'nama_kategori' => [
            'required'   => 'Nama kategori wajib diisi.',
            'max_length' => 'Nama kategori maksimal 100 karakter.',
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

    public function getKategoriList(): array
    {
        return $this->orderBy('id_kategori', 'ASC')->findAll();
    }

    public function isCategoryInUse(int $id): bool
    {
        return $this->db->table('menu')
            ->where('id_kategori', $id)
            ->countAllResults() > 0;
    }
}
