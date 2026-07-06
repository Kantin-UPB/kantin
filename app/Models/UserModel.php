<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'user';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    // Field yang boleh di-insert / update via model
    // Catatan: kolom `username` untuk backoffice (bebas),
    //          kolom `npm` untuk mahasiswa / client side (9 digit angka).
    protected $allowedFields = [
        'username',
        'npm',
        'password',
        'role',
        'login_type',
        'createdby',
        'createdat',
        'updatedby',
        'updatedat',
    ];

    // Default timestamps nonaktif - tabel pakai field createdat/updatedat manual
    protected $useTimestamps = false;

    // Validation rules default (untuk backoffice - username bebas).
    // Untuk register mahasiswa (client side nanti), rule NPM 9 digit
    // akan didefinisikan di controller atau dipisah ke validation group.
    protected $validationRules = [
        'username'    => 'permit_empty|max_length[75]|is_unique[user.username,id,{id}]',
        'password'    => 'required|min_length[6]',
        'login_type'  => 'required|in_list[backoffice,mahasiswa]',
        'role'        => 'required|in_list[Admin,Penjual,Pembeli]',
    ];

    protected $validationMessages = [
        'username' => [
            'max_length' => 'Username maksimal 75 karakter.',
            'is_unique'  => 'Username sudah dipakai.',
        ],
        'password' => [
            'required'   => 'Password wajib diisi.',
            'min_length' => 'Password minimal 6 karakter.',
        ],
        'login_type' => [
            'required'  => 'Login type wajib diisi.',
            'in_list'   => 'Login type harus backoffice atau mahasiswa.',
        ],
        'role' => [
            'required' => 'Role wajib diisi.',
            'in_list'  => 'Role harus Admin, Penjual, atau Pembeli.',
        ],
    ];

    /**
     * Ambil user backoffice berdasarkan username.
     * Dipakai oleh halaman login backoffice.
     */
    public function findByUsername(string $username): ?array
    {
        $row = $this->where('username', $username)
                    ->where('login_type', 'backoffice')
                    ->first();

        return $row ?? null;
    }

    /**
     * Ambil user mahasiswa berdasarkan NPM (9 digit).
     * Akan dipakai oleh halaman login client side (nanti).
     */
    public function findByNpm(string $npm): ?array
    {
        $row = $this->where('npm', $npm)
                    ->where('login_type', 'mahasiswa')
                    ->first();

        return $row ?? null;
    }
}
