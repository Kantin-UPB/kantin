<?php

namespace App\Controllers;

class Kategori extends BaseController
{
    public function index()
    {
        // Dummy Data
        $data['kategori'] = [
            [
                'id_kategori'   => 1,
                'nama_kategori' => 'Elektronik',
            ],
            [
                'id_kategori'   => 2,
                'nama_kategori' => 'Makanan',
            ],
            [
                'id_kategori'   => 3,
                'nama_kategori' => 'Pakaian',
            ],
        ];

        return view('kategori/index', $data);
    }
}