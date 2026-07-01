<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AuthFilter — cek session `isLoggedIn`.
 *
 * Cara pakai (didaftarkan di Config\Filters::$aliases lalu dipasang
 * ke rute tertentu, mis. rute '/' yang menampilkan dashboard):
 *
 *   $routes->get('/', 'Home::index', ['filter' => 'auth']);
 *
 * Filter ini hanya memblokir rute yang dipasangi filter — tidak
 * mengubah template Kantin yang sudah ada (Header/Menu/Footer).
 */
class AuthFilter implements FilterInterface
{
    /**
     * Sebelum request dijalankan: kalau belum login, redirect ke /login.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
