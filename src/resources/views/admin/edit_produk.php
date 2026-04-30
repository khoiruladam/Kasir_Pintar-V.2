<?php
include '../../../fungsi/autentikasi.php';
cekLogin();

if (!isset($_SESSION['Level']) || $_SESSION['Level'] != 'administrator') {
    header('Location: produk.php');
    exit;
}

include '../../../config/koneksi.php';

$id_produk = $_GET['id'] ?? 0;

try {
    $stmt = $koneksi->prepare("SELECT * FROM produk WHERE ProdukID = ?");
    $stmt->execute([$id_produk]);
    $data_produk = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data_produk) {
        echo "<script>alert('Data produk tidak ditemukan!'); window.location.href='produk.php';</script>";
        exit;
    }
} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage());
}

include '../../template/header.php';
?>

<style>
    body { background-color: #f4f7fe; }
    
    .app-container {
        max-width: 600px; /* Lebar optimal untuk Mobile & Tablet */
        margin: 0 auto;
        padding-bottom: 100px;
    }

    /* UI Card Form */
    .card-custom {
        background: #ffffff;
        border-radius: 24px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        padding: 30px 25px;
    }

    /* Styling Label & Input Premium */
    .form-label-custom {
        font-weight: 700;
        color: #475569;
        margin-bottom: 10px;
        font-size: 0.95rem;
    }

    .form-control-custom {
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 14px 18px;
        font-size: 0.95rem;
        box-shadow: none !important;
        transition: all 0.3s ease;
        background-color: #f8fafc;
    }

    .form-control-custom:focus {
        border-color: #f59e0b; /* Warna kuning/oranye untuk Edit */
        background-color: #fff;
    }

    /* Input Group (Icon + Input) */
    .input-group-text-custom {
        border-radius: 16px 0 0 16px;
        border: 1px solid #e2e8f0;
        border-right: none;
        background-color: #f8fafc;
        color: #94a3b8;
        padding-left: 18px;
        padding-right: 15px;
    }

    .form-control-custom.with-icon {
        border-radius: 0 16px 16px 0;
        border-left: none;
        padding-left: 0;
    }

    .form-control-custom.with-icon:focus {
        border-left: none;
    }
    
    /* Tombol Simpan Gradient Edit (Warna Warning/Kuning) */
    .btn-simpan {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        border-radius: 16px;
        padding: 14px;
        font-weight: 700;
        font-size: 1.05rem;
        border: none;
        width: 100%;
        transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .btn-simpan:active { 
        transform: scale(0.97); 
    }
</style>

<div class="app-container mt-4 px-3">

    <!-- Header Navigation -->
    <div class="d-flex align-items-center mb-4">
        <button onclick="history.back()" class="btn btn-light rounded-circle p-2 shadow-sm me-3 d-flex justify-content-center align-items-center" style="width:42px; height:42px;">
            <i class="bi bi-arrow-left fs-5"></i>
        </button>
        <div>
            <h4 class="fw-extrabold mb-0 text-dark">Edit Produk</h4>
            <small class="text-muted">Perbarui detail informasi barang</small>
        </div>
    </div>

    <!-- Form Container -->
    <div class="card-custom">
        <form action="../../../proses/proses_edit_produk.php" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($data_produk['ProdukID']); ?>">

            <!-- Input Nama Produk -->
            <div class="mb-4">
                <label for="nama_produk" class="form-label-custom">Nama Produk</label>
                <div class="input-group">
                    <span class="input-group-text input-group-text-custom">
                        <i class="bi bi-box-seam"></i>
                    </span>
                    <input type="text" class="form-control form-control-custom with-icon" id="nama_produk" name="nama_produk" value="<?= htmlspecialchars($data_produk['NamaProduk']); ?>" required autocomplete="off">
                </div>
            </div>

            <!-- Input Harga -->
            <div class="mb-4">
                <label for="harga" class="form-label-custom">Harga Jual</label>
                <div class="input-group">
                    <span class="input-group-text input-group-text-custom fw-bold text-dark">
                        Rp
                    </span>
                    <input type="number" class="form-control form-control-custom with-icon" id="harga" name="harga" value="<?= htmlspecialchars($data_produk['Harga']); ?>" min="0" required>
                </div>
            </div>

            <!-- Input Stok -->
            <div class="mb-5">
                <label for="stok" class="form-label-custom">Sisa Stok</label>
                <div class="input-group">
                    <span class="input-group-text input-group-text-custom">
                        <i class="bi bi-layers"></i>
                    </span>
                    <input type="number" class="form-control form-control-custom with-icon" id="stok" name="stok" value="<?= htmlspecialchars($data_produk['Stok']); ?>" min="0" required>
                </div>
            </div>

            <!-- Tombol Submit -->
            <button type="submit" class="btn-simpan shadow-lg">
                <i class="bi bi-cloud-arrow-up-fill me-2"></i> Update Data Produk
            </button>
            
        </form>
    </div>

</div>

<?php include '../../template/footer.php'; ?>