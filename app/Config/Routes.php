<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Protected routes - butuh login backoffice
$routes->get('/', 'Home::index', ['filter' => 'auth']);
$routes->get('/sample', 'Home::Sample', ['filter' => 'auth']);

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


