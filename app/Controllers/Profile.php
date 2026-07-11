<?php

namespace App\Controllers;

use App\Models\UserModel;

class Profile extends BaseController
{
    public function index()
    {
        // 1. Validasi session: Pastikan pembeli sudah login
        if (!session()->get('isLoggedIn') || session()->get('login_type') !== 'mahasiswa') {
            return redirect()->to('/mahasiswa/login');
        }

        $userModel = new UserModel();
        
        // 2. Ambil ID user dari session aktif saat ini[cite: 1]
        $userId = session()->get('id');
        
        // 3. Tarik data lengkap dari database berdasarkan ID[cite: 1]
        $data['user'] = $userModel->find($userId);

        // 4. Lempar data user ke halaman view profile
        return view('client/profile', $data);
    }
}