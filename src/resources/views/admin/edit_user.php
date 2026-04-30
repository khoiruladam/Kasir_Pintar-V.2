<?php
include '../../../fungsi/autentikasi.php';
cekLogin();

if (!isset($_SESSION['Level']) || !in_array(strtolower($_SESSION['Level']), ['administrator', 'owner'])) {
    echo "<script>alert('Akses Ditolak! Anda tidak memiliki izin.'); window.history.back();</script>";
    exit;
}

include '../../../config/koneksi.php';

// Validasi dan Ambil ID User dari URL
$id_user = $_GET['id'] ?? 0;

try {
    $stmt = $koneksi->prepare("SELECT UserID, Username, Level FROM user WHERE UserID = ?");
    $stmt->execute([$id_user]);
    $data_user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data_user) {
        echo "<script>alert('Data pengguna tidak ditemukan!'); window.location.href='users.php';</script>";
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
        border-color: #f59e0b;
        /* Warna kuning/oranye untuk Edit */
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
        <button onclick="window.location.href='user.php'"
            class="btn btn-light rounded-circle p-2 shadow-sm me-3 d-flex justify-content-center align-items-center"
            style="width:42px; height:42px;">
            <i class="bi bi-arrow-left fs-5"></i>
        </button>
        <div>
            <h4 class="fw-extrabold mb-0 text-dark">Edit Pengguna</h4>
            <small class="text-muted">ID: #<?= htmlspecialchars($data_user['UserID']); ?></small>
        </div>
    </div>

    <!-- Form Container -->
    <div class="card-custom">
        <form action="../../../proses/proses_edit_user.php" method="POST">
            
            <input type="hidden" name="id" value="<?= htmlspecialchars($data_user['UserID']); ?>">

            <!-- Input Username -->
            <div class="mb-4">
                <label for="username" class="form-label-custom">Username</label>
                <div class="input-group">
                    <span class="input-group-text input-group-text-custom">
                        <i class="bi bi-person-fill"></i>
                    </span>
                    <input type="text" class="form-control form-control-custom with-icon" id="username" name="username"
                        value="<?= htmlspecialchars($data_user['Username']); ?>" required autocomplete="off">
                </div>
            </div>

            <!-- Input Level/Role -->
            <div class="mb-5">
                <label for="level" class="form-label-custom">Hak Akses (Role)</label>
                <div class="input-group">
                    <span class="input-group-text input-group-text-custom">
                        <i class="bi bi-shield-lock-fill"></i>
                    </span>
                    <?php $level_aktif = strtolower($data_user['Level']); ?>
                    <select class="form-select form-control-custom with-icon" id="level" name="level" required>
                        <option value="owner" <?= ($level_aktif == 'owner') ? 'selected' : ''; ?>>Owner (Pemilik)</option>
                        <option value="manager" <?= ($level_aktif == 'manager') ? 'selected' : ''; ?>>Manager</option>
                        <option value="administrator" <?= ($level_aktif == 'administrator') ? 'selected' : ''; ?>>
                            Administrator</option>
                        <option value="petugas" <?= ($level_aktif == 'petugas' || $level_aktif == 'kasir') ? 'selected' : ''; ?>>Petugas / Kasir</option>
                    </select>
                </div>
            </div>

            <!-- Tombol Submit -->
            <button type="submit" class="btn-simpan shadow-lg">
                <i class="bi bi-cloud-arrow-up-fill me-2"></i> Update Data Pengguna
            </button>

        </form>
    </div>

</div>

<?php include '../../template/footer.php'; ?>