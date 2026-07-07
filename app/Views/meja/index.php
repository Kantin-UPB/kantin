<div class="container-fluid px-4 py-4">
    <h2 class="mb-4"><i class="bi bi-ui-checks-grid"></i> Monitoring Meja (Real-Time)</h2>

    <div class="row text-center mb-4 g-3">
        <div class="col-md-4">
            <div class="card bg-success text-white shadow-sm border-0">
                <div class="card-body py-4">
                    <h6 class="card-title text-uppercase mb-2"><i class="bi bi-check-circle"></i> Tersedia</h6>
                    <h2 id="count-tersedia" class="mb-0 fw-bold">0 Meja</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark shadow-sm border-0">
                <div class="card-body py-4">
                    <h6 class="card-title text-uppercase mb-2"><i class="bi bi-clock-history"></i> Dipesan</h6>
                    <h2 id="count-dipesan" class="mb-0 fw-bold">0 Meja</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white shadow-sm border-0">
                <div class="card-body py-4">
                    <h6 class="card-title text-uppercase mb-2"><i class="bi bi-x-circle"></i> Terisi</h6>
                    <h2 id="count-terisi" class="mb-0 fw-bold">0 Meja</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-dark text-white py-3">
            <h6 class="mb-0 fw-bold"><i class="bi bi-display"></i> Denah Meja Kantin</h6>
        </div>
        <div class="card-body bg-light">
            <div class="row g-4" id="grid-meja">
                </div>
        </div>
    </div>
    <div class="modal fade" id="modalMeja" tabindex="-1" aria-labelledby="modalMejaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalMejaLabel">
                        <i class="bi bi-sliders"></i> KONTROL MEJA: <span id="modal-table-id" class="fw-bold"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex align-items-center mb-4 p-3 bg-light rounded border">
                        <span class="me-3 fw-bold text-secondary">Status Saat Ini:</span>
                        <span id="modal-current-indicator" class="badge rounded-pill fs-6 px-3 py-2 shadow-sm"></span>
                    </div>
                    
                    <p class="text-secondary small mb-3 fw-bold">PILIH AKSI UNTUK MENGUBAH STATUS:</p>
                    <div class="d-grid gap-3">
                        <button onclick="updateTableStatus('terisi')" class="btn btn-outline-danger text-start p-3 d-flex align-items-center rounded-3">
                            <i class="bi bi-person-fill fs-3 me-3"></i> 
                            <div>
                                <strong class="d-block mb-1">[ ISI MEJA ]</strong>
                                <small>Pelanggan langsung duduk / meja terisi</small>
                            </div>
                        </button>
                        <button onclick="updateTableStatus('booking')" class="btn btn-outline-warning text-start p-3 d-flex align-items-center rounded-3">
                            <i class="bi bi-calendar-check-fill fs-3 me-3"></i> 
                            <div>
                                <strong class="d-block mb-1">[ BOOKING ]</strong>
                                <small>Amankan meja untuk reservasi nanti</small>
                            </div>
                        </button>
                        <button onclick="updateTableStatus('kosong')" class="btn btn-outline-success text-start p-3 d-flex align-items-center rounded-3">
                            <i class="bi bi-check-circle-fill fs-3 me-3"></i> 
                            <div>
                                <strong class="d-block mb-1">[ KOSONGKAN ]</strong>
                                <small>Reset meja jika sudah selesai / dibersihkan</small>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

<script>
    // Ambil data dari PHP Controller yang sudah diubah jadi JSON
    const mejaData = <?= $mejaData ?>;

    const statusConfig = {
        'kosong': { bg: 'bg-success', text: 'text-white', label: 'Kosong', icon: 'bi-check-circle' },
        'booking': { bg: 'bg-warning', text: 'text-dark', label: 'Booking', icon: 'bi-clock-history' },
        'terisi': { bg: 'bg-danger', text: 'text-white', label: 'Terisi', icon: 'bi-x-circle' }
    };

    let selectedMejaId = null;
    let myModal = null;

    document.addEventListener("DOMContentLoaded", function() {
        myModal = new bootstrap.Modal(document.getElementById('modalMeja'));
        renderMeja();
    });

    function renderMeja() {
        const grid = document.getElementById('grid-meja');
        grid.innerHTML = ''; 
        let tersedia = 0, dipesan = 0, terisi = 0;

        for (const [id, status] of Object.entries(mejaData)) {
            if (status === 'kosong') tersedia++;
            if (status === 'booking') dipesan++;
            if (status === 'terisi') terisi++;

            const config = statusConfig[status];
            
            grid.innerHTML += `
                <div class="col-12 col-sm-6 col-md-3">
                    <div onclick="openModal('${id}')" class="card h-100 ${config.bg} ${config.text} text-center shadow" style="cursor:pointer; transition: transform 0.2s;">
                        <div class="card-body d-flex flex-column justify-content-center py-4">
                            <h3 class="card-title fw-bold mb-2">${id}</h3>
                            <div><i class="bi ${config.icon} display-6 mb-2"></i></div>
                            <span class="fw-bold text-uppercase mt-1 tracking-wider">${config.label}</span>
                        </div>
                    </div>
                </div>
            `;
        }

        document.getElementById('count-tersedia').innerText = `${tersedia} Meja`;
        document.getElementById('count-dipesan').innerText = `${dipesan} Meja`;
        document.getElementById('count-terisi').innerText = `${terisi} Meja`;
    }

    function openModal(id) {
        selectedMejaId = id;
        document.getElementById('modal-table-id').innerText = id;
        
        const statusSaatIni = mejaData[id];
        const config = statusConfig[statusSaatIni];
        
        const indicator = document.getElementById('modal-current-indicator');
        indicator.className = `badge rounded-pill ${config.bg} ${config.text} fs-6 px-3 py-2 shadow-sm`;
        indicator.innerHTML = `<i class="bi ${config.icon}"></i> ${config.label.toUpperCase()}`;

        myModal.show();
    }

    function updateTableStatus(newStatus) {
        if (selectedMejaId) {
            // 1. Update UI secara langsung agar responsif tanpa nunggu loading server
            mejaData[selectedMejaId] = newStatus;
            renderMeja();
            myModal.hide();

            // 2. Kirim update ke Database secara background (AJAX)
            let formData = new FormData();
            formData.append('id_meja', selectedMejaId);
            formData.append('status', newStatus);

            fetch('<?= site_url('/meja/update') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(response => response.json())
            .then(data => {
                if(!data.success) {
                    console.error("Gagal menyimpan ke database");
                }
            })
            .catch(error => console.error('Error AJAX:', error));
        }
    }
</script>