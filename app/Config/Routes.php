<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Default route -> client side (katalog menu mahasiswa).
// Filter clientauth: wajib login sebagai mahasiswa, kalau belum
// redirect ke /mahasiswa/login. Backoffice user (login_type=backoffice)
// juga akan di-redirect ke /mahasiswa/login kalau coba akses /.
$routes->get('/', 'Pesan::index', ['filter' => 'clientauth']);
$routes->get('pesan', 'Pesan::index', ['filter' => 'clientauth']);

// Backoffice dashboard -- akses via /admin. Filter auth akan redirect
// ke /login kalau belum login backoffice.
$routes->get('/admin', 'Home::index', ['filter' => 'auth']);
$routes->get('/sample', 'Home::Sample', ['filter' => 'auth']);
$routes->get('/kategori', 'Kategori::index', ['filter' => 'auth']);
$routes->get('/kategori/create', 'Kategori::create', ['filter' => 'auth']);
$routes->post('/kategori/store', 'Kategori::store', ['filter' => 'auth']);
$routes->get('/kategori/edit/(:num)', 'Kategori::edit/$1', ['filter' => 'auth']);
$routes->post('/kategori/update/(:num)', 'Kategori::update/$1', ['filter' => 'auth']);
$routes->get('/kategori/delete/(:num)', 'Kategori::delete/$1', ['filter' => 'auth']);
$routes->get('/menu', 'Menu::index', ['filter' => 'auth']);
$routes->get('/menu/pending', 'Menu::pending', ['filter' => 'auth']);
$routes->get('/menu/cancelled', 'Menu::cancelled', ['filter' => 'auth']);
$routes->get('/menu/create', 'Menu::create', ['filter' => 'auth']);
$routes->post('/menu/store', 'Menu::store', ['filter' => 'auth']);
$routes->get('/menu/(:num)', 'Menu::show/$1', ['filter' => 'auth']);
$routes->get('/menu/edit/(:num)', 'Menu::edit/$1', ['filter' => 'auth']);
$routes->post('/menu/update/(:num)', 'Menu::update/$1', ['filter' => 'auth']);
$routes->get('/menu/activate/(:num)', 'Menu::activate/$1', ['filter' => 'auth']);
$routes->get('/menu/cancel/(:num)', 'Menu::cancel/$1', ['filter' => 'auth']);
$routes->get('/menu/delete/(:num)', 'Menu::delete/$1', ['filter' => 'auth']);
$routes->get('/menu/draft/(:num)', 'Menu::draft/$1', ['filter' => 'auth']);
$routes->get('/menu/restore/(:num)', 'Menu::restore/$1', ['filter' => 'auth']);

$routes->get('/meja', 'Meja::index', ['filter' => 'auth']);
$routes->post('/meja/update', 'Meja::updateStatus', ['filter' => 'auth']);
// Auth routes (public - tidak butuh login)
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::loginProcess');
$routes->get('/logout', 'Auth::logout');

// Catatan: Tidak ada route /register untuk backoffice.

// Client-side auth routes (mahasiswa) - public, tidak butuh login.
// Login & register mahasiswa pakai NPM (9 digit angka) + password.
// Filter 'auth' TIDAK dipasang di sini (halaman login/register harus
// bisa diakses tanpa login). Proteksi halaman client (mis. /pesan)
// ditangani terpisah kalau diperlukan.
$routes->get('/mahasiswa/login', 'ClientAuth::login');
$routes->post('/mahasiswa/login', 'ClientAuth::loginProcess');
$routes->get('/mahasiswa/register', 'ClientAuth::register');
$routes->post('/mahasiswa/register', 'ClientAuth::registerProcess');
$routes->get('/mahasiswa/logout', 'ClientAuth::logout');

$routes->group('manage-poin', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ManagePoin::index');
    $routes->get('create', 'ManagePoin::create');
    $routes->post('store', 'ManagePoin::store');
    $routes->get('edit/(:num)', 'ManagePoin::edit/$1');
    $routes->post('update/(:num)', 'ManagePoin::update/$1');
    $routes->get('delete/(:num)', 'ManagePoin::delete/$1');
    $routes->get('riwayat', 'ManagePoin::riwayat');
});


