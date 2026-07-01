<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * UserModel — representasi tabel `user`.
 *
 * Skema tabel (lihat migration 2026-06-26-191114_CreateUser + 2026-07-01-000000_AlterUserNpm):
 *   id         INT UNSIGNED PK AUTO_INCREMENT
 *   npm        CHAR(9)        UNIQUE NOT NULL  — pengganti username/email
 *   password   CHAR(64)       SHA-256 hash
 *   role       ENUM('Admin','Penjual','Pembeli')
 *   createdby  INT UNSIGNED
 *   createdat  DATETIME
 *   updatedby  INT UNSIGNED
 *   updatedat  DATETIME
 *
 * Catatan: password disimpan sebagai hash('sha256', $plaintext) sesuai
 * kontrak existing project Kantin (lihat UserSeeder).
 */
class UserModel extends Model
{
    protected $table         = 'user';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    /** Field yang boleh diisi lewat insert/update mass-assignment. */
    protected $allowedFields = [
        'npm',
        'password',
        'role',
        'createdby',
        'createdat',
        'updatedby',
        'updatedat',
    ];

    // Default timestamps — kita pakai field `createdat` / `updatedat`
    // (camelCase, sesuai skema existing), bukan `created_at`/`updated_at`.
    protected $useTimestamps      = false;
    protected $dateFormat         = 'datetime';
    protected $createdField       = 'createdat';
    protected $updatedField       = 'updatedat';

    /**
     * Cari user berdasarkan NPM.
     *
     * @param string $npm 9-digit NPM
     * @return array|null
     */
    public function findByNpm(string $npm): ?array
    {
        return $this->where('npm', $npm)->first();
    }
}
