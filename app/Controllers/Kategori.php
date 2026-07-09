<?php

namespace App\Controllers;

use App\Models\KategoriModel;
use CodeIgniter\HTTP\RedirectResponse;

class Kategori extends BaseController
{
    protected KategoriModel $kategoriModel;

    public function __construct()
    {
        helper('form');
        $this->kategoriModel = new KategoriModel();
    }

    private function renderPage(string $view, array $data): string
    {
        return view('Layout/Header', ['title' => $data['title'] ?? 'Kategori'])
            . view('Layout/Menu')
            . view($view, $data)
            . view('Layout/Footer');
    }

    public function index()
    {
        $data = [
            'title' => 'Daftar Kategori',
            'kategori' => $this->kategoriModel->getKategoriList(),
        ];

        return $this->renderPage('kategori/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Kategori',
            'kategori' => [],
            'validation' => \Config\Services::validation(),
        ];

        return $this->renderPage('kategori/create', $data);
    }

    public function store(): RedirectResponse
    {
        $postData = ['nama_kategori' => $this->request->getPost('nama_kategori') ?? ''];

        if (! $this->validate($this->kategoriModel->getValidationRules(), $this->kategoriModel->getValidationMessages())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if ($this->kategoriModel->insert($postData)) {
            $return = $this->request->getGet('return');
            $return = $return ? trim($return) : '';

            // Allowed return paths (fallback to /menu/create)
            $allowed = ['/menu/create', '/menu', '/menu/pending', '/menu/cancelled', '/kategori'];
            if ($return && in_array($return, $allowed, true)) {
                return redirect()->to($return)->with('success', 'Kategori berhasil ditambahkan.');
            }

            return redirect()->to('/menu/create')->with('success', 'Kategori berhasil ditambahkan.');
        }

        return redirect()->back()->withInput()->with('errors', $this->kategoriModel->errors());
    }

    public function edit($id)
    {
        $kategori = $this->kategoriModel->find($id);

        if (! $kategori) {
            return redirect()->to('/kategori')->with('error', 'Kategori tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Kategori',
            'kategori' => $kategori,
            'validation' => \Config\Services::validation(),
        ];

        return $this->renderPage('kategori/edit', $data);
    }

    public function update($id): RedirectResponse
    {
        $postData = ['nama_kategori' => $this->request->getPost('nama_kategori') ?? ''];

        if (! $this->validate($this->kategoriModel->getValidationRules(), $this->kategoriModel->getValidationMessages())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if ($this->kategoriModel->update($id, $postData)) {
            return redirect()->to('/kategori')->with('success', 'Kategori berhasil diperbarui.');
        }

        return redirect()->back()->withInput()->with('errors', $this->kategoriModel->errors());
    }

    public function delete($id): RedirectResponse
    {
        $kategori = $this->kategoriModel->find($id);

        if (! $kategori) {
            return redirect()->to('/kategori')->with('error', 'Kategori tidak ditemukan.');
        }

        if ($this->kategoriModel->isCategoryInUse((int) $id)) {
            return redirect()->to('/kategori')->with('error', 'Kategori sedang digunakan oleh menu, tidak dapat dihapus.');
        }

        if ($this->kategoriModel->delete($id)) {
            return redirect()->to('/kategori')->with('success', 'Kategori berhasil dihapus.');
        }

        return redirect()->to('/kategori')->with('error', 'Gagal menghapus kategori.');
    }
}