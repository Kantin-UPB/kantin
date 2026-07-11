<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - <?= env('app.name', 'Kantin') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        html, body {
            height: 100%;
            overflow: hidden;
        }

        body {
            display: flex;
            flex-direction: column;
            background-color: #14181d;
            color: #f1f1f1;
        }

        .kitchen-topbar {
            flex: 0 0 auto;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #2a2f36;
        }

        .kitchen-grid-area {
            flex: 1 1 auto;
            overflow-y: auto;
        }

        .order-card {
            display: flex;
            flex-direction: column;
            background-color: #1f242b;
            border: 1px solid #2a2f36;
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            transition: background-color .3s ease;
        }

        .order-card .order-meja {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .order-card .order-elapsed {
            font-size: 0.95rem;
            color: #adb5bd;
        }

        .order-card ul.order-items {
            margin: 0.75rem 0;
            padding-left: 0;
            list-style: none;
            max-height: 220px;
            overflow-y: auto;
        }

        .order-card .order-item {
            padding: 0.4rem 0.5rem;
            border-radius: 0.4rem;
            cursor: pointer;
        }

        .order-card .order-item:hover {
            background-color: rgba(255, 255, 255, 0.08);
        }

        .order-card.order-delayed {
            background-color: #7a1f1f;
            border-color: #a33;
        }

        .order-card.order-delayed .order-elapsed {
            color: #ffd9d9;
        }
    </style>
</head>

<body>
    <div class="kitchen-topbar d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h1 class="h4 mb-0"><i class="bi bi-fire me-2"></i><?= esc($title) ?></h1>
        <div class="d-flex align-items-center gap-2">
            <span id="jamSekarang" class="fs-5 fw-semibold"></span>
            <label for="pilihGrid" class="mb-0 small text-muted">Grid</label>
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

    <div class="container-fluid kitchen-grid-area p-4">
        <div id="gridOrder" class="row row-cols-4 g-3"></div>
    </div>

    <div class="modal fade" id="modalQty" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="qtyModalNama">Nama Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="text-muted mb-2">Berapa qty yang mau ditandai selesai?</p>
                    <div class="d-flex justify-content-center align-items-center gap-3">
                        <button type="button" class="btn btn-outline-light" onclick="ubahQtyModal(-1)">-</button>
                        <span id="qtyModalValue" class="fs-3 fw-bold">1</span>
                        <button type="button" class="btn btn-outline-light" onclick="ubahQtyModal(1)">+</button>
                    </div>
                    <button type="button" class="btn btn-link text-info mt-2" onclick="pilihMaxQty()">Pilih Maksimal (<span id="qtyModalMaxLabel"></span>)</button>
                </div>
                <div class="modal-footer border-secondary">
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
                    <div class="col-12 text-center text-muted py-5">
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
                            <li class="order-item d-flex justify-content-between align-items-center" onclick="klikItem(${order.id}, ${item.id})">
                                <span>${item.nama}</span>
                                <span class="badge bg-secondary">@${sisa}</span>
                            </li>`;
                    })
                    .join('');

                return `
                    <div class="col">
                        <div class="order-card h-100" data-waktu="${order.waktu}">
                            <div class="order-meja">${order.meja}</div>
                            <div class="order-elapsed"></div>
                            <ul class="order-items">${itemsHtml}</ul>
                            <button type="button" class="btn btn-success w-100 mt-auto" onclick="selesaikanSemua(${order.id})">
                                <i class="bi bi-check2-all me-1"></i> Selesai Semua
                            </button>
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
