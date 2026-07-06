<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // 1. Panggil dulu file helpernya agar fungsi writelog() dikenali
        helper('Func_helper');

        // 2. Data session login dari update tim
        $data = [
            'title'    => 'Dashboard',
            'username' => session()->get('username'),
            'role'     => session()->get('role'),
        ];

        // 3. Tampilkan halaman utama dengan membawa data login
        echo view('Layout/Header');
        echo view('Layout/Menu');
        echo view('Home', $data);
        echo view('Layout/Footer');
    }

    public function Sample()
    {
        return view('SamplePage');
    }
}