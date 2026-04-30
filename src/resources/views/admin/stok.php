<?php
include '../../../fungsi/autentikasi.php';
cekLogin();

$level_user = strtolower($_SESSION['Level'] ?? '');
if (!in_array($level_user, ['administrator', 'owner', 'manager'])) {
    echo "<script>alert('Akses Ditolak!'); window.history.back();</script>";
    exit;
}

include '../../../config/koneksi.php';

try {
    $query_stok = "SELECT ProdukID, NamaProduk, Harga, Stok FROM produk ORDER BY Stok ASC";
    $stmt = $koneksi->query($query_stok);
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
        max-width: 600px;
        /* Standar Mobile-First */
        margin: 0 auto;
        padding-bottom: 120px;
    }

    /* Product Card */
    .stok-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 18px;
        margin-bottom: 15px;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        transition: transform 0.2s;
    }

    .stok-card:active {
        transform: scale(0.98);
    }

    .stok-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border-bottom: 1px dashed #e2e8f0;
        padding-bottom: 12px;
        margin-bottom: 15px;
    }

    .stok-info h6 {
        margin: 0 0 5px;
        font-weight: 800;
        color: #1e293b;
        font-size: 1.05rem;
    }

    .stok-info small {
        color: #94a3b8;
        font-size: 0.8rem;
    }

    /* Indikator Stok Cerdas */
    .badge-stok {
        padding: 6px 12px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .stok-aman {
        background: #dcfce7;
        color: #16a34a;
    }

    /* Hijau */
    .stok-kritis {
        background: #fee2e2;
        color: #ef4444;
    }

    /* Merah */

    /* Area Form Input di dalam Card */
    .form-update-stok {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .input-stok-custom {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        padding: 10px 15px;
        font-size: 0.95rem;
        box-shadow: none !important;
        background-color: #f8fafc;
        width: 100%;
        transition: 0.3s;
    }

    .input-stok-custom:focus {
        border-color: #f59e0b;
        /* Warna Amber/Kuning */
        background-color: #fff;
    }

    .btn-update {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        border-radius: 14px;
        padding: 10px 20px;
        font-weight: 700;
        border: none;
        white-space: nowrap;
        transition: 0.3s;
    }
</style>

<div class="app-container mt-4 px-3">

    <!-- Header Navigation -->
    <div class="d-flex align-items-center mb-4">
        <button onclick="history.back()"
            class="btn btn-light rounded-circle p-2 shadow-sm me-3 d-flex justify-content-center align-items-center"
            style="width:42px; height:42px;">
            <i class="bi bi-arrow-left fs-5"></i>
        </button>
        <div>
            <h4 class="fw-extrabold mb-0 text-dark">Update Stok</h4>
            <small class="text-muted">Total: <?= count($produk_list); ?> Barang</small>
        </div>
    </div>

    <!-- Daftar Produk -->
    <div class="stok-grid">
        <?php if (count($produk_list) > 0): ?>
            <?php foreach ($produk_list as $row):
                $stok_saat_ini = (int) $row['Stok'];
                if ($stok_saat_ini < 10) {
                    $badge_class = 'stok-kritis';
                    $icon_stok = '<i class="bi bi-exclamation-triangle-fill"></i>';
                } else {
                    $badge_class = 'stok-aman';
                    $icon_stok = '<i class="bi bi-box-seam-fill"></i>';
                }
                ?>
                <div class="stok-card shadow-sm">

                    <!-- Informasi Barang -->
                    <div class="stok-header">
                        <div class="stok-info">
                            <h6><?= htmlspecialchars($row['NamaProduk']); ?></h6>
                            <small>ID: #<?= htmlspecialchars($row['ProdukID']); ?> | Rp
                                <?= number_format($row['Harga'], 0, ',', '.'); ?></small>
                        </div>

                        <!-- Lencana Stok -->
                        <div class="badge-stok <?= $badge_class; ?>">
                            <?= $icon_stok; ?> Stok: <?= $stok_saat_ini; ?>
                        </div>
                    </div>

                    <!-- Form Update Stok (Kini Legal & Rapi secara HTML) -->
                    <form action="../../../proses/proses_stok.php" method="POST" class="form-update-stok">
                        <input type="hidden" name="id_produk" value="<?= htmlspecialchars($row['ProdukID']); ?>">

                        <input type="number" name="tambah_stok" class="input-stok-custom form-control"
                            placeholder="+ Jml Tambah" min="1" required autocomplete="off">

                        <button type="submit" class="btn-update shadow-sm">
                            <i class="bi bi-plus-circle me-1"></i> Update
                        </button>
                    </form>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center my-5 py-5 w-100">
                <i class="bi bi-inboxes text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                <h5 class="mt-3 fw-bold text-muted">Belum Ada Produk</h5>
                <p class="text-muted small">Tambahkan produk terlebih dahulu di menu Master Produk.</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php include '../../template/footer.php'; ?>