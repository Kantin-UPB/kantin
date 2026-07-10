<?php

namespace App\Controllers;

use App\Models\PaketBundlingModel;
use CodeIgniter\HTTP\RedirectResponse;

class PaketBundling extends BaseController
{
    protected PaketBundlingModel $paketModel;

    public function __construct()
    {
        helper('form');
        $this->paketModel = new PaketBundlingModel();
    }

    private function renderPage(string $view, array $data): string
    {
        return view('Layout/Header', ['title' => $data['title'] ?? 'Promo Bundling'])
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
        $cleanValue  = preg_replace('/[^\d]/', '', $stringValue);

        return $cleanValue === '' ? 0 : (int) $cleanValue;
    }

    private function formatHargaForInput($value): string
    {
        return number_format($this->normalizeHarga($value), 0, ',', '.');
    }

    private function getStatusLabelMap(): array
    {
        return [
            1 => 'Pending',
            5 => 'Active',
            8 => 'Cancel',
        ];
    }

    private function preparePaketData(array $postData): array
    {
        $data = [
            'nama_paket'  => $postData['nama_paket'] ?? '',
            'deskripsi'   => $postData['deskripsi'] ?? '',
            'harga_paket' => $this->normalizeHarga($postData['harga_paket'] ?? 0),
            'status_id'   => 1,
            'created_by'  => session()->get('id') ?? 0,
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $file = $this->request->getFile('url_gambar');

        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/paket_bundling';

            if (! is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            $data['url_gambar'] = 'uploads/paket_bundling/' . $newName;
        } else {
            $data['url_gambar'] = $postData['existing_url_gambar'] ?? $postData['url_gambar'] ?? '';
        }

        return $data;
    }

    private function prepareUpdateData(array $postData, int $id): array
    {
        $existing = $this->paketModel->find($id);
        $data     = $this->preparePaketData($postData);
        $data['status_id'] = $existing['status_id'] ?? 1;

        if (empty($data['url_gambar']) && ! empty($existing['url_gambar'])) {
            $data['url_gambar'] = $existing['url_gambar'];
        }

        return $data;
    }

    private function redirectToReturnOrDefault(?string $return, string $defaultPath): RedirectResponse
    {
        $return  = $return ? trim($return) : '';
        $allowed = ['/paket-bundling', '/paket-bundling/pending', '/paket-bundling/cancelled'];

        if ($return && in_array($return, $allowed, true)) {
            return redirect()->to($return);
        }

        return redirect()->to($defaultPath);
    }

    public function index()
    {
        $data = [
            'title'          => 'Promo Paket Bundling',
            'paket'          => $this->paketModel->getActivePaketList(),
            'statusLabels'   => $this->getStatusLabelMap(),
            'showBackButton' => false,
            'statusPage'     => 'active',
        ];

        return $this->renderPage('paket_bundling/index', $data);
    }

    public function pending()
    {
        $data = [
            'title'          => 'Paket Bundling Pending',
            'paket'          => $this->paketModel->getPendingPaketList(),
            'statusLabels'   => $this->getStatusLabelMap(),
            'showBackButton' => true,
            'statusPage'     => 'pending',
        ];

        return $this->renderPage('paket_bundling/index', $data);
    }

    public function cancelled()
    {
        $data = [
            'title'          => 'Paket Bundling Dibatalkan',
            'paket'          => $this->paketModel->getCancelledPaketList(),
            'statusLabels'   => $this->getStatusLabelMap(),
            'showBackButton' => true,
            'statusPage'     => 'cancelled',
        ];

        return $this->renderPage('paket_bundling/index', $data);
    }

    public function create()
    {
        $data = [
            'title'          => 'Tambah Paket Bundling',
            'paket'          => [],
            'selectedItems'  => [],
            'availableMenus' => $this->paketModel->getAvailableMenus(),
            'validation'     => \Config\Services::validation(),
            'hargaDisplay'   => '',
        ];

        return $this->renderPage('paket_bundling/create', $data);
    }

    public function store(): RedirectResponse
    {
        $postData = $this->request->getPost(['nama_paket', 'deskripsi', 'harga_paket']);
        $menuIds  = $this->request->getPost('id_menu') ?? [];
        $qtys     = $this->request->getPost('qty') ?? [];

        if (! $this->validate($this->paketModel->getValidationRules(), $this->paketModel->getValidationMessages())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $filteredMenuIds = array_values(array_filter($menuIds, fn ($id) => (int) $id > 0));

        if (empty($filteredMenuIds)) {
            return redirect()->back()->withInput()->with('errors', ['Pilih minimal 1 menu untuk paket bundling.']);
        }

        $data = $this->preparePaketData($postData);
        $id   = $this->paketModel->insert($data);

        if ($id) {
            $this->paketModel->saveItems((int) $id, $menuIds, $qtys);

            return redirect()->to('/paket-bundling/pending')->with('success', 'Paket bundling berhasil ditambahkan dan disimpan sebagai Pending.');
        }

        return redirect()->back()->withInput()->with('errors', $this->paketModel->errors());
    }

    public function show($id)
    {
        $paket = $this->paketModel->getPaketById((int) $id);

        if (! $paket) {
            return redirect()->to('/paket-bundling')->with('error', 'Paket bundling tidak ditemukan.');
        }

        $data = [
            'title'        => 'Detail Paket Bundling',
            'paket'        => $paket,
            'statusLabels' => $this->getStatusLabelMap(),
        ];

        return $this->renderPage('paket_bundling/detail', $data);
    }

    public function edit($id)
    {
        $paket = $this->paketModel->getPaketById((int) $id);

        if (! $paket) {
            return redirect()->to('/paket-bundling')->with('error', 'Paket bundling tidak ditemukan.');
        }

        $selectedItems = [];
        foreach ($paket['items'] as $item) {
            $selectedItems[(int) $item['id_menu']] = (int) $item['qty'];
        }

        $data = [
            'title'          => 'Edit Paket Bundling',
            'paket'          => array_merge($paket, ['harga_display' => $this->formatHargaForInput($paket['harga_paket'] ?? 0)]),
            'selectedItems'  => $selectedItems,
            'availableMenus' => $this->paketModel->getAvailableMenus(),
            'validation'     => \Config\Services::validation(),
            'statusLabels'   => $this->getStatusLabelMap(),
        ];

        return $this->renderPage('paket_bundling/edit', $data);
    }

    public function update($id): RedirectResponse
    {
        $id       = (int) $id;
        $postData = $this->request->getPost(['nama_paket', 'deskripsi', 'harga_paket', 'existing_url_gambar']);
        $menuIds  = $this->request->getPost('id_menu') ?? [];
        $qtys     = $this->request->getPost('qty') ?? [];

        if (! $this->validate($this->paketModel->getValidationRules(), $this->paketModel->getValidationMessages())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $filteredMenuIds = array_values(array_filter($menuIds, fn ($idMenu) => (int) $idMenu > 0));

        if (empty($filteredMenuIds)) {
            return redirect()->back()->withInput()->with('errors', ['Pilih minimal 1 menu untuk paket bundling.']);
        }

        $data = $this->prepareUpdateData($postData, $id);

        if ($this->paketModel->update($id, $data)) {
            $this->paketModel->saveItems($id, $menuIds, $qtys);

            return redirect()->to('/paket-bundling')->with('success', 'Paket bundling berhasil diperbarui.');
        }

        return redirect()->back()->withInput()->with('errors', $this->paketModel->errors());
    }

    public function cancel($id): RedirectResponse
    {
        $return = $this->request->getGet('return');

        if ($this->paketModel->update($id, ['status_id' => 8])) {
            return $this->redirectToReturnOrDefault($return, '/paket-bundling/cancelled')->with('success', 'Paket bundling berhasil dibatalkan.');
        }

        return $this->redirectToReturnOrDefault($return, '/paket-bundling/cancelled')->with('error', 'Gagal membatalkan paket bundling.');
    }

    public function draft($id): RedirectResponse
    {
        $return = $this->request->getGet('return');

        if ($this->paketModel->update($id, ['status_id' => 1])) {
            return $this->redirectToReturnOrDefault($return, '/paket-bundling/pending')->with('success', 'Paket bundling dikembalikan ke Draft.');
        }

        return $this->redirectToReturnOrDefault($return, '/paket-bundling/pending')->with('error', 'Gagal mengembalikan paket bundling ke Draft.');
    }

    public function activate($id): RedirectResponse
    {
        $return = $this->request->getGet('return');

        if ($this->paketModel->update($id, ['status_id' => 5])) {
            return $this->redirectToReturnOrDefault($return, '/paket-bundling')->with('success', 'Paket bundling berhasil diaktifkan.');
        }

        return $this->redirectToReturnOrDefault($return, '/paket-bundling')->with('error', 'Gagal mengaktifkan paket bundling.');
    }

    public function restore($id): RedirectResponse
    {
        $return = $this->request->getGet('return');

        if ($this->paketModel->update($id, ['status_id' => 1])) {
            return $this->redirectToReturnOrDefault($return, '/paket-bundling/pending')->with('success', 'Paket bundling berhasil dipulihkan ke Pending.');
        }

        return $this->redirectToReturnOrDefault($return, '/paket-bundling/cancelled')->with('error', 'Gagal memulihkan paket bundling.');
    }

    public function delete($id): RedirectResponse
    {
        $return = $this->request->getGet('return');
        $id     = (int) $id;
        $paket  = $this->paketModel->find($id);

        if (! $paket) {
            return $this->redirectToReturnOrDefault($return, '/paket-bundling/cancelled')->with('error', 'Paket bundling tidak ditemukan.');
        }

        if ((int) ($paket['status_id'] ?? 1) !== 8) {
            return $this->redirectToReturnOrDefault($return, '/paket-bundling')->with('error', 'Hanya paket yang dibatalkan yang bisa dihapus.');
        }

        if (! empty($paket['url_gambar'])) {
            $imagePath = FCPATH . ltrim($paket['url_gambar'], '/');
            if (is_file($imagePath)) {
                unlink($imagePath);
            }
        }

        $this->paketModel->deleteItems($id);

        if ($this->paketModel->delete($id)) {
            return $this->redirectToReturnOrDefault($return, '/paket-bundling/cancelled')->with('success', 'Paket bundling dibatalkan berhasil dihapus permanen.');
        }

        return $this->redirectToReturnOrDefault($return, '/paket-bundling/cancelled')->with('error', 'Gagal menghapus paket bundling.');
    }
}
