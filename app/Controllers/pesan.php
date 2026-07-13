<?php

namespace App\Controllers;

use App\Models\PaketBundlingModel;
use App\Models\MenuModel;

class Pesan extends BaseController
{
    public function index()
    {
        $paketModel = new PaketBundlingModel();
        $menuModel  = new MenuModel();

        $paketList = $paketModel->getActivePaketList();
        $menuList  = $menuModel->getActiveMenuList();

        $paketPromo = array_map(static function (array $paket): array {
            return [
                'id'           => (int) $paket['id'],
                'nama'         => $paket['nama_paket'],
                'desc'         => $paket['deskripsi'] ?? '',
                'harga'        => (float) $paket['harga_paket'],
                'harga_normal' => (float) ($paket['harga_normal'] ?? 0),
                'hemat'        => (float) ($paket['hemat'] ?? 0),
                'persen_hemat' => (int) ($paket['persen_hemat'] ?? 0),
                'img'          => ! empty($paket['url_gambar']) ? base_url($paket['url_gambar']) : '',
                'items'        => $paket['items'] ?? [],
            ];
        }, $paketList);

        $data = [
            'title'      => 'Client Side - Halaman Pesan',
            'paketPromo' => $paketPromo,
        ];

        return view('client/pesan', $data);
    }
}