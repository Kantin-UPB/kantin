<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
    // 1. Panggil dulu file helpernya agar fungsi writelog() dikenali
        helper('Func_helper');
        echo view('Layout/Header');
        echo view('Layout/Menu');
        echo view('Home');
        echo view('Layout/Footer');
    }

    public function Sample()
    {
        return view('SamplePage');
    }
}