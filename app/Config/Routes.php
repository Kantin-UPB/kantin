<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Halaman publik (auth) — siapa saja boleh akses.
$routes->get('/login',    'Auth::login');
$routes->post('/login',   'Auth::login');
$routes->get('/register', 'Auth::register');
$routes->post('/register','Auth::register');
$routes->get('/logout',   'Auth::logout');

// Halaman yang diproteksi login — pakai filter 'auth' (lihat Config\Filters).
// Filter hanya mengarahkan user belum-login ke /login. TIDAK mengubah
// template Kantin (Header/Menu/Footer/Home) yang sudah ada.
$routes->get('/', 'Home::index', ['filter' => 'auth']);

$routes->get('/sample', 'Home::Sample');
