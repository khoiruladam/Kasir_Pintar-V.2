<?php
include '../../../fungsi/autentikasi.php';
cekLogin();

if (!isset($_SESSION['Level']) || !in_array(strtolower($_SESSION['Level']), ['administrator', 'owner'])) {
    echo "<script>alert('Akses Ditolak! Anda tidak memiliki izin.'); window.history.back();</script>";
    exit;
}

include '../../../config/koneksi.php';
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
        border-color: #6366f1;
        /* Warna Indigo/Ungu untuk Add */
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

    /* Modifikasi khusus input group dengan toggle mata di ujung kanan */
    .form-control-custom.with-icon-both {
        border-radius: 0;
        border-left: none;
        border-right: none;
        padding-left: 0;
    }

    .input-group-text-end {
        border-radius: 0 16px 16px 0;
        border: 1px solid #e2e8f0;
        border-left: none;
        background-color: #f8fafc;
        color: #94a3b8;
        cursor: pointer;
        transition: 0.3s;
    }

    .input-group-text-end:hover {
        color: #6366f1;
    }

    /* Fokus pada grup input */
    .form-control-custom:focus+.input-group-text-end,
    .input-group-text-custom:has(+ .form-control-custom:focus) {
        border-color: #6366f1;
        background-color: #fff;
    }

    /* Tombol Simpan Gradient (Warna Utama/Indigo) */
    .btn-simpan {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
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
        <button onclick="window.location.href='users.php'"
            class="btn btn-light rounded-circle p-2 shadow-sm me-3 d-flex justify-content-center align-items-center"
            style="width:42px; height:42px;">
            <i class="bi bi-arrow-left fs-5"></i>
        </button>
        <div>
            <h4 class="fw-extrabold mb-0 text-dark">Tambah Pengguna</h4>
            <small class="text-muted">Buat akses untuk staf baru</small>
        </div>
    </div>

    <!-- Form Container -->
    <div class="card-custom">
        <form action="../../../proses/proses_tambah_user.php" method="POST">

            <!-- Input Username -->
            <div class="mb-4">
                <label for="username" class="form-label-custom">Username</label>
                <div class="input-group">
                    <span class="input-group-text input-group-text-custom">
                        <i class="bi bi-person-badge"></i>
                    </span>
                    <input type="text" class="form-control form-control-custom with-icon" id="username" name="username"
                        placeholder="Ciptakan username tanpa spasi" required autocomplete="off">
                </div>
            </div>

            <!-- Input Password dengan Toggle Mata -->
            <div class="mb-4">
                <label for="password" class="form-label-custom">Password</label>
                <div class="input-group">
                    <span class="input-group-text input-group-text-custom">
                        <i class="bi bi-key"></i>
                    </span>
                    <input type="password" class="form-control form-control-custom with-icon-both" id="password"
                        name="password" placeholder="Ketik kata sandi" required>
                    <span class="input-group-text input-group-text-end" id="togglePassword">
                        <i class="bi bi-eye-slash" id="eyeIcon"></i>
                    </span>
                </div>
            </div>

            <!-- Input Level/Role -->
            <div class="mb-5">
                <label for="level" class="form-label-custom">Hak Akses (Role)</label>
                <div class="input-group">
                    <span class="input-group-text input-group-text-custom">
                        <i class="bi bi-shield-check"></i>
                    </span>
                    <select class="form-select form-control-custom with-icon" id="level" name="level" required>
                        <option value="" disabled selected>-- Pilih Jabatan --</option>
                        <option value="petugas">Petugas / Kasir</option>
                        <option value="administrator">Administrator</option>
                        <option value="manager">Manager</option>
                        <option value="owner">Owner (Pemilik)</option>
                    </select>
                </div>
            </div>

            <!-- Tombol Submit -->
            <button type="submit" class="btn-simpan shadow-lg">
                <i class="bi bi-person-plus-fill me-2"></i> Daftarkan Pengguna
            </button>

        </form>
    </div>

</div>

<script>
    document.getElementById('togglePassword').addEventListener('click', function (e) {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        const isPassword = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

        eyeIcon.classList.toggle('bi-eye-slash');
        eyeIcon.classList.toggle('bi-eye');
    });
</script>

<?php include '../../template/footer.php'; ?>