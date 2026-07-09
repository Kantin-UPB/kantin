<?php

namespace App\Controllers;
use App\Models\MejaModel;

class Meja extends BaseController
{
    public function index()
    {
        $mejaModel = new MejaModel();
        $mejaDB = $mejaModel->findAll();
        
        // Format data dari DB ke array simple [ 'A1' => 'kosong', ... ] agar mudah dibaca JS
        $dataMejaFormatted = [];
        foreach ($mejaDB as $row) {
            $dataMejaFormatted[$row['id_meja']] = $row['status'];
        }

        $data = [
            'title'    => 'Monitoring Meja',
            'mejaData' => json_encode($dataMejaFormatted) // Kirim sebagai string JSON
        ]; 

        return view('Layout/Header', $data)
             . view('Layout/Menu')
             . view('meja/index', $data)
             . view('Layout/Footer');
    }

    public function updateStatus()
    {
        if ($this->request->isAJAX()) {
            $id_meja = $this->request->getPost('id_meja');
            $status  = $this->request->getPost('status');

            $mejaModel = new MejaModel();
            $mejaModel->update($id_meja, ['status' => $status]);

            return $this->response->setJSON(['success' => true]);
        }
    }
}