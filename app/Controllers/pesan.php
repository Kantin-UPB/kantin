<?php

namespace App\Controllers;

class Pesan extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Client Side - Halaman Pesan'
        ];
        
        // Memanggil view yang ada di folder app/Views/client/pesan.php
        return view('client/pesan', $data);
    }
}