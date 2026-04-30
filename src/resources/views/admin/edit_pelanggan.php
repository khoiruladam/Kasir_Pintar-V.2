<?php
include '../../../fungsi/autentikasi.php';
cekLogin();

$level_user = strtolower($_SESSION['Level'] ?? '');
if ($level_user != 'petugas' && $level_user != 'kasir') {
    echo "<script>alert('Akses Ditolak!'); window.history.back();</script>";
    exit;
}

include '../../../config/koneksi.php';

$id_pelanggan = intval($_GET['id'] ?? 0);

if ($id_pelanggan === 1) {
    echo "<script>alert('Data Pelanggan Umum tidak boleh diedit!'); window.location.href='pelanggan.php';</script>";
    exit;
}

try {
    $stmt = $koneksi->prepare("SELECT * FROM pelanggan WHERE PelangganID = ?");
    $stmt->execute([$id_pelanggan]);
    $data_pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data_pelanggan) {
        echo "<script>alert('Data member tidak ditemukan!'); window.location.href='pelanggan.php';</script>";
        exit;
    }
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
        /* Lebar optimal Mobile & Tablet */
        margin: 0 auto;
        padding-bottom: 100px;
    }

    /* UI Card Form */
    .card-custom {
        background: #ffffff;
        border-radius: 24px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        padding: 30px 25px;
    }

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
        border-color: #f59e0b;
        /* Warna Amber/Kuning untuk Edit */
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

    textarea.form-control-custom {
        border-radius: 16px;
        padding-left: 18px;
    }

    .input-group-text-custom:has(+ .form-control-custom:focus) {
        border-color: #f59e0b;
        background-color: #fff;
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
        <button onclick="window.location.href='pelanggan.php'"
            class="btn btn-light rounded-circle p-2 shadow-sm me-3 d-flex justify-content-center align-items-center"
            style="width:42px; height:42px;">
            <i class="bi bi-arrow-left fs-5"></i>
        </button>
        <div>
            <h4 class="fw-extrabold mb-0 text-dark">Edit Member</h4>
            <small class="text-muted">Perbarui data pelanggan setia</small>
        </div>
    </div>

    <!-- Form Container -->
    <div class="card-custom">

        <form action="../../../proses/proses_edit_pelanggan.php" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($data_pelanggan['PelangganID']); ?>">
            <div class="mb-4">
                <label for="nama_pelanggan" class="form-label-custom">Nama Lengkap</label>
                <div class="input-group">
                    <span class="input-group-text input-group-text-custom">
                        <i class="bi bi-person-vcard"></i>
                    </span>
                    <input type="text" class="form-control form-control-custom with-icon" id="nama_pelanggan"
                        name="nama_pelanggan" value="<?= htmlspecialchars($data_pelanggan['NamaPelanggan']); ?>"
                        required autocomplete="off">
                </div>
            </div>

            <!-- Input Nomor Telepon/WA -->
            <div class="mb-4">
                <label for="nomor_telepon" class="form-label-custom">Nomor WhatsApp / Telepon</label>
                <div class="input-group">
                    <span class="input-group-text input-group-text-custom">
                        <i class="bi bi-telephone"></i>
                    </span>
                    <input type="number" class="form-control form-control-custom with-icon" id="nomor_telepon"
                        name="nomor_telepon" value="<?= htmlspecialchars($data_pelanggan['NomorTelepon']); ?>"
                        autocomplete="off">
                </div>
            </div>

            <!-- Input Alamat -->
            <div class="mb-5">
                <label for="alamat" class="form-label-custom">Alamat Lengkap</label>
                <textarea class="form-control form-control-custom" id="alamat" name="alamat" rows="3"
                    autocomplete="off"><?= htmlspecialchars($data_pelanggan['Alamat']); ?></textarea>
            </div>

            <!-- Tombol Submit -->
            <button type="submit" class="btn-simpan shadow-lg">
                <i class="bi bi-cloud-arrow-up-fill me-2"></i> Update Data Member
            </button>

        </form>
    </div>

</div>

<?php include '../../template/footer.php'; ?>