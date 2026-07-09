<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Filter "before" - cek apakah user sudah login sebagai backoffice.
     * Mahasiswa (login_type=mahasiswa) akan di-redirect ke /mahasiswa/login.
     * Belum login sama sekali di-redirect ke /login (backoffice).
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $isLoggedIn = session()->get('isLoggedIn');
        $loginType  = session()->get('login_type');

        if (! $isLoggedIn) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Mahasiswa tidak boleh akses halaman backoffice
        if ($loginType === 'mahasiswa') {
            return redirect()->to('/')->with('error', 'Akses backoffice hanya untuk Admin/Penjual.');
        }
    }

    /**
     * Filter "after" - tidak ada operasi tambahan.
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
