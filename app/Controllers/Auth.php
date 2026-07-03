<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Auth — controller untuk Login, Register, dan Logout.
 *
 * Aturan main (sesuai permintaan tim):
 *   1. Field login & register pakai NPM (9 digit angka) + password.
 *      TIDAK pakai email.
 *   2. NPM wajib tepat 9 digit angka — gagal validasi kalau kurang/lebih
 *      atau mengandung karakter non-digit.
 *   3. Database tetap pakai tabel `user`, kolom identity-nya `npm`
 *      (CHAR 9, UNIQUE — lihat migration AlterUserNpm).
 *   4. Password di-hash memakai SHA-256 (sama dengan kontrak existing
 *      project Kantin — lihat UserSeeder).
 *   5. User yang register baru otomatis dapat role `Pembeli`.
 *
 * Controller ini TIDAK menyentuh template Kantin (Header/Menu/Footer/Home).
 * Halaman login & register pakai view standalone di app/Views/Auth/.
 */
class Auth extends BaseController
{
    /** Minimum & maksimum panjang password (bebas, tidak dipaksakan tim). */
    private const PASSWORD_MIN_LENGTH = 8;

    /** Helper yang dipakai di view login/register (form, url). */
    protected $helpers = ['form', 'url'];

    private UserModel $userModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->userModel = new UserModel();
    }

    /**
     * GET  /login  -> tampilkan form login.
     * POST /login  -> proses login.
     */
    public function login()
    {
        // Kalau sudah login, langsung lempar ke dashboard.
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        $data = [
            'title'             => 'Login — Kantin UPB',
            'passwordMinLength' => self::PASSWORD_MIN_LENGTH,
        ];

        if ($this->request->getMethod() === 'POST') {
            return $this->processLogin($data);
        }

        return view('Auth/Login', $data);
    }

    /**
     * GET  /register  -> tampilkan form registrasi.
     * POST /register  -> proses registrasi.
     */
    public function register()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        $data = [
            'title'             => 'Daftar Akun — Kantin UPB',
            'passwordMinLength' => self::PASSWORD_MIN_LENGTH,
        ];

        if ($this->request->getMethod() === 'POST') {
            return $this->processRegister($data);
        }

        return view('Auth/Register', $data);
    }

    /**
     * GET /logout -> hapus session & lempar ke /login.
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    /* =====================================================================
     *  Private — proses form submit
     * ===================================================================== */

    private function processLogin(array $data)
    {
        $npm      = (string) $this->request->getPost('npm');
        $password = (string) $this->request->getPost('password');

        // Validasi server-side: NPM wajib 9 digit angka.
        if (! preg_match('/^[0-9]{9}$/', $npm)) {
            return redirect()->to('/login')->withInput()->with('error', 'NPM harus tepat 9 digit angka.');
        }

        if ($password === '') {
            return redirect()->to('/login')->withInput()->with('error', 'Password wajib diisi.');
        }

        $user = $this->userModel->findByNpm($npm);

        // Cegah timing attack tetap aman: hash input tetap dihitung walau
        // user tidak ditemukan, supaya response time tidak membocorkan info.
        $hashedInput = hash('sha256', $password);
        if ($user === null || ! hash_equals($user['password'], $hashedInput)) {
            return redirect()->to('/login')->withInput()->with('error', 'NPM atau password salah.');
        }

        // Sukses login — set session.
        $session = session();
        $session->set([
            'isLoggedIn' => true,
            'userId'      => $user['id'],
            'npm'         => $user['npm'],
            'role'        => $user['role'],
        ]);

        return redirect()->to('/')->with('success', 'Login berhasil. Selamat datang!');
    }

    private function processRegister(array $data)
    {
        $npm            = (string) $this->request->getPost('npm');
        $password       = (string) $this->request->getPost('password');
        $passwordConfirm = (string) $this->request->getPost('password_confirm');

        // --- Validasi NPM: 9 digit angka, tidak boleh kurang/lebih ---
        if (! preg_match('/^[0-9]{9}$/', $npm)) {
            return redirect()->to('/register')->withInput()->with('error', 'NPM harus tepat 9 digit angka (0-9).');
        }

        // --- Validasi password ---
        if (strlen($password) < self::PASSWORD_MIN_LENGTH) {
            return redirect()->to('/register')->withInput()->with('error', 'Password minimal ' . self::PASSWORD_MIN_LENGTH . ' karakter.');
        }

        if ($password !== $passwordConfirm) {
            return redirect()->to('/register')->withInput()->with('error', 'Konfirmasi password tidak cocok.');
        }

        // --- Cek NPM unik ---
        if ($this->userModel->findByNpm($npm) !== null) {
            return redirect()->to('/register')->withInput()->with('error', 'NPM sudah terdaftar. Silakan login atau gunakan NPM lain.');
        }

        // --- Insert user baru, role default Pembeli ---
        $now = date('Y-m-d H:i:s');
        $this->userModel->insert([
            'npm'        => $npm,
            'password'   => hash('sha256', $password),
            'role'       => 'Pembeli',
            'createdby'  => 0,
            'createdat'  => $now,
            'updatedby'  => 0,
            'updatedat'  => $now,
        ]);

        return redirect()->to('/login')->with('success', 'Registrasi berhasil. Silakan login dengan NPM & password Anda.');
    }
}
