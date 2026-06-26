<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        echo view('Layout/Header');
        echo view('Home');
        echo view('Layout/Footer');
    }
    public function Sample()
    {
        return view('SamplePage');
    }
}
