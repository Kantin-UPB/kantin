<?php

namespace App\Controllers;

class Kitchen extends BaseController
{
    public function index()
    {
        if (session()->get('role') !== 'Admin') {
            return redirect()->to('/')->with('error', 'Akses Kitchen Display hanya untuk Admin.');
        }

        $now = time();

        $orders = [
            ['id' => 1, 'meja' => 'Meja 3', 'waktu' => $now - (25 * 60), 'items' => [
                ['id' => 101, 'nama' => 'Nasi Goreng', 'qty' => 2, 'qtyDone' => 0],
                ['id' => 102, 'nama' => 'Es Teh', 'qty' => 2, 'qtyDone' => 0],
                ['id' => 103, 'nama' => 'Kerupuk', 'qty' => 1, 'qtyDone' => 0],
            ]],
            ['id' => 2, 'meja' => 'Meja 7', 'waktu' => $now - (18 * 60), 'items' => [
                ['id' => 201, 'nama' => 'Mie Ayam', 'qty' => 1, 'qtyDone' => 0],
                ['id' => 202, 'nama' => 'Jus Alpukat', 'qty' => 1, 'qtyDone' => 0],
                ['id' => 203, 'nama' => 'Pangsit Goreng', 'qty' => 2, 'qtyDone' => 0],
            ]],
            ['id' => 3, 'meja' => 'Meja 1', 'waktu' => $now - (12 * 60), 'items' => [
                ['id' => 301, 'nama' => 'Ayam Bakar', 'qty' => 3, 'qtyDone' => 0],
                ['id' => 302, 'nama' => 'Nasi Putih', 'qty' => 3, 'qtyDone' => 0],
                ['id' => 303, 'nama' => 'Lalapan', 'qty' => 3, 'qtyDone' => 0],
                ['id' => 304, 'nama' => 'Sambal Terasi', 'qty' => 1, 'qtyDone' => 0],
            ]],
            ['id' => 4, 'meja' => 'Meja 5', 'waktu' => $now - (5 * 60), 'items' => [
                ['id' => 401, 'nama' => 'Soto Ayam', 'qty' => 1, 'qtyDone' => 0],
                ['id' => 402, 'nama' => 'Perkedel', 'qty' => 2, 'qtyDone' => 0],
                ['id' => 403, 'nama' => 'Kerupuk', 'qty' => 1, 'qtyDone' => 0],
            ]],
            ['id' => 5, 'meja' => 'Meja 2', 'waktu' => $now - 40, 'items' => [
                ['id' => 501, 'nama' => 'Es Campur', 'qty' => 2, 'qtyDone' => 0],
                ['id' => 502, 'nama' => 'Kerupuk', 'qty' => 1, 'qtyDone' => 0],
                ['id' => 503, 'nama' => 'Pisang Goreng', 'qty' => 3, 'qtyDone' => 0],
                ['id' => 504, 'nama' => 'Es Teh Manis', 'qty' => 2, 'qtyDone' => 0],
            ]],
            ['id' => 6, 'meja' => 'Meja 9', 'waktu' => $now - (8 * 60), 'items' => [
                ['id' => 601, 'nama' => 'Nasi Putih', 'qty' => 5, 'qtyDone' => 0],
                ['id' => 602, 'nama' => 'Ayam Geprek', 'qty' => 3, 'qtyDone' => 0],
                ['id' => 603, 'nama' => 'Tempe Goreng', 'qty' => 4, 'qtyDone' => 0],
                ['id' => 604, 'nama' => 'Es Teh Manis', 'qty' => 5, 'qtyDone' => 0],
                ['id' => 605, 'nama' => 'Jus Alpukat', 'qty' => 2, 'qtyDone' => 0],
                ['id' => 606, 'nama' => 'Tahu Goreng', 'qty' => 3, 'qtyDone' => 0],
                ['id' => 607, 'nama' => 'Sate Ayam', 'qty' => 5, 'qtyDone' => 0],
                ['id' => 608, 'nama' => 'Gado-Gado', 'qty' => 1, 'qtyDone' => 0],
            ]],
        ];

        usort($orders, static fn ($a, $b) => $a['waktu'] <=> $b['waktu']);

        foreach ($orders as &$order) {
            $order['baru'] = ($now - $order['waktu']) < 90;
        }
        unset($order);

        return view('kitchen/index', [
            'title'  => 'Kitchen Display',
            'orders' => $orders,
        ]);
    }
}
