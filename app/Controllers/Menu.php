<?php

namespace App\Controllers;

use App\Models\MenuModel;
use CodeIgniter\HTTP\RedirectResponse;

class Menu extends BaseController
{
    protected MenuModel $menuModel;

    public function __construct()
    {
        helper('form');
        $this->menuModel = new MenuModel();
    }

    private function renderPage(string $view, array $data): string
    {
        return view('Layout/Header', ['title' => $data['title'] ?? 'Menu'])
            . view('Layout/Menu')
            . view($view, $data)
            . view('Layout/Footer');
    }

    private function normalizeHarga($value): int
    {
        if (is_null($value) || $value === '') {
            return 0;
        }

        $stringValue = (string) $value;
        $stringValue = str_replace(['.', ','], '', $stringValue);
        $cleanValue = preg_replace('/[^\d]/', '', $stringValue);

        return $cleanValue === '' ? 0 : (int) $cleanValue;
    }

    private function formatHargaForInput($value): string
    {
        return number_format($this->normalizeHarga($value), 0, ',', '.');
    }

    private function normalizeDiskon($value): int
    {
        if (is_null($value) || $value === '') {
            return 0;
        }

        $clean = preg_replace('/[^\d]/', '', (string) $value);
        $diskon = $clean === '' ? 0 : (int) $clean;

        return max(0, min(100, $diskon));
    }

