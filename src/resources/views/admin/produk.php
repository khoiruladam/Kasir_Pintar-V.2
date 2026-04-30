<?php
include '../../../fungsi/autentikasi.php';
cekLogin();
include '../../../config/koneksi.php';

$search_query = $_GET['search'] ?? '';

try {
    if (!empty($search_query)) {
        $query = "SELECT ProdukID, NamaProduk, Harga, Stok FROM produk WHERE NamaProduk LIKE ?";
        $stmt = $koneksi->prepare($query);
        $search_term = "%" . $search_query . "%";
        $stmt->execute([$search_term]);
    } else {
        $query = "SELECT ProdukID, NamaProduk, Harga, Stok FROM produk ORDER BY ProdukID DESC";
        $stmt = $koneksi->query($query);
    }

    $produk_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage());
}

include '../../template/header.php';
?>

<style>
    body {
        background-color: #f4f7fe;
    }

    .app-container {
        max-width: 800px;
        /* Lebar optimal untuk HP dan Tablet */
        margin: 0 auto;
        padding-bottom: 120px;
        /* Ruang untuk bottom nav */
    }

    /* Styling Input Pencarian ala Aplikasi */
    .search-box {
        position: relative;
        margin-bottom: 25px;
    }

    .search-box .form-control {
        border-radius: 20px;
        padding: 14px 20px 14px 45px;
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        font-size: 0.95rem;
    }

    .search-box .bi-search {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    /* Styling Card Produk (Pengganti Tabel) */
    .product-card {
        background: #fff;
        border-radius: 20px;
        padding: 18px;
        margin-bottom: 15px;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        display: flex;
        flex-direction: column;
        transition: transform 0.2s;
    }

    .product-card:active {
        transform: scale(0.98);
    }

    .product-info {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .price-tag {
        color: #6366f1;
        font-weight: 800;
        font-size: 1.1rem;
    }

    .stock-badge {
        background: #f1f5f9;
        color: #475569;
        padding: 5px 12px;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .stock-badge.low {
        background: #fee2e2;
        color: #ef4444;
    }

    /* Tombol Aksi */
    .action-buttons {
        display: flex;
        gap: 10px;
        border-top: 1px dashed #e2e8f0;
        padding-top: 15px;
    }

    .btn-action {
        flex: 1;
        border-radius: 12px;
        padding: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        justify-content: center;
        align-items: center;
        text-decoration: none;
    }

    .btn-edit {
        background: #fffbeb;
        color: #d97706;
        border: 1px solid #fde68a;
    }

    .btn-delete {
        background: #fef2f2;
        color: #ef4444;
        border: 1px solid #fecaca;
    }

    /* FAB (Floating Action Button) untuk Tambah Produk */
    .fab-add {
        position: fixed;
        bottom: 90px;
        /* Di atas bottom nav */
        right: 20px;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 24px;
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
        z-index: 1000;
        transition: 0.3s;
    }

    /* Desktop/Tablet Hover adjustment */
    @media (min-width: 768px) {
        .product-list-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .fab-add {
            right: 50px;
            bottom: 50px;
        }
    }
</style>

<div class="app-container mt-4 px-3">

    <?php
    $pesan = $_GET['pesan'] ?? '';
    if ($pesan) {
        $alert_class = ($pesan == 'hapus_sukses') ? 'alert-warning' : 'alert-success';
        $alert_text = '';
        if ($pesan == 'sukses')
            $alert_text = 'Produk berhasil ditambahkan!';
        if ($pesan == 'edit_sukses')
            $alert_text = 'Data produk berhasil diperbarui!';
        if ($pesan == 'hapus_sukses')
            $alert_text = 'Produk berhasil dihapus!';

        echo "<div class='alert {$alert_class} alert-dismissible fade show rounded-4 border-0 shadow-sm' role='alert'>
                <i class='bi bi-info-circle-fill me-2'></i> {$alert_text}
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
    }
    ?>

    <!-- Header & Back Button -->
    <div class="d-flex align-items-center mb-4">
        <button onclick="history.back()" class="btn btn-light rounded-circle p-2 shadow-sm me-3"
            style="width:40px; height:40px;">
            <i class="bi bi-arrow-left"></i>
        </button>
        <div>
            <h4 class="fw-extrabold mb-0 text-dark">Data Produk</h4>
            <small class="text-muted">Total: <?= count($produk_list); ?> Item</small>
        </div>
    </div>

    <!-- Search Box -->
    <form action="" method="GET">
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="search" class="form-control" name="search" placeholder="Cari nama produk..."
                value="<?= htmlspecialchars($search_query); ?>">
            <button type="submit" class="d-none"></button>
        </div>
    </form>

    <!-- Daftar Produk (Grid/List) -->
    <div class="product-list-grid">
        <?php if (count($produk_list) > 0): ?>
            <?php foreach ($produk_list as $row): ?>
                <div class="product-card">
                    <div class="product-info">
                        <div>
                            <h6 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($row['NamaProduk']); ?></h6>
                            <div class="price-tag">Rp <?= number_format($row['Harga'], 0, ',', '.'); ?></div>
                        </div>

                        <?php
                        $stock_class = ($row['Stok'] < 10) ? 'low' : '';
                        $stock_text = ($row['Stok'] == 0) ? 'Habis' : $row['Stok'] . ' Pcs';
                        ?>
                        <span class="stock-badge <?= $stock_class; ?>">
                            <i class="bi bi-box-seam me-1"></i> <?= $stock_text; ?>
                        </span>
                    </div>

                    <!-- Tombol Aksi (Hanya Admin) -->
                    <?php if (isset($_SESSION['Level']) && $_SESSION['Level'] == 'administrator'): ?>
                        <div class="action-buttons">
                            <a href="edit_produk.php?id=<?= $row['ProdukID']; ?>" class="btn-action btn-edit">
                                <i class="bi bi-pencil-square me-2"></i> Edit
                            </a>
                            <a href="../../../proses/proses_hapus_produk.php?id=<?= $row['ProdukID']; ?>"
                                class="btn-action btn-delete tombol-hapus-sweet"
                                data-nama="<?= htmlspecialchars($row['NamaProduk']); ?>">
                                <i class="bi bi-trash me-2"></i> Hapus
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center my-5 py-5 w-100" style="grid-column: span 2;">
                <i class="bi bi-box-seam text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                <h5 class="mt-3 fw-bold text-muted">Produk Tidak Ditemukan</h5>
                <p class="text-muted small">Coba kata kunci pencarian yang lain.</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php if (isset($_SESSION['Level']) && $_SESSION['Level'] == 'administrator'): ?>
    <a href="tambah_produk.php" class="fab-add" title="Tambah Produk">
        <i class="bi bi-plus"></i>
    </a>
<?php endif; ?>
<?php include '../../template/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tombolHapus = document.querySelectorAll('.tombol-hapus-sweet');

    tombolHapus.forEach(tombol => {
        tombol.addEventListener('click', function(e) {
            e.preventDefault();

            const linkHapus = this.getAttribute('href'); 
            const namaProduk = this.getAttribute('data-nama');

            Swal.fire({
                title: 'Hapus Produk?',
                html: `Yakin ingin menghapus <b>${namaProduk}</b> dari daftar?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8', 
                confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-4 shadow-lg border-0',
                    confirmButton: 'rounded-3 shadow-sm px-4',
                    cancelButton: 'rounded-3 shadow-sm px-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = linkHapus;
                }
            });
        });
    });
});
</script>