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
