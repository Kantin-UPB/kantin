<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * ClientAuthFilter — proteksi halaman client side (mahasiswa).
 *
 * Cek session:
 *   - isLoggedIn = true
 *   - login_type = 'mahasiswa'
 *
 * Kalau gagal, redirect ke /mahasiswa/login dengan flash error.
 *
 * Berbeda dengan AuthFilter (backoffice) yang redirect ke /login.
 */
class ClientAuthFilter implements FilterInterface
{
    /**
     * Filter "before" - cek apakah user sudah login sebagai mahasiswa.
     * Jika belum, redirect ke /mahasiswa/login.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $isLoggedIn = session()->get('isLoggedIn');
        $loginType  = session()->get('login_type');

        if (! $isLoggedIn || $loginType !== 'mahasiswa') {
            return redirect()->to('/mahasiswa/login')->with('error', 'Silakan login terlebih dahulu sebagai mahasiswa.');
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
