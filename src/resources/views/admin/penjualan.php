<?php
// Pastikan path ../ disesuaikan dengan posisi file ini
include '../../../fungsi/autentikasi.php';
cekLogin();
include '../../../config/koneksi.php';

// PERBAIKAN LOGIKA: Menggunakan PDO, bukan mysqli
try {
    // Ambil data produk
    $query_produk = "SELECT ProdukID, NamaProduk, Harga, Stok FROM produk WHERE Stok > 0";
    $stmt_produk = $koneksi->query($query_produk);
    $produk_list = $stmt_produk->fetchAll(PDO::FETCH_ASSOC);

    // Ambil data pelanggan
    $query_pelanggan = "SELECT PelangganID, NamaPelanggan FROM pelanggan WHERE PelangganID != 1";
    $stmt_pelanggan = $koneksi->query($query_pelanggan);
    $pelanggan_list = $stmt_pelanggan->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage());
}

include '../../template/header.php';
?>

<style>
    /* UI Premium Ala Aplikasi Mobile */
    body {
        background-color: #f4f7fe; 
    }
    
    .app-container {
        max-width: 600px;
        margin: 0 auto;
        /* PERBAIKAN: Padding bottom diperbesar agar keranjang tidak terpotong footer */
        padding-bottom: 200px; 
    }

    .card-custom {
        background: #ffffff;
        border-radius: 24px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
    }

    .form-control-custom, .form-select-custom {
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 12px 15px;
        font-size: 0.95rem;
        box-shadow: none !important;
        transition: all 0.3s ease;
    }

    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #6366f1;
        background-color: #fff;
    }

    .btn-add {
        background: #e0e7ff;
        color: #4f46e5;
        border-radius: 16px;
        font-weight: 700;
        border: none;
        padding: 12px;
        transition: 0.2s;
    }
    .btn-add:hover { background: #c7d2fe; color: #4338ca; }

    /* Cart Item Card */
    .cart-item {
        background: #fff;
        border-radius: 20px;
        padding: 15px;
        margin-bottom: 15px;
        border: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }

    .price-badge {
        background: #f5f3ff;
        color: #7c3aed;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 12px;
        font-size: 0.85rem;
    }

    .qty-control {
        display: flex;
        align-items: center;
        background: #f8fafc;
        border-radius: 12px;
        padding: 4px;
    }

    .btn-qty {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        border: none;
        background: #fff;
        color: #333;
        font-weight: bold;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .qty-number {
        width: 30px;
        text-align: center;
        font-weight: 700;
        font-size: 0.9rem;
    }

    /* PERBAIKAN: Fixed Bottom Checkout Dibuat Lebih Ringkas */
    .checkout-footer {
        position: fixed;
        bottom: 80px; /* Menyesuaikan tinggi bottom nav header.php */
        left: 50%;
        transform: translateX(-50%);
        width: 100%;
        max-width: 600px;
        background: #fff;
        padding: 15px 20px; /* Padding dikurangi */
        border-radius: 24px 24px 0 0; /* Radius diperkecil sedikit */
        box-shadow: 0 -5px 20px rgba(0,0,0,0.05); /* Shadow diperhalus */
        z-index: 1000;
    }

    /* PERBAIKAN: Tombol Checkout Diperkecil */
    .btn-checkout {
        background: #6366f1; 
        color: #fff;
        border-radius: 16px; /* Radius lebih kecil */
        padding: 12px 16px; /* Padding dikurangi */
        font-size: 0.95rem; /* Font lebih kecil */
        font-weight: 700;
        border: none;
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .btn-checkout:disabled { background: #cbd5e1; color: #f8fafc; }
</style>

<div class="app-container mt-4 px-3">
    
    <!-- Header -->
    <div class="d-flex align-items-center mb-4">
        <button onclick="history.back()" class="btn btn-light rounded-circle p-2 shadow-sm me-3" style="width:40px; height:40px;">
            <i class="bi bi-arrow-left"></i>
        </button>
        <h4 class="fw-bold mb-0">Keranjang Kasir</h4>
    </div>

    <!-- Form Transaksi -->
    <form action="../../../proses/proses_penjualan.php" method="POST" id="form-transaksi">
        
        <!-- Pemilihan Input -->
        <div class="card-custom p-4 mb-4">
            <h6 class="fw-bold text-muted mb-3"><i class="bi bi-person me-2"></i>Data Pelanggan</h6>
            <select name="pelanggan_id" class="form-select form-select-custom mb-4">
                <option value="1" selected>Pelanggan Umum (Cash)</option>
                <?php foreach($pelanggan_list as $row): ?>
                    <option value="<?= $row['PelangganID']; ?>">
                        <?= htmlspecialchars($row['NamaPelanggan']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <h6 class="fw-bold text-muted mb-3"><i class="bi bi-box-seam me-2"></i>Pilih Produk</h6>
            <div class="row g-2 mb-2">
                <div class="col-8">
                    <select id="produk_input" class="form-select form-select-custom">
                        <option value="">Cari produk...</option>
                        <?php foreach($produk_list as $row): ?>
                            <option value="<?= $row['ProdukID']; ?>"
                                    data-harga="<?= $row['Harga']; ?>"
                                    data-nama="<?= htmlspecialchars($row['NamaProduk']); ?>"
                                    data-stok="<?= $row['Stok']; ?>">
                                <?= htmlspecialchars($row['NamaProduk']); ?> (Sisa: <?= $row['Stok']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-4">
                    <button type="button" class="btn-add w-100 h-100" id="tambah-keranjang">
                        <i class="bi bi-plus-lg me-1"></i> Add
                    </button>
                </div>
            </div>
        </div>

        <!-- Keranjang List (Pengganti Tabel) -->
        <h6 class="fw-bold px-2 mb-3">Item Pesanan</h6>
        <div id="keranjang-list">
            <div class="text-center text-muted my-5 py-3" id="empty-state">
                <i class="bi bi-cart-x fs-1 text-light mb-2"></i>
                <p>Keranjang masih kosong</p>
            </div>
        </div>

        <!-- Input Hidden -->
        <div id="keranjang-inputs"></div>
        <input type="hidden" name="total_harga" id="total_harga">
        <div class="checkout-footer">
            <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                <span class="text-muted fw-bold" style="font-size: 0.85rem;">Total Pembayaran</span>
                <h5 class="fw-extrabold text-dark mb-0" id="total-harga-display">Rp 0</h5>
            </div>
            <button type="submit" class="btn-checkout shadow-sm" id="simpan-transaksi" disabled>
                <span>Proses Transaksi</span>
                <i class="bi bi-arrow-right-circle-fill fs-5"></i>
            </button>
        </div>

    </form>
</div>

<?php include '../../template/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let keranjang = {};

    function hitungTotal() {
        let total = 0;
        let count = 0;
        for (const id in keranjang) {
            total += keranjang[id].subtotal;
            count++;
        }
        
        $('#total-harga-display').text('Rp ' + total.toLocaleString('id-ID'));
        $('#total_harga').val(total);

        if (count > 0) {
            $('#simpan-transaksi').removeAttr('disabled');
            $('#empty-state').hide();
        } else {
            $('#simpan-transaksi').attr('disabled', 'disabled');
            $('#empty-state').show();
        }
    }

    function renderKeranjang() {
        $('#keranjang-list .cart-item').remove();
        $('#keranjang-inputs').empty();

        for (const id in keranjang) {
            const item = keranjang[id];
            
            // Render UI Card App-style
            const card = `
            <div class="cart-item">
                <div class="d-flex flex-column flex-grow-1 me-2">
                    <span class="fw-bold mb-2 text-dark" style="font-size:1.05rem;">${item.nama}</span>
                    <span class="price-badge d-inline-block" style="width: fit-content;">Rp ${item.harga.toLocaleString('id-ID')}</span>
                </div>
                
                <div class="d-flex flex-column align-items-end">
                    <button type="button" class="btn btn-link text-danger p-0 mb-2 hapus-keranjang" data-id="${item.id}">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                    
                    <div class="qty-control">
                        <button type="button" class="btn-qty min-qty" data-id="${item.id}">-</button>
                        <span class="qty-number">${item.jumlah}</span>
                        <button type="button" class="btn-qty plus-qty" data-id="${item.id}">+</button>
                    </div>
                </div>
            </div>
            `;
            $('#keranjang-list').append(card);

            // Render hidden input untuk PHP
            const inputHidden = `
                <input type="hidden" name="produk_id[]" value="${item.id}">
                <input type="hidden" name="jumlah[]" value="${item.jumlah}">
            `;
            $('#keranjang-inputs').append(inputHidden);
        }
        hitungTotal();
    }

    // Fungsi Tambah
    $('#tambah-keranjang').click(function() {
        const produkInput = $('#produk_input');
        const produkID = produkInput.val();
        
        if (!produkID) {
            alert('Pilih produk terlebih dahulu!');
            return;
        }

        const harga = parseFloat(produkInput.find('option:selected').data('harga'));
        const nama = produkInput.find('option:selected').data('nama');
        const stok = parseInt(produkInput.find('option:selected').data('stok'));

        if (keranjang[produkID]) {
            if (keranjang[produkID].jumlah + 1 > stok) {
                alert('Stok tidak mencukupi!');
                return;
            }
            keranjang[produkID].jumlah += 1;
            keranjang[produkID].subtotal = keranjang[produkID].jumlah * harga;
        } else {
            keranjang[produkID] = {
                id: produkID, nama: nama, harga: harga, jumlah: 1, stok: stok, subtotal: harga
            };
        }
        
        // Reset pilihan produk
        produkInput.val(''); 
        renderKeranjang();
    });

    // Fungsi Tambah QTY (+)
    $(document).on('click', '.plus-qty', function() {
        const id = $(this).data('id');
        if (keranjang[id].jumlah + 1 > keranjang[id].stok) {
            alert('Maksimal stok tercapai!');
            return;
        }
        keranjang[id].jumlah += 1;
        keranjang[id].subtotal = keranjang[id].jumlah * keranjang[id].harga;
        renderKeranjang();
    });

    // Fungsi Kurang QTY (-)
    $(document).on('click', '.min-qty', function() {
        const id = $(this).data('id');
        if (keranjang[id].jumlah - 1 === 0) {
            delete keranjang[id]; // Hapus jika jumlah 0
        } else {
            keranjang[id].jumlah -= 1;
            keranjang[id].subtotal = keranjang[id].jumlah * keranjang[id].harga;
        }
        renderKeranjang();
    });

    // Fungsi Hapus Tong Sampah
    $(document).on('click', '.hapus-keranjang', function() {
        const id = $(this).data('id');
        delete keranjang[id];
        renderKeranjang();
    });
});
</script>