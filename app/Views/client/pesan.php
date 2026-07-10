<?= $this->extend('client/layout'); ?>

<?= $this->section('content'); ?>
<div class="row" id="appKantin">
    
    <div class="col-12 mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-1">🛒 Pemesanan</h2>
            <p class="text-muted mb-0">Bypass Route /pesan - Fitur Grid 6 Menu & Pagination Dinamis</p>
        </div>
        
        <div class="d-flex gap-2">
            <button class="btn btn-outline-success btn-lg shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambahMenu">
                <i class="bi bi-plus-circle-fill me-2"></i> Tambah Menu Baru
            </button>

            <button class="btn btn-primary btn-lg position-relative shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#modalKeranjang" onclick="renderKeranjang()">
                <i class="bi bi-cart3 me-2"></i> Lihat Keranjang
                <span id="badge-keranjang" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    0
                </span>
            </button>
        </div>
    </div>

    <div class="col-12 mb-4">
        <ul class="nav nav-pills nav-fill bg-white p-2 rounded shadow-sm" id="kategoriTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-bold" id="makanan-tab" data-bs-toggle="tab" data-bs-target="#makanan" type="button" onclick="gantiTab('makanan')"><i class="bi bi-egg-fried me-2"></i>Makanan</button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold" id="minuman-tab" data-bs-toggle="tab" data-bs-target="#minuman" type="button" onclick="gantiTab('minuman')"><i class="bi bi-cup-straw me-2"></i>Minuman</button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold" id="dessert-tab" data-bs-toggle="tab" data-bs-target="#dessert" type="button" onclick="gantiTab('dessert')"><i class="bi bi-ice-cream me-2"></i>Dessert</button>
            </li>
        </ul>
    </div>

    <div class="col-12 mb-3">
        <div class="d-flex justify-content-end align-items-center gap-2">
            <label for="sortHarga" class="form-label fw-semibold mb-0 text-muted small">
                <i class="bi bi-sort-down me-1"></i> Urutkan Harga
            </label>
            <select class="form-select form-select-sm w-auto shadow-sm" id="sortHarga" onchange="ubahSortHarga(this.value)">
                <option value="default">Default</option>
                <option value="asc">Termurah &rarr; Termahal</option>
                <option value="desc">Termahal &rarr; Termurah</option>
            </select>
        </div>
    </div>

    <div class="col-12">
        <div class="tab-content" id="kontenKategori">
            
            <div class="tab-pane fade show active" id="makanan">
                <div class="row g-4" id="grid-makanan"></div>
                <div class="d-flex justify-content-center mt-4" id="pagination-makanan"></div>
            </div>
            
            <div class="tab-pane fade" id="minuman">
                <div class="row g-4" id="grid-minuman"></div>
                <div class="d-flex justify-content-center mt-4" id="pagination-minuman"></div>
            </div>
            
            <div class="tab-pane fade" id="dessert">
                <div class="row g-4" id="grid-dessert"></div>
                <div class="d-flex justify-content-center mt-4" id="pagination-dessert"></div>
            </div>
            
        </div>
    </div>

    <div class="modal fade" id="modalTambahMenu" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-square me-2"></i> Input Menu Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="formMenuBaru">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Menu</label>
                            <input type="text" class="form-control" id="inputNama" placeholder="Contoh: Ayam Bakar Solo" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kategori</label>
                            <select class="form-select" id="inputKategori" required>
                                <option value="makanan">Makanan</option>
                                <option value="minuman">Minuman</option>
                                <option value="dessert">Dessert</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Harga (Rupiah)</label>
                            <input type="number" class="form-control" id="inputHarga" placeholder="Contoh: 15000" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi Menu</label>
                            <textarea class="form-control" id="inputDesc" rows="3" placeholder="Tulis rincian menu..." required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success px-4" onclick="simpanMenuBaru()">Simpan ke Daftar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalKeranjang" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-basket3 me-2"></i> Detail Keranjang Belanja</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="list-keranjang" class="mb-3"></div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center fw-bold fs-5">
                        <span>Total Pembayaran:</span>
                        <span class="text-danger" id="total-harga">Rp 0</span>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tambah Menu Lagi</button>
                    <button type="button" class="btn btn-success px-4 fw-bold" id="btn-pesan" onclick="prosesPesan()">
                        <i class="bi bi-cursor-fill me-2"></i> Langsung Pesan!
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    // Data dummy awal sengaja dibuat banyak (7 makanan) biar halaman ke-2 langsung aktif otomatis pas dibuka!
    let produkKantin = [
        { id: 1, kategori: 'makanan', nama: 'Nasi Goreng Spesial', harga: 15000, desc: 'Nasi goreng + telur ceplok + ayam suwir', img: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500' },
        { id: 2, kategori: 'makanan', nama: 'Ayam Geprek Sambal Ijo', harga: 18000, desc: 'Ayam krispi dengan ulekan cabai ijo asli', img: 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=500' },
        { id: 3, kategori: 'makanan', nama: 'Mie Goreng Dok-Dok', harga: 13000, desc: 'Mie goreng legendaris porsi kenyang', img: 'https://images.unsplash.com/photo-1585032226651-759b368d7246?w=500' },
        { id: 4, kategori: 'makanan', nama: 'Nasi Ayam Bakar', harga: 20000, desc: 'Ayam panggang kecap meresap manis gurih', img: 'https://images.unsplash.com/photo-1532550907401-a500c9a57435?w=500' },
        { id: 5, kategori: 'makanan', nama: 'Soto Ayam Lamongan', harga: 14000, desc: 'Soto kuah kuning kental plus taburan koya', img: 'https://images.unsplash.com/photo-1626804475315-776371f60049?w=500' },
        { id: 6, kategori: 'makanan', nama: 'Bakso Sapi Urat', harga: 16000, desc: 'Pentol urat besar dengan kuah kaldu segar', img: 'https://images.unsplash.com/photo-1583032015879-e5025c7582b0?w=500' },
        // Menu ke-7 (Akan terlempar ke Page 2 otomatis)
        { id: 7, kategori: 'makanan', nama: 'Bebek Goreng Serundeng', harga: 25000, desc: 'Bebek empuk krispi tabur serundeng kelapa', img: 'https://images.unsplash.com/photo-1516685018646-549198525c1b?w=500' },
        
        { id: 8, kategori: 'minuman', nama: 'Es Jeruk Peras', harga: 6000, desc: 'Jeruk peras murni tanpa pemanis buatan', img: 'https://images.unsplash.com/photo-1497034825429-c343d7c6a68f?w=500' },
        { id: 9, kategori: 'minuman', nama: 'Es Teh Manis Jumbo', harga: 4000, desc: 'Teh manis dingin porsi besar pelepas dahaga', img: 'https://images.unsplash.com/photo-1568254183919-78a4f43a2877?w=500' },
        { id: 10, kategori: 'dessert', nama: 'Salad Buah Sehat', harga: 12000, desc: 'Potongan buah segar dengan saus mayo manis', img: 'https://images.unsplash.com/photo-1511690656952-34342bb7c2f2?w=500' },
        { id: 11, kategori: 'dessert', nama: 'Puding Coklat Lava', harga: 8000, desc: 'Puding lembut dengan vla susu lumer', img: 'https://images.unsplash.com/photo-1541783245831-57d6fb0926d3?w=500' }
    ];

    let keranjangBelanja = [];
    
    // Status halaman aktif untuk tiap kategori
    let currentPage = {
        makanan: 1,
        minuman: 1,
        dessert: 1
    };
    
    const limitPerHalaman = 6; // Batas maksimal isi 1 page sesuai permintaanmu

    // Status urutan harga aktif: 'default', 'asc' (rendah->tinggi), 'desc' (tinggi->rendah)
    let sortHargaAktif = 'default';

    function ubahSortHarga(value) {
        sortHargaAktif = value;
        // Reset semua kategori ke halaman 1 biar urutan baru konsisten dari awal
        currentPage.makanan = 1;
        currentPage.minuman = 1;
        currentPage.dessert = 1;
        tampilkanSemua();
    }

    const placeholderImages = {
        makanan: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=500',
        minuman: 'https://images.unsplash.com/photo-1497515114629-f71d768fd07c?w=500',
        dessert: 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=500'
    };

    function gantiTab(kategori) {
        // Reset ke page 1 tiap kali pindah tab biar gak bingung
        currentPage[kategori] = 1;
        tampilkanMenuPerKategori(kategori);
    }

    function tampilkanMenuPerKategori(kat) {
        const grid = document.getElementById(`grid-${kat}`);
        const paginationContainer = document.getElementById(`pagination-${kat}`);
        grid.innerHTML = '';
        paginationContainer.innerHTML = '';

        // 1. Filter menu berdasarkan kategori saat ini
        let menuTerfilter = produkKantin.filter(item => item.kategori === kat);

        // 1b. Urutkan berdasarkan harga jika filter sort aktif dipilih
        if (sortHargaAktif === 'asc') {
            menuTerfilter = menuTerfilter.slice().sort((a, b) => a.harga - b.harga);
        } else if (sortHargaAktif === 'desc') {
            menuTerfilter = menuTerfilter.slice().sort((a, b) => b.harga - a.harga);
        }
        
        // 2. Hitung batasan index item untuk pagination (Rumus potong data 6 item)
        const indexMulai = (currentPage[kat] - 1) * limitPerHalaman;
        const indexSelesai = indexMulai + limitPerHalaman;
        const menuHalamanIni = menuTerfilter.slice(indexMulai, indexSelesai);

        // 3. Render Grid Menu ke HTML
        if(menuHalamanIni.length === 0) {
            grid.innerHTML = `<div class="col-12 text-center text-muted py-5">Belum ada menu di halaman ini.</div>`;
            return;
        }

        menuHalamanIni.forEach(item => {
            grid.innerHTML += `
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden product-card">
                        <img src="${item.img}" class="card-img-top" style="height: 150px; object-fit: cover;">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="card-title fw-bold text-dark mb-1">${item.nama}</h6>
                                <p class="text-muted small mb-2 text-truncate-2" style="font-size: 11px; min-height: 32px;">${item.desc}</p>
                            </div>
                            <div>
                                <h6 class="text-danger fw-bold mb-2">Rp ${item.harga.toLocaleString('id-ID')}</h6>
                                <button class="btn btn-outline-primary btn-sm w-100 fw-bold" onclick="tambahKeKeranjang(${item.id})">
                                    <i class="bi bi-plus-lg"></i> Beli
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        // 4. Render Tulisan Kecil Navigasi Next Page di Bawah
        const totalHalaman = Math.ceil(menuTerfilter.length / limitPerHalaman);
        if (totalHalaman > 1) {
            let tombolHTML = `<nav aria-label="Page navigation"><ul class="pagination pagination-sm mb-0">`;
            
            // Tombol Previous
            tombolHTML += `
                <li class="page-item ${currentPage[kat] === 1 ? 'disabled' : ''}">
                    <a class="page-link text-muted" href="javascript:void(0)" onclick="pindahHalaman('${kat}', ${currentPage[kat] - 1})">Previous</a>
                </li>`;

            // Angka Halaman
            for (let i = 1; i <= totalHalaman; i++) {
                tombolHTML += `
                    <li class="page-item ${currentPage[kat] === i ? 'active' : ''}">
                        <a class="page-link" href="javascript:void(0)" onclick="pindahHalaman('${kat}', ${i})">${i}</a>
                    </li>`;
            }

            // Tombol Next Page Tulisan Kecil
            tombolHTML += `
                <li class="page-item ${currentPage[kat] === totalHalaman ? 'disabled' : ''}">
                    <a class="page-link text-primary fw-semibold" href="javascript:void(0)" onclick="pindahHalaman('${kat}', ${currentPage[kat] + 1})">Next Page &raquo;</a>
                </li>`;
                
            tombolHTML += `</ul></nav>`;
            paginationContainer.innerHTML = tombolHTML;
        }
    }

    function pindahHalaman(kategori, targetPage) {
        currentPage[kategori] = targetPage;
        tampilkanMenuPerKategori(kategori);
        // Otomatis scroll smooth sedikit ke atas area tab pas ganti halaman
        document.getElementById('kategoriTab').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function tampilkanSemua() {
        tampilkanMenuPerKategori('makanan');
        tampilkanMenuPerKategori('minuman');
        tampilkanMenuPerKategori('dessert');
    }

    function simpanMenuBaru() {
        const nama = document.getElementById('inputNama').value;
        const kategori = document.getElementById('inputKategori').value;
        const harga = parseInt(document.getElementById('inputHarga').value);
        const desc = document.getElementById('inputDesc').value;

        if(!nama || !harga || !desc) {
            alert('Tolong isi semua kolom formnya ya!');
            return;
        }

        const idBaru = produkKantin.length + 1;
        const gambarOtomatis = placeholderImages[kategori];

        produkKantin.push({
            id: idBaru,
            kategori: kategori,
            nama: nama,
            harga: harga,
            desc: desc,
            img: gambarOtomatis
        });

        tampilkanSemua();
        
        document.getElementById('formMenuBaru').reset();
        const modalEl = document.getElementById('modalTambahMenu');
        const modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();

        alert(`✔ Menu "${nama}" sukses disimpan! Menuju ke halaman kategori terkait.`);
    }

    function tambahKeKeranjang(idProduk) {
        const produk = produkKantin.find(p => p.id === idProduk);
        const sudahAda = keranjangBelanja.find(item => item.id === idProduk);

        if (sudahAda) {
            sudahAda.qty += 1;
        } else {
            keranjangBelanja.push({ ...produk, qty: 1 });
        }
        updateBadge();
        alert(`✔ ${produk.nama} dimasukkan ke keranjang!`);
    }

    function updateBadge() {
        const totalQty = keranjangBelanja.reduce((sum, item) => sum + item.qty, 0);
        document.getElementById('badge-keranjang').innerText = totalQty;
    }

    function renderKeranjang() {
        const container = document.getElementById('list-keranjang');
        const totalHargaElement = document.getElementById('total-harga');
        const btnPesan = document.getElementById('btn-pesan');
        
        container.innerHTML = '';
        let totalBelanja = 0;

        if (keranjangBelanja.length === 0) {
            container.innerHTML = `<p class="text-center text-muted py-4">Keranjang masih kosong nih. Yuk jajan dulu!</p>`;
            btnPesan.disabled = true;
            totalHargaElement.innerText = "Rp 0";
            return;
        }

        btnPesan.disabled = false;

        keranjangBelanja.forEach((item, index) => {
            const subtotal = item.harga * item.qty;
            totalBelanja += subtotal;

            container.innerHTML += `
                <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded mb-2 border-start border-primary border-3">
                    <div>
                        <h6 class="fw-bold mb-0">${item.nama}</h6>
                        <small class="text-muted">Rp ${item.harga.toLocaleString('id-ID')} x ${item.qty}</small>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="fw-bold text-dark">Rp ${subtotal.toLocaleString('id-ID')}</span>
                        <button class="btn btn-sm btn-danger py-0 px-2" onclick="hapusItemKeranjang(${index})"><i class="bi bi-trash"></i></button>
                    </div>
                </div>
            `;
        });

        totalHargaElement.innerText = `Rp ${totalBelanja.toLocaleString('id-ID')}`;
    }

    function hapusItemKeranjang(index) {
        keranjangBelanja.splice(index, 1);
        updateBadge();
        renderKeranjang();
    }

    function prosesPesan() {
        alert("🚀 PESANAN BERHASIL DIKIRIM!\n\nSimulasi sukses: Sisi visual antarmuka pemesanan client aman!");
        keranjangBelanja = [];
        updateBadge();
        
        const modalEl = document.getElementById('modalKeranjang');
        const modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();
    }

    document.addEventListener("DOMContentLoaded", function() {
        tampilkanSemua();
    });
</script>

<style>
    .product-card { transition: transform 0.2s ease; }
    .product-card:hover { transform: translateY(-4px); }
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;  
        overflow: hidden;
    }
    .page-link {
        font-size: 13px;
        padding: 6px 12px;
    }
</style>
<?= $this->endSection(); ?>