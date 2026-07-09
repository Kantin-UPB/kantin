<?php

// Lokasi file ini di project: app/Database/Migrations/2026-07-05-000000_CreatePoinTables.php
// CATATAN: Cek dulu ke Jasmen apakah tabel `user` sudah punya kolom saldo_poin.
// Kalau belum, migration ini akan menambahkannya.

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePoinTables extends Migration
{
    public function up()
    {
        // Tabel pengaturan aturan poin (diatur admin lewat form)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_aturan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'rasio_rupiah' => [
                'type'       => 'INT',
                'constraint' => 11,
                'comment'    => 'Nominal rupiah per 1 poin, misal 1000',
            ],
            'minimal_transaksi' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => 'Transaksi minimal (Rp) supaya dapat poin',
            ],
            'status_aktif' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'nonaktif'],
                'default'    => 'aktif',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pengaturan_poin');

        // Tabel riwayat poin per user (log setiap penambahan/pengurangan)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'transaksi_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Relasi ke tabel transaksi, null kalau adjustment manual',
            ],
            'jenis' => [
                'type'       => 'ENUM',
                'constraint' => ['masuk', 'keluar'],
            ],
            'jumlah_poin' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->createTable('riwayat_poin');

        // Tambah kolom saldo_poin ke tabel user (SESUAIKAN NAMA KOLOM PK/tabel user
        // dengan struktur asli project setelah migration login beres —
        // cek dulu ke Jasmen sebelum menjalankan bagian ini)
        $fields = [
            'saldo_poin' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'after'      => 'role',
            ],
        ];
        $this->forge->addColumn('user', $fields);
    }

    public function down()
    {
        $this->forge->dropTable('riwayat_poin');
        $this->forge->dropTable('pengaturan_poin');
        $this->forge->dropColumn('user', 'saldo_poin');
    }
}
