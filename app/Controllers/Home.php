<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $data = [
            'title'    => 'Dashboard',
            'username' => session()->get('username'),
            'role'     => session()->get('role'),
        ];

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
