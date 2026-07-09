<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Protected routes - butuh login backoffice
$routes->get('/', 'Home::index', ['filter' => 'auth']);
$routes->get('pesan', 'Pesan::index');
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
// Registrasi mahasiswa (client side) akan dikerjakan terpisah
// di routing /mahasiswa/* setelah backend backoffice selesai.

$routes->group('manage-poin', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ManagePoin::index');
    $routes->get('create', 'ManagePoin::create');
    $routes->post('store', 'ManagePoin::store');
    $routes->get('edit/(:num)', 'ManagePoin::edit/$1');
    $routes->post('update/(:num)', 'ManagePoin::update/$1');
    $routes->get('delete/(:num)', 'ManagePoin::delete/$1');
    $routes->get('riwayat', 'ManagePoin::riwayat');
});