    private function prepareMenuData(array $postData): array
    {
        $data = [
            'id_kategori' => $postData['id_kategori'] ?? null,
            'nama'        => $postData['nama'] ?? '',
            'deskripsi'   => $postData['deskripsi'] ?? '',
            'harga'       => $this->normalizeHarga($postData['harga'] ?? 0),
            'diskon'      => $this->normalizeDiskon($postData['diskon'] ?? 0),
            'status_id'   => 1,
            'created_by'  => session()->get('id') ?? 0,
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $file = $this->request->getFile('url_gambar');

        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/menus';

            if (! is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            $data['url_gambar'] = 'uploads/menus/' . $newName;
        } else {
            $data['url_gambar'] = $postData['existing_url_gambar'] ?? $postData['url_gambar'] ?? '';
        }

        return $data;
    }

    private function prepareUpdateData(array $postData, int $id): array
    {
        $existing = $this->menuModel->find($id);
        $data = $this->prepareMenuData($postData);
        $data['status_id'] = $existing['status_id'] ?? 1;

        if (empty($data['url_gambar']) && ! empty($existing['url_gambar'])) {
            $data['url_gambar'] = $existing['url_gambar'];
        }

        return $data;
    }

    private function getStatusLabelMap(): array
    {
        return [
            1 => 'Pending',
            5 => 'Active',
            8 => 'Cancel',
        ];
    }

    public function index()
    {
        $data = [
            'title' => 'Daftar Menu Aktif',
            'menu' => $this->menuModel->getActiveMenuList(),
            'statusOptions' => $this->menuModel->getStatusOptions(),
            'statusLabels' => $this->getStatusLabelMap(),
            'showBackButton' => false,
            'statusPage' => 'active',
        ];

        return $this->renderPage('menu/index', $data);
    }

    public function pending()
    {
        $data = [
            'title' => 'Menu Pending',
            'menu' => $this->menuModel->getPendingMenuList(),
            'statusOptions' => $this->menuModel->getStatusOptions(),
            'statusLabels' => $this->getStatusLabelMap(),
            'showBackButton' => true,
            'statusPage' => 'pending',
        ];

        return $this->renderPage('menu/index', $data);
    }

    public function cancelled()
    {
        $data = [
            'title' => 'Menu Dibatalkan',
            'menu' => $this->menuModel->getCancelledMenuList(),
            'statusOptions' => $this->menuModel->getStatusOptions(),
            'statusLabels' => $this->getStatusLabelMap(),
            'showBackButton' => true,
            'statusPage' => 'cancelled',
        ];

        return $this->renderPage('menu/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Menu',
            'menu' => [],
            'categories' => $this->menuModel->getCategories(),
            'validation' => \Config\Services::validation(),
            'hargaDisplay' => '',
        ];

        return $this->renderPage('menu/create', $data);
    }

    public function store(): RedirectResponse
    {
        $postData = $this->request->getPost(['id_kategori', 'nama', 'deskripsi', 'harga', 'diskon']);

        if (! $this->validate($this->menuModel->getValidationRules(), $this->menuModel->getValidationMessages())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->prepareMenuData($postData);

        if ($this->menuModel->insert($data)) {
            return redirect()->to('/menu/pending')->with('success', 'Menu berhasil ditambahkan dan disimpan sebagai Pending.');
        }

        return redirect()->back()->withInput()->with('errors', $this->menuModel->errors());
    }

    public function show($id)
    {
        $menu = $this->menuModel->getMenuById($id);

        if (! $menu) {
            return redirect()->to('/menu')->with('error', 'Menu tidak ditemukan.');
        }

        $data = [
            'title' => 'Detail Menu',
            'menu' => $menu,
            'statusOptions' => $this->menuModel->getStatusOptions(),
            'statusLabels' => $this->getStatusLabelMap(),
        ];

        return $this->renderPage('menu/detail', $data);
    }

    public function edit($id)
    {
        $menu = $this->menuModel->find($id);

        if (! $menu) {
            return redirect()->to('/menu')->with('error', 'Menu tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Menu',
            'menu' => array_merge($menu, ['harga_display' => $this->formatHargaForInput($menu['harga'] ?? 0)]),
            'categories' => $this->menuModel->getCategories(),
            'validation' => \Config\Services::validation(),
            'statusLabels' => $this->getStatusLabelMap(),
        ];

        return $this->renderPage('menu/edit', $data);
    }

    public function update($id): RedirectResponse
    {
        $postData = $this->request->getPost(['id_kategori', 'nama', 'deskripsi', 'harga', 'diskon', 'existing_url_gambar']);

        if (! $this->validate($this->menuModel->getValidationRules(), $this->menuModel->getValidationMessages())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->prepareUpdateData($postData, $id);

        if ($this->menuModel->update($id, $data)) {
            return redirect()->to('/menu')->with('success', 'Menu berhasil diperbarui.');
        }

        return redirect()->back()->withInput()->with('errors', $this->menuModel->errors());
    }

    private function redirectToReturnOrDefault(?string $return, string $defaultPath): RedirectResponse
    {
        $return = $return ? trim($return) : '';


        $allowed = ['/menu', '/menu/pending', '/menu/cancelled'];
        if ($return && in_array($return, $allowed, true)) {
            return redirect()->to($return);
        }

        return redirect()->to($defaultPath);
    }

    public function cancel($id): RedirectResponse
    {
        $return = $this->request->getGet('return');

        if ($this->menuModel->update($id, ['status_id' => 8])) {
            return $this->redirectToReturnOrDefault($return, '/menu/cancelled')->with('success', 'Menu berhasil dibatalkan.');
        }

        return $this->redirectToReturnOrDefault($return, '/menu/cancelled')->with('error', 'Gagal membatalkan menu.');
    }

    public function draft($id): RedirectResponse
    {
        $return = $this->request->getGet('return');

        if ($this->menuModel->update($id, ['status_id' => 1])) {
            // After setting to Draft, menu belongs to Pending list.
            return $this->redirectToReturnOrDefault($return, '/menu/pending')->with('success', 'Menu dikembalikan ke Draft.');
        }

        return $this->redirectToReturnOrDefault($return, '/menu/pending')->with('error', 'Gagal mengembalikan menu ke Draft.');
    }

    public function activate($id): RedirectResponse
    {
        $return = $this->request->getGet('return');

        if ($this->menuModel->update($id, ['status_id' => 5])) {
            return $this->redirectToReturnOrDefault($return, '/menu')->with('success', 'Menu berhasil diaktifkan.');
        }

        return $this->redirectToReturnOrDefault($return, '/menu')->with('error', 'Gagal mengaktifkan menu.');
    }

    public function restore($id): RedirectResponse
    {
        $return = $this->request->getGet('return');

        if ($this->menuModel->update($id, ['status_id' => 1])) {
            // After restore, menu belongs to Pending list.
            return $this->redirectToReturnOrDefault($return, '/menu/pending')->with('success', 'Menu berhasil dipulihkan ke Pending.');
        }

        return $this->redirectToReturnOrDefault($return, '/menu/cancelled')->with('error', 'Gagal memulihkan menu.');
    }

    public function delete($id): RedirectResponse
    {
        $return = $this->request->getGet('return');

        $menu = $this->menuModel->find($id);

        if (! $menu) {
            return $this->redirectToReturnOrDefault($return, '/menu/cancelled')->with('error', 'Menu tidak ditemukan.');
        }

        if ((int) ($menu['status_id'] ?? 1) !== 8) {
            return $this->redirectToReturnOrDefault($return, '/menu')->with('error', 'Hanya menu yang dibatalkan yang bisa dihapus.');
        }

        if (! empty($menu['url_gambar'])) {
            $imagePath = FCPATH . ltrim($menu['url_gambar'], '/');
            if (is_file($imagePath)) {
                unlink($imagePath);
            }
        }

        if ($this->menuModel->delete($id)) {
            return $this->redirectToReturnOrDefault($return, '/menu/cancelled')->with('success', 'Menu dibatalkan berhasil dihapus permanen.');
        }

        return $this->redirectToReturnOrDefault($return, '/menu/cancelled')->with('error', 'Gagal menghapus menu.');
    }
}

