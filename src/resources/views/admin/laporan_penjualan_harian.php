<?php

include '../../../fungsi/autentikasi.php';
cekLogin();
include '../../../config/koneksi.php';

$tanggal_hari_ini = date("Y-m-d");

$query_penjualan = "SELECT 
                        p.PenjualanID,
                        p.TanggalPenjualan,
                        p.TotalHarga,
                        pl.NamaPelanggan,
                        u.Username AS NamaPetugas
                    FROM penjualan p
                    JOIN pelanggan pl ON p.PelangganID = pl.PelangganID
                    JOIN user u ON p.UserID = u.UserID
                    WHERE p.TanggalPenjualan = ?
                    ORDER BY p.PenjualanID DESC";

$stmt = $koneksi->prepare($query_penjualan);
$stmt->execute([$tanggal_hari_ini]);
$transaksi_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_omset = 0;
foreach ($transaksi_list as $t) {
    $total_omset += $t['TotalHarga'];
}

include '../../template/header.php';
?>

<style>
    body {
        background-color: #f4f7fe;
    }

    .app-container {
        max-width: 600px;
        /* Lebar optimal untuk Mobile & Tablet */
        margin: 0 auto;
        padding-bottom: 120px;
    }

    /* Summary Card (Omset) */
    .summary-card {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        border-radius: 24px;
        padding: 25px;
        color: white;
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
        margin-bottom: 25px;
        position: relative;
        overflow: hidden;
    }

    .summary-card::after {
        content: '';
        position: absolute;
        top: -20px;
        right: -20px;
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    /* Transaction Card (Pengganti Tabel) */
    .trx-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 18px;
        margin-bottom: 15px;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        display: flex;
        flex-direction: column;
        transition: transform 0.2s;
        cursor: pointer;
    }

    .trx-card:active {
        transform: scale(0.98);
    }

    .trx-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px dashed #e2e8f0;
        padding-bottom: 12px;
        margin-bottom: 12px;
    }

    .trx-id {
        font-weight: 700;
        color: #475569;
        font-size: 0.9rem;
    }

    .trx-time {
        color: #10b981;
        font-weight: 600;
        font-size: 0.8rem;
        background: #dcfce7;
        padding: 4px 10px;
        border-radius: 8px;
    }

    .trx-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .trx-info h6 {
        margin: 0 0 4px;
        font-weight: 700;
        color: #1e293b;
    }

    .trx-info small {
        color: #64748b;
        font-size: 0.85rem;
    }

    .trx-price {
        font-weight: 800;
        color: #1e293b;
        font-size: 1.1rem;
    }

    /* Modal Custom Styling */
    .modal-content-custom {
        border-radius: 24px;
        border: none;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .modal-header-custom {
        border-bottom: 1px solid #f1f5f9;
        padding: 20px 25px;
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
            <h4 class="fw-extrabold mb-0 text-dark">Laporan Harian</h4>
            <small class="text-muted"><?= date("d M Y", strtotime($tanggal_hari_ini)); ?></small>
        </div>
    </div>

    <!-- Ringkasan Omset Hari Ini -->
    <div class="summary-card">
        <p class="mb-1" style="color: rgba(255,255,255,0.8); font-size: 0.9rem;">Total Pendapatan</p>
        <h2 class="fw-extrabold mb-0">Rp <?= number_format($total_omset, 0, ',', '.'); ?></h2>
        <div class="mt-3 d-flex justify-content-between align-items-center">
            <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold shadow-sm">
                <?= count($transaksi_list); ?> Transaksi Selesai
            </span>
            <a href="penjualan.php" class="btn btn-sm btn-light rounded-pill fw-bold text-primary px-3 shadow-sm">
                <i class="bi bi-plus-lg"></i> Kasir
            </a>
        </div>
    </div>

    <!-- Daftar Transaksi (Card Based) -->
    <h6 class="fw-bold px-2 mb-3 text-muted">Riwayat Transaksi</h6>

    <?php if (count($transaksi_list) > 0): ?>
        <?php foreach ($transaksi_list as $row): ?>

            <div class="trx-card detail-btn" data-id="<?= $row['PenjualanID']; ?>">
                <div class="trx-header">
                    <div class="trx-id">
                        <i class="bi bi-receipt me-1"></i> TRX-<?= str_pad($row['PenjualanID'], 5, '0', STR_PAD_LEFT); ?>
                    </div>
                    <div class="trx-time"><i class="bi bi-check-circle-fill me-1"></i> Sukses</div>
                </div>
                <div class="trx-body">
                    <div class="trx-info">
                        <h6><i class="bi bi-person me-1"></i> <?= htmlspecialchars($row['NamaPelanggan']); ?></h6>
                        <small><i class="bi bi-person-badge me-1"></i> Kasir:
                            <?= htmlspecialchars($row['NamaPetugas']); ?></small>
                    </div>
                    <div class="text-end">
                        <div class="trx-price">Rp <?= number_format($row['TotalHarga'], 0, ',', '.'); ?></div>
                        <small class="text-primary fw-bold mt-1 d-block" style="font-size: 0.8rem;">Lihat Struk <i
                                class="bi bi-chevron-right"></i></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    <?php else: ?>
        <div class="text-center my-5 py-5 w-100">
            <i class="bi bi-inbox text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
            <h5 class="mt-3 fw-bold text-muted">Belum Ada Transaksi</h5>
            <p class="text-muted small">Belum ada penjualan yang tercatat pada hari ini.</p>
        </div>
    <?php endif; ?>

</div>

<!-- Modal Detail Transaksi (Desain Diperhalus) -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-custom">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title fw-bold" id="detailModalLabel">Detail Struk</h5>
                <button type="button" class="btn-close bg-light rounded-circle p-2" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="modal-body-content">

                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted small">Memuat struk...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../template/footer.php'; ?>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('.detail-btn').click(function () {
            var penjualanID = $(this).data('id');

            $('#modal-body-content').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted small">Memuat struk...</p></div>');
            $('#detailModal').modal('show');

            $.ajax({
                url: '../../../proses/get_detail_penjualan.php',
                type: 'GET',
                data: { id: penjualanID },
                success: function (response) {
                    $('#modal-body-content').html(response);
                },
                error: function () {
                    $('#modal-body-content').html('<div class="alert alert-danger text-center border-0 rounded-4">Gagal memuat struk. Periksa koneksi jaringan.</div>');
                }
            });
        });
    });
</script>