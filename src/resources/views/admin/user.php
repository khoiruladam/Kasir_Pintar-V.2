<?php
include '../../../fungsi/autentikasi.php';
cekLogin();

if (!isset($_SESSION['Level']) || !in_array(strtolower($_SESSION['Level']), ['administrator', 'owner'])) {
    echo "<script>alert('Akses Ditolak! Anda bukan Admin/Owner.'); window.history.back();</script>";
    exit;
}

include '../../../config/koneksi.php';

$search_query = $_GET['search'] ?? '';

try {
    if (!empty($search_query)) {
        $query = "SELECT UserID, Username, Level FROM user WHERE Username LIKE ?";
        $stmt = $koneksi->prepare($query);
        $search_term = "%" . $search_query . "%";
        $stmt->execute([$search_term]);
    } else {
        $query = "SELECT UserID, Username, Level FROM user ORDER BY UserID DESC";
        $stmt = $koneksi->query($query);
    }

    $user_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        /* Ukuran Mobile/Tablet */
        margin: 0 auto;
        padding-bottom: 120px;
    }

    /* Styling Input Pencarian */
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

    /* User Card (Pengganti Tabel) */
    .user-card {
        background: #fff;
        border-radius: 24px;
        padding: 20px;
        margin-bottom: 15px;
        border: none;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        display: flex;
        flex-direction: column;
        transition: transform 0.2s;
    }

    .user-card:active {
        transform: scale(0.98);
    }

    .user-info {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .user-avatar {
        width: 55px;
        height: 55px;
        border-radius: 18px;
        margin-right: 15px;
        object-fit: cover;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }

    /* Lencana Warna Dinamis Berdasarkan Role */
    .role-badge {
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .role-admin {
        background: #e0e7ff;
        color: #4f46e5;
    }

    /* Indigo */
    .role-owner {
        background: #fef3c7;
        color: #d97706;
    }

    /* Amber */
    .role-manager {
        background: #fce7f3;
        color: #db2777;
    }

    /* Pink */
    .role-kasir {
        background: #dcfce7;
        color: #16a34a;
    }

    /* Emerald */
    .role-default {
        background: #f1f5f9;
        color: #475569;
    }

    /* Slate */

    /* Tombol Aksi */
    .action-buttons {
        display: flex;
        gap: 10px;
        border-top: 1px dashed #e2e8f0;
        padding-top: 15px;
    }

    .btn-action {
        flex: 1;
        border-radius: 14px;
        padding: 10px;
        font-weight: 700;
        font-size: 0.9rem;
        display: flex;
        justify-content: center;
        align-items: center;
        text-decoration: none;
        transition: 0.2s;
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

    /* FAB (Floating Action Button) */
    .fab-add {
        position: fixed;
        bottom: 90px;
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
    }

    @media (min-width: 768px) {
        .fab-add {
            right: 50px;
            bottom: 50px;
        }

        .user-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
    }
</style>

<div class="app-container mt-4 px-3">

    <!-- Header & Back Button -->
    <div class="d-flex align-items-center mb-4">
        <button onclick="history.back()" class="btn btn-light rounded-circle p-2 shadow-sm me-3"
            style="width:42px; height:42px;">
            <i class="bi bi-arrow-left fs-5"></i>
        </button>
        <div>
            <h4 class="fw-extrabold mb-0 text-dark">Manajemen Akses</h4>
            <small class="text-muted">Total: <?= count($user_list); ?> Pengguna Sistem</small>
        </div>
    </div>

    <!-- Search Box -->
    <form action="" method="GET">
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="search" class="form-control" name="search" placeholder="Cari username..."
                value="<?= htmlspecialchars($search_query); ?>" autocomplete="off">
            <button type="submit" class="d-none"></button>
        </div>
    </form>

    <!-- Daftar User (Card Based) -->
    <div class="user-grid">
        <?php if (count($user_list) > 0): ?>
            <?php foreach ($user_list as $row):
                $role_lower = strtolower($row['Level']);
                $badge_class = 'role-default';

                if ($role_lower == 'administrator')
                    $badge_class = 'role-admin';
                elseif ($role_lower == 'owner')
                    $badge_class = 'role-owner';
                elseif ($role_lower == 'manager')
                    $badge_class = 'role-manager';
                elseif ($role_lower == 'kasir' || $role_lower == 'petugas')
                    $badge_class = 'role-kasir';
                ?>
                <div class="user-card">
                    <div class="user-info">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($row['Username']); ?>&background=random&color=fff&bold=true"
                            class="user-avatar" alt="Avatar">

                        <div>
                            <h6 class="fw-bold mb-1 text-dark"><?= htmlspecialchars(ucfirst($row['Username'])); ?></h6>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <small class="text-muted">ID: #<?= $row['UserID']; ?></small>
                                <span class="role-badge <?= $badge_class; ?>"><?= htmlspecialchars($row['Level']); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="action-buttons">
                        <a href="edit_user.php?id=<?= $row['UserID']; ?>" class="btn-action btn-edit">
                            <i class="bi bi-pencil-square me-2"></i> Edit
                        </a>

                        <?php if ($row['UserID'] != $_SESSION['UserID']): ?>
                            <a href="../../../proses/proses_hapus_user.php?id=<?= $row['UserID']; ?>"
                                class="btn-action btn-delete tombol-hapus-sweet"
                                data-nama="<?= htmlspecialchars($row['Username']); ?>">
                                <i class="bi bi-trash me-2"></i> Hapus
                            </a>
                        <?php else: ?>
                            <button class="btn-action" style="background:#f1f5f9; color:#94a3b8; border:none; cursor:not-allowed;">
                                <i class="bi bi-shield-lock me-2"></i> Akun Anda
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center my-5 py-5 w-100" style="grid-column: span 2;">
                <i class="bi bi-people text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                <h5 class="mt-3 fw-bold text-muted">Pengguna Tidak Ditemukan</h5>
                <p class="text-muted small">Coba gunakan username lain.</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<a href="tambah_user.php" class="fab-add" title="Tambah Pengguna Baru">
    <i class="bi bi-person-plus-fill"></i>
</a>

<?php include '../../template/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tombolHapus = document.querySelectorAll('.tombol-hapus-sweet');

        tombolHapus.forEach(tombol => {
            tombol.addEventListener('click', function (e) {
                e.preventDefault();
                const linkHapus = this.getAttribute('href');
                const namaUser = this.getAttribute('data-nama');

                Swal.fire({
                    title: 'Cabut Akses?',
                    html: `Yakin ingin menghapus <b>${namaUser}</b> dari sistem? Data yang terhapus tidak bisa dikembalikan.`,
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