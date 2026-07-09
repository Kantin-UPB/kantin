<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * ClientAuth Controller - Client Side Login (Mahasiswa)
 *
 * Mengelola autentikasi untuk sisi client (mahasiswa) Kantin UPB.
 *
 * Berbeda dengan Auth (backoffice yang login pakai username bebas),
 * ClientAuth login mahasiswa pakai NPM (9 digit angka) + password.
 *
 * Scope:
 *   - Login mahasiswa menggunakan NPM + password
 *   - Register mahasiswa (role otomatis Pembeli, login_type mahasiswa)
 *   - Logout mahasiswa
 *
 * Password di-hash SHA-256 (kontrak existing project Kantin, sama
 * dengan UserSeeder dan Auth backoffice).
 *
 * Routing:
 *   GET  /mahasiswa/login    -> ClientAuth::login
 *   POST /mahasiswa/login    -> ClientAuth::loginProcess
 *   GET  /mahasiswa/register -> ClientAuth::register
 *   POST /mahasiswa/register -> ClientAuth::registerProcess
 *   GET  /mahasiswa/logout   -> ClientAuth::logout
 */
class ClientAuth extends BaseController
{
    /**
     * Tampilkan halaman login client (mahasiswa).
     * Field: NPM (9 digit) + password.
     */
    public function login()
    {
        // Jika sudah login, arahkan ke halaman pemesanan client
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/pesan');
        }

        $data = [
            'title'      => 'Login Mahasiswa',
            'validation' => \Config\Services::validation(),
        ];

        return view('client/login', $data);
    }

    /**
     * Proses form login client (mahasiswa).
     */
    public function loginProcess(): RedirectResponse
    {
        $rules = [
            'npm' => [
                'label'  => 'NPM',
                'rules'  => 'required|regex_match[/^[0-9]{9}$/]',
                'errors' => [
                    'required'    => 'NPM wajib diisi.',
                    'regex_match' => 'NPM harus tepat 9 digit angka.',
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

        $npm      = (string) $this->request->getPost('npm');
        $password = (string) $this->request->getPost('password');

        $userModel = new UserModel();
        $user      = $userModel->findByNpm($npm);

        // Cegah user enumeration: pesan error sama untuk "user tidak ada" dan "password salah"
        if (! $user) {
            return redirect()->back()->withInput()->with('error', 'NPM atau password salah.');
        }

        // Password di-hash dengan SHA-256 (sesuai kontrak existing project Kantin)
        $hashedInput = hash('sha256', $password);

        if (! hash_equals($user['password'], $hashedInput)) {
            return redirect()->back()->withInput()->with('error', 'NPM atau password salah.');
        }

        // Set session
        $session = session();
        $session->set([
            'id'         => $user['id'],
            'npm'        => $user['npm'],
            'role'       => $user['role'],
            'login_type' => $user['login_type'] ?? 'mahasiswa',
            'isLoggedIn' => true,
        ]);

        // Log aktivitas login ke tabel logsystem (best-effort, jangan block login jika gagal)
        try {
            $db = \Config\Database::connect();
            $db->table('logsystem')->insert([
                'iduser'     => $user['id'],
                'module'     => 'ClientAuth',
                'level'      => $user['role'],
                'aksi'       => 'login',
                'deskripsi'  => sprintf("Mahasiswa NPM %s berhasil login ke client side", $user['npm']),
                'createdby'  => $user['id'],
                'createdat'  => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Gagal menyimpan logsystem: ' . $e->getMessage());
        }

        return redirect()->to('/pesan')->with('success', 'Selamat datang, NPM ' . $user['npm'] . '!');
    }

    /**
     * Tampilkan halaman register client (mahasiswa).
     */
    public function register()
    {
        // Jika sudah login, arahkan ke halaman pemesanan client
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/pesan');
        }

        $data = [
            'title'             => 'Daftar Akun Mahasiswa',
            'passwordMinLength' => 8,
            'validation'        => \Config\Services::validation(),
        ];

        return view('client/register', $data);
    }

    /**
     * Proses form register client (mahasiswa).
     */
    public function registerProcess(): RedirectResponse
    {
        $rules = [
            'npm' => [
                'label'  => 'NPM',
                'rules'  => 'required|regex_match[/^[0-9]{9}$/]|is_unique[user.npm]',
                'errors' => [
                    'required'    => 'NPM wajib diisi.',
                    'regex_match' => 'NPM harus tepat 9 digit angka (0-9).',
                    'is_unique'   => 'NPM sudah terdaftar. Silakan login atau gunakan NPM lain.',
                ],
            ],
            'password' => [
                'label'  => 'Password',
                'rules'  => 'required|min_length[8]',
                'errors' => [
                    'required'   => 'Password wajib diisi.',
                    'min_length' => 'Password minimal 8 karakter.',
                ],
            ],
            'password_confirm' => [
                'label'  => 'Konfirmasi Password',
                'rules'  => 'required|matches[password]',
                'errors' => [
                    'required' => 'Konfirmasi password wajib diisi.',
                    'matches'  => 'Konfirmasi password tidak cocok.',
                ],
            ],
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $npm      = (string) $this->request->getPost('npm');
        $password = (string) $this->request->getPost('password');

        $hashedPassword = hash('sha256', $password);
        $currentDateTime = date('Y-m-d H:i:s');

        $userModel = new UserModel();
        $userId = $userModel->insert([
            'username'    => '',          // backoffice field, kosong untuk mahasiswa
            'npm'         => $npm,
            'password'    => $hashedPassword,
            'role'        => 'Pembeli',
            'login_type'  => 'mahasiswa',
            'createdby'   => 0,
            'createdat'   => $currentDateTime,
            'updatedby'   => 0,
            'updatedat'   => $currentDateTime,
        ]);

        // Log aktivitas register (best-effort)
        try {
            $db = \Config\Database::connect();
            $db->table('logsystem')->insert([
                'iduser'     => $userId,
                'module'     => 'ClientAuth',
                'level'      => 'Pembeli',
                'aksi'       => 'register',
                'deskripsi'  => sprintf("Mahasiswa NPM %s mendaftar akun baru", $npm),
                'createdby'  => $userId,
                'createdat'  => $currentDateTime,
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Gagal menyimpan logsystem: ' . $e->getMessage());
        }

        return redirect()->to('/mahasiswa/login')->with('success', 'Registrasi berhasil. Silakan login dengan NPM & password Anda.');
    }

    /**
     * Logout client (mahasiswa).
     */
    public function logout(): RedirectResponse
    {
        $userId = session()->get('id');
        $npm    = session()->get('npm');

        // Log aktivitas logout (best-effort)
        if ($userId) {
            try {
                $db = \Config\Database::connect();
                $db->table('logsystem')->insert([
                    'iduser'     => $userId,
                    'module'     => 'ClientAuth',
                    'level'      => session()->get('role') ?? '',
                    'aksi'       => 'logout',
                    'deskripsi'  => sprintf("Mahasiswa NPM %s logout dari client side", $npm ?? ''),
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
        session()->remove('npm');
        session()->remove('role');
        session()->remove('login_type');
        session()->remove('isLoggedIn');
        session()->regenerate(true);

        return redirect()->to('/mahasiswa/login')->with('success', 'Anda telah berhasil logout.');
    }
}
