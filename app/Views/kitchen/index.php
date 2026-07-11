<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - <?= env('app.name', 'Kantin') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        [data-bs-theme="dark"] {
            --bs-body-bg: #14181d;
            --bs-card-bg: #1f242b;
            --bs-border-color: #2a2f36;
            --bs-tertiary-bg: #2a2f36;
        }

        .kds-item-list {
            max-height: 260px;
            overflow-y: auto;
        }

        .kds-item-list .list-group-item-action {
            cursor: pointer;
        }

        .order-card {
            transition: background-color .3s ease, border-color .3s ease;
        }

        .order-card.order-delayed,
        .order-card.order-delayed .list-group-item {
            background-color: #7a1f1f;
            border-color: #a33;
            color: #fff;
        }
    </style>
</head>

<body class="d-flex flex-column vh-100 overflow-hidden">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 p-3 border-bottom flex-shrink-0">
        <h1 class="h4 mb-0"><i class="bi bi-fire me-2"></i><?= esc($title) ?></h1>
        <div class="d-flex align-items-center gap-2">
            <span id="jamSekarang" class="fs-5 fw-semibold"></span>
            <label for="pilihGrid" class="mb-0 small text-secondary">Grid</label>
            <select id="pilihGrid" class="form-select form-select-sm w-auto" onchange="ubahGrid(this.value)">
                <option value="2">2 kolom</option>
                <option value="3">3 kolom</option>
                <option value="4" selected>4 kolom</option>
                <option value="5">5 kolom</option>
                <option value="6">6 kolom</option>
            </select>
            <button type="button" id="btnFullscreen" class="btn btn-sm btn-outline-light">
                <i class="bi bi-arrows-fullscreen me-1"></i> Fullscreen
            </button>
            <a href="<?= site_url('/') ?>" class="btn btn-sm btn-outline-light">
                <i class="bi bi-house-door me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>

    <div class="flex-grow-1 overflow-auto p-3">
        <div id="gridOrder" class="row row-cols-4 g-3"></div>
    </div>

    <div class="modal fade" id="modalQty" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qtyModalNama">Nama Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="text-secondary mb-2">Berapa qty yang mau ditandai selesai?</p>
                    <div class="d-flex justify-content-center align-items-center gap-3">
                        <button type="button" class="btn btn-outline-light" onclick="ubahQtyModal(-1)">-</button>
                        <span id="qtyModalValue" class="fs-3 fw-bold">1</span>
                        <button type="button" class="btn btn-outline-light" onclick="ubahQtyModal(1)">+</button>
                    </div>
                    <button type="button" class="btn btn-link" onclick="pilihMaxQty()">Pilih Maksimal (<span id="qtyModalMaxLabel"></span>)</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" onclick="konfirmasiQty()">Tandai Selesai</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const DELAY_THRESHOLD_MS = 15 * 60 * 1000;

        let dataOrder = <?= json_encode($orders) ?>;

        let qtyModalOrderId = null;
        let qtyModalItemId = null;
        let qtyModalMax = 1;

        function renderOrder() {
            dataOrder = dataOrder.filter((order) => order.items.some((item) => item.qtyDone < item.qty));

            const grid = document.getElementById('gridOrder');

            if (dataOrder.length === 0) {
                grid.innerHTML = `
                    <div class="col-12 text-center text-secondary py-5">
                        <i class="bi bi-check2-circle fs-1"></i>
                        <p class="mt-2">Semua order sudah selesai.</p>
                    </div>`;
                return;
            }

            grid.innerHTML = dataOrder.map((order) => {
                const itemsHtml = order.items
                    .filter((item) => item.qtyDone < item.qty)
                    .map((item) => {
                        const sisa = item.qty - item.qtyDone;
                        return `
                            <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" onclick="klikItem(${order.id}, ${item.id})">
                                <span>${item.nama}</span>
                                <span class="badge bg-secondary rounded-pill">@${sisa}</span>
                            </li>`;
                    })
                    .join('');

                return `
                    <div class="col">
                        <div class="card h-100 order-card" data-waktu="${order.waktu}">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span class="fw-bold fs-5">${order.meja}</span>
                                <small class="order-elapsed text-secondary"></small>
                            </div>
                            <ul class="list-group list-group-flush kds-item-list flex-grow-1">${itemsHtml}</ul>
                            <div class="card-footer">
                                <button type="button" class="btn btn-success w-100" onclick="selesaikanSemua(${order.id})">
                                    <i class="bi bi-check2-all me-1"></i> Selesai Semua
                                </button>
                            </div>
                        </div>
                    </div>`;
            }).join('');
        }

        function klikItem(orderId, itemId) {
            const order = dataOrder.find((o) => o.id === orderId);
            const item = order.items.find((i) => i.id === itemId);
            const sisa = item.qty - item.qtyDone;

            if (item.qty === 1) {
                if (confirm(`Tandai "${item.nama}" selesai?`)) {
                    item.qtyDone = item.qty;
                    renderOrder();
                }
                return;
            }

            qtyModalOrderId = orderId;
            qtyModalItemId = itemId;
            qtyModalMax = sisa;
            document.getElementById('qtyModalNama').textContent = item.nama;
            document.getElementById('qtyModalValue').textContent = '1';
            document.getElementById('qtyModalMaxLabel').textContent = sisa;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalQty')).show();
        }

        function ubahQtyModal(delta) {
            const el = document.getElementById('qtyModalValue');
            const nilaiBaru = Math.min(qtyModalMax, Math.max(1, parseInt(el.textContent, 10) + delta));
            el.textContent = nilaiBaru;
        }

        function pilihMaxQty() {
            document.getElementById('qtyModalValue').textContent = qtyModalMax;
        }

        function konfirmasiQty() {
            const order = dataOrder.find((o) => o.id === qtyModalOrderId);
            const item = order.items.find((i) => i.id === qtyModalItemId);
            const jumlah = parseInt(document.getElementById('qtyModalValue').textContent, 10);

            item.qtyDone = Math.min(item.qty, item.qtyDone + jumlah);
            renderOrder();

            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalQty')).hide();
        }

        function ubahGrid(jumlahKolom) {
            document.getElementById('gridOrder').className = `row row-cols-${jumlahKolom} g-3`;
        }

        function selesaikanSemua(orderId) {
            if (! confirm('Tandai SEMUA item di order ini selesai?')) return;

            const order = dataOrder.find((o) => o.id === orderId);
            order.items.forEach((item) => {
                item.qtyDone = item.qty;
            });
            renderOrder();
        }

        function formatElapsed(diffMs) {
            const diffMin = Math.floor(diffMs / 60000);
            if (diffMin <= 0) return 'Baru saja';
            return diffMin + ' menit lalu';
        }

        function tick() {
            const now = Date.now();
            document.querySelectorAll('.order-card').forEach((card) => {
                const waktu = parseInt(card.dataset.waktu, 10) * 1000;
                const diffMs = now - waktu;
                card.querySelector('.order-elapsed').textContent = formatElapsed(diffMs);
                card.classList.toggle('order-delayed', diffMs >= DELAY_THRESHOLD_MS);
            });

            const jamEl = document.getElementById('jamSekarang');
            if (jamEl) jamEl.textContent = new Date().toLocaleTimeString('id-ID');
        }

        function beepNotifikasi() {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (! AudioCtx) return;
            const ctx = new AudioCtx();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.value = 880;
            gain.gain.setValueAtTime(0.2, ctx.currentTime);
            osc.start();
            osc.stop(ctx.currentTime + 0.3);
        }

        document.getElementById('btnFullscreen').addEventListener('click', () => {
            if (! document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        });

        window.addEventListener('DOMContentLoaded', () => {
            renderOrder();
            setInterval(tick, 1000);
            tick();

            if (dataOrder.some((order) => order.baru)) {
                beepNotifikasi();
            }
        });
    </script>
</body>

</html>
