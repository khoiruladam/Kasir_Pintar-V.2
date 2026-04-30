<?php
include '../../../fungsi/autentikasi.php';
cekLogin();
include '../../../config/koneksi.php';

$level_user = strtolower($_SESSION['Level'] ?? '');

try {
  $query = "SELECT * FROM pelanggan WHERE PelangganID != 1 ORDER BY PelangganID DESC";
  $stmt = $koneksi->query($query);
  $pelanggan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    /* Ukuran optimal untuk Mobile & Tablet */
    margin: 0 auto;
    padding-bottom: 120px;
  }

  /* Pelanggan Card (Pengganti Tabel) */
  .pelanggan-card {
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

  .pelanggan-card:active {
    transform: scale(0.98);
  }

  .pelanggan-info {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
  }

  .pelanggan-avatar {
    width: 55px;
    height: 55px;
    border-radius: 18px;
    margin-right: 15px;
    object-fit: cover;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
  }

  .info-text h6 {
    margin: 0 0 4px;
    font-weight: 700;
    color: #1e293b;
    font-size: 1.05rem;
  }

  .info-text p {
    margin: 0;
    color: #64748b;
    font-size: 0.85rem;
    line-height: 1.4;
  }

  .badge-phone {
    background: #f1f5f9;
    color: #475569;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 700;
    display: inline-block;
    margin-top: 6px;
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

  /* FAB (Floating Action Button) untuk Tambah Data */
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
    transition: transform 0.2s;
  }

  .fab-add:active {
    transform: scale(0.9);
  }

  @media (min-width: 768px) {
    .fab-add {
      right: 50px;
      bottom: 50px;
    }

    .pelanggan-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 15px;
    }
  }
</style>

<div class="app-container mt-4 px-3">

  <!-- Header Navigation Custom -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
      <button onclick="history.back()"
        class="btn btn-light rounded-circle p-2 shadow-sm me-3 d-flex justify-content-center align-items-center"
        style="width:42px; height:42px;">
        <i class="bi bi-arrow-left fs-5"></i>
      </button>
      <div>
        <h4 class="fw-extrabold mb-0 text-dark">Data Pelanggan</h4>
        <small class="text-muted"><?= count($pelanggan_list); ?> Pelanggan Terdaftar</small>
      </div>
    </div>
  </div>

  <!-- Daftar Pelanggan (Card Based Mobile First) -->
  <div class="pelanggan-grid">
    <?php if (count($pelanggan_list) > 0): ?>
      <?php foreach ($pelanggan_list as $row): ?>
        <div class="pelanggan-card">
          <div class="pelanggan-info">
            <img
              src="https://ui-avatars.com/api/?name=<?= urlencode($row['NamaPelanggan']); ?>&background=random&color=fff&bold=true"
              class="pelanggan-avatar" alt="Avatar">

            <div class="info-text">
              <h6><?= htmlspecialchars($row['NamaPelanggan']); ?></h6>
              <p><i class="bi bi-geo-alt me-1 text-primary"></i>
                <?= htmlspecialchars($row['Alamat'] ?: 'Alamat tidak diisi'); ?></p>
              <div class="badge-phone">
                <i class="bi bi-telephone-fill me-1"></i> <?= htmlspecialchars($row['NomorTelepon'] ?: '-'); ?>
              </div>
            </div>
          </div>

          <!-- Tombol Aksi (Hanya muncul jika yang login adalah Petugas/Kasir) -->
          <?php if ($level_user == 'petugas' || $level_user == 'kasir'): ?>
            <div class="action-buttons">
              <a href="edit_pelanggan.php?id=<?= $row['PelangganID']; ?>" class="btn-action btn-edit">
                <i class="bi bi-pencil-square me-2"></i> Edit
              </a>
              <a href="../../../proses/proses_hapus_pelanggan.php?id=<?= $row['PelangganID']; ?>"
                class="btn-action btn-delete tombol-hapus-sweet" data-nama="<?= htmlspecialchars($row['NamaPelanggan']); ?>">
                <i class="bi bi-trash me-2"></i> Hapus
              </a>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="text-center my-5 py-5 w-100" style="grid-column: span 2;">
        <i class="bi bi-person-vcard text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
        <h5 class="mt-3 fw-bold text-muted">Belum Ada Pelanggan</h5>
        <p class="text-muted small">Data pelanggan setia akan muncul di sini.</p>
      </div>
    <?php endif; ?>
  </div>

</div>
<a href="tambah_pelanggan.php" class="fab-add" title="Tambah Member Baru">
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
        const namaPelanggan = this.getAttribute('data-nama');

        Swal.fire({
          title: 'Hapus Member?',
          html: `Yakin ingin menghapus data member <b>${namaPelanggan}</b>? Riwayat transaksinya mungkin akan terpengaruh.`,
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