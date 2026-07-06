<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
<<<<<<< HEAD
    // 1. Panggil dulu file helpernya agar fungsi writelog() dikenali
        helper('Func_helper');
=======
        $data = [
            'title'    => 'Dashboard',
            'username' => session()->get('username'),
            'role'     => session()->get('role'),
        ];

>>>>>>> 8349947d78278f0b05961c3605a6e648366427f6
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