<?php

// Lokasi file ini di project: app/Controllers/ManagePoin.php
//
// CATATAN UNTUK DELON:
// - Sesuaikan nama filter role ('auth') dan cara cek role dengan yang dipakai
//   AuthFilter.php punya tim login (LOGIN_INFO.md bilang session simpan 'role').
// - Route untuk controller ini didaftarkan terpisah di app/Config/Routes.php
//   (lihat contoh di file routes_snippet.php).

namespace App\Controllers;

use App\Models\PoinModel;
use App\Models\RiwayatPoinModel;

class ManagePoin extends BaseController
{
    protected PoinModel $poinModel;
    protected RiwayatPoinModel $riwayatModel;

    public function __construct()
    {
        $this->poinModel   = new PoinModel();
        $this->riwayatModel = new RiwayatPoinModel();
    }

    /**
     * Halaman utama: daftar aturan poin yang sudah dibuat.
     */
    public function index()
    {
        $data = [
            'title'  => 'Manage Sistem Poin',
            'aturan' => $this->poinModel->orderBy('id', 'DESC')->findAll(),
        ];

        return view('Poin/Index', $data);
    }

    /**
     * Tampilkan form tambah aturan poin baru.
     */
    public function create()
    {
        return view('Poin/Form', [
            'title'  => 'Tambah Aturan Poin',
            'aturan' => null,
        ]);
    }

    /**
     * Simpan aturan poin baru dari form.
     */
    public function store()
    {
        $rules = [
            'nama_aturan'       => 'required|min_length[3]|max_length[100]',
            'rasio_rupiah'      => 'required|integer|greater_than[0]',
            'minimal_transaksi' => 'permit_empty|integer|greater_than_equal_to[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->poinModel->insert([
            'nama_aturan'       => $this->request->getPost('nama_aturan'),
            'rasio_rupiah'      => (int) $this->request->getPost('rasio_rupiah'),
            'minimal_transaksi' => (int) ($this->request->getPost('minimal_transaksi') ?: 0),
            'status_aktif'      => $this->request->getPost('status_aktif') ?: 'aktif',
        ]);

        return redirect()->to('/manage-poin')->with('success', 'Aturan poin berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit aturan poin.
     */
    public function edit($id)
    {
        $aturan = $this->poinModel->find($id);

        if (! $aturan) {
            return redirect()->to('/manage-poin')->with('error', 'Aturan poin tidak ditemukan.');
        }

        return view('Poin/Form', [
            'title'  => 'Edit Aturan Poin',
            'aturan' => $aturan,
        ]);
    }

    /**
     * Simpan perubahan aturan poin.
     */
    public function update($id)
    {
        $rules = [
            'nama_aturan'       => 'required|min_length[3]|max_length[100]',
            'rasio_rupiah'      => 'required|integer|greater_than[0]',
            'minimal_transaksi' => 'permit_empty|integer|greater_than_equal_to[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->poinModel->update($id, [
            'nama_aturan'       => $this->request->getPost('nama_aturan'),
            'rasio_rupiah'      => (int) $this->request->getPost('rasio_rupiah'),
            'minimal_transaksi' => (int) ($this->request->getPost('minimal_transaksi') ?: 0),
            'status_aktif'      => $this->request->getPost('status_aktif') ?: 'aktif',
        ]);

        return redirect()->to('/manage-poin')->with('success', 'Aturan poin berhasil diperbarui.');
    }

    /**
     * Hapus aturan poin.
     */
    public function delete($id)
    {
        $this->poinModel->delete($id);

        return redirect()->to('/manage-poin')->with('success', 'Aturan poin berhasil dihapus.');
    }

    /**
     * Lihat riwayat poin semua user (untuk admin/penjual pantau transaksi poin).
     */
    public function riwayat()
    {
        $data = [
            'title'   => 'Riwayat Poin',
            'riwayat' => $this->riwayatModel->orderBy('created_at', 'DESC')->findAll(),
        ];

        return view('Poin/Riwayat', $data);
    }
}
