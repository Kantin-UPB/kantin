<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Auth Controller - Backoffice Login
 *
 * Mengelola proses autentikasi untuk backoffice Kantin UPB.
 *
 * Scope saat ini (sesuai clarification):
 *   - Login backoffice menggunakan USERNAME (bebas, tanpa validasi NPM)
 *   - TIDAK ada fitur register untuk backoffice
 *   - Logout
 *
 * Rencana client side (mahasiswa, login NPM + register) akan
 * dikerjakan terpisah setelah backend backoffice selesai.
 */
class Auth extends BaseController
{
    /**
     * Tampilkan halaman login backoffice.
     * Field: username (bebas) + password.
     */
    public function login()
    {
        // Jika sudah login, arahkan ke dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        $data = [
            'title'      => 'Login Backoffice',
            'validation' => \Config\Services::validation(),
        ];

        return view('Auth/Login', $data);
    }

    /**
     * Proses form login backoffice.
     */
    public function loginProcess(): RedirectResponse
    {
        $rules = [
            'username' => [
                'label'  => 'Username',
                'rules'  => 'required|max_length[75]',
                'errors' => [
                    'required'   => 'Username wajib diisi.',
                    'max_length' => 'Username maksimal 75 karakter.',
                ],
            ],
            'password' => [
                'label'  => 'Password',
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Password wajib diisi.',
                ],
            ],
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = (string) $this->request->getPost('username');
        $password = (string) $this->request->getPost('password');

        $userModel = new UserModel();
        $user      = $userModel->findByUsername($username);

        // Cegah user enumeration: pesan error sama untuk "user tidak ada" dan "password salah"
        if (! $user) {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
        }

        // Password di-hash dengan SHA-256 (sesuai UserSeeder existing)
        $hashedInput = hash('sha256', $password);

        if (! hash_equals($user['password'], $hashedInput)) {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
        }

        // Set session
        $session = session();
        $session->set([
            'id'         => $user['id'],
            'username'   => $user['username'],
            'role'       => $user['role'],
            'login_type' => $user['login_type'] ?? 'backoffice',
            'isLoggedIn' => true,
        ]);

        // Log aktivitas login ke tabel logsystem (best-effort, jangan block login jika gagal)
        try {
            $db = \Config\Database::connect();
            $db->table('logsystem')->insert([
                'iduser'     => $user['id'],
                'module'     => 'Auth',
                'level'      => $user['role'],
                'aksi'       => 'login',
                'deskripsi'  => sprintf("User %s berhasil login ke backoffice", $user['username']),
                'createdby'  => $user['id'],
                'createdat'  => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Gagal menyimpan logsystem: ' . $e->getMessage());
        }

        return redirect()->to('/')->with('success', 'Selamat datang, ' . $user['username'] . '!');
    }

    /**
     * Logout user.
     */
    public function logout(): RedirectResponse
    {
        $userId   = session()->get('id');
        $username = session()->get('username');

        // Log aktivitas logout (best-effort)
        if ($userId) {
            try {
                $db = \Config\Database::connect();
                $db->table('logsystem')->insert([
                    'iduser'     => $userId,
                    'module'     => 'Auth',
                    'level'      => session()->get('role') ?? '',
                    'aksi'       => 'logout',
                    'deskripsi'  => sprintf("User %s logout dari backoffice", $username ?? ''),
                    'createdby'  => $userId,
                    'createdat'  => date('Y-m-d H:i:s'),
                ]);
            } catch (\Throwable $e) {
                log_message('warning', 'Gagal menyimpan logsystem: ' . $e->getMessage());
            }
        }

        // Catatan: jangan pakai session()->destroy() karena flashdata
        // yang di-set di redirect()->with() akan hilang juga. Cukup
        // hapus key-key login supaya isLoggedIn=false, lalu regenerate
        // session ID untuk keamanan (session fixation protection).
        session()->remove('id');
        session()->remove('username');
        session()->remove('role');
        session()->remove('login_type');
        session()->remove('isLoggedIn');
        session()->regenerate(true);

        return redirect()->to('/login')->with('success', 'Anda telah berhasil logout.');
    }
}
