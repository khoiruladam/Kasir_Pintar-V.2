<?php
include '../../../fungsi/autentikasi.php';
cekLogin();
include '../../../config/koneksi.php';

$level_user = strtolower($_SESSION['Level'] ?? '');
$username = htmlspecialchars($_SESSION['Username'] ?? 'User');

try {
    $stmt = $koneksi->query("SELECT COUNT(*) AS total FROM produk");
    $total_produk = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $hari_ini = date("Y-m-d");
    $stmt2 = $koneksi->prepare("SELECT SUM(TotalHarga) AS total, COUNT(*) AS jumlah FROM penjualan WHERE TanggalPenjualan = ?");
    $stmt2->execute([$hari_ini]);
    $row = $stmt2->fetch(PDO::FETCH_ASSOC);

    $total_penjualan = $row['total'] ?? 0;
    $jumlah_transaksi = $row['jumlah'] ?? 0;

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

include '../../template/header.php';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* =====================
       DASHBOARD — MOBILE FIRST
    ===================== */
    body {
        background-color: #f4f7fe;
    }

    .dash {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px 16px 100px;
        /* Padding bawah agar tidak tertutup bottom nav */
    }

    /* Welcome card */
    .welcome-card {
        background: #fff;
        border-radius: 20px;
        padding: 18px;
        margin-bottom: 20px;
        box-shadow: 0 4px 20px rgba(99, 102, 241, .05);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .wc-name {
        font-size: 1.1rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 3px;
    }

    .wc-sub {
        font-size: 0.8rem;
        color: #94a3b8;
        margin-bottom: 10px;
    }

    .badge-role {
        display: inline-block;
        background: #ede9fe;
        color: #6366f1;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        margin-left: 4px;
        vertical-align: middle;
        text-transform: capitalize;
    }

    .btn-logout {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff0f0;
        color: #ef4444;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 8px 16px;
        border-radius: 30px;
        border: 1px solid #fecaca;
        text-decoration: none;
        transition: 0.2s;
    }

    .btn-logout:active {
        transform: scale(0.95);
    }

    .wc-avatar {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        background: #ede9fe;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        flex-shrink: 0;
        color: #6366f1;
    }

    /* Section label */
    .sec-label {
        font-size: 0.75rem;
        font-weight: 800;
        color: #94a3b8;
        letter-spacing: .05em;
        text-transform: uppercase;
        margin: 0 0 12px;
    }

    /* Stat cards */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: #fff;
        border-radius: 20px;
        padding: 18px;
        box-shadow: 0 4px 20px rgba(99, 102, 241, .05);
        display: flex;
        flex-direction: column;
    }

    .sc-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #fef9c3;
        color: #92400e;
        font-size: 0.65rem;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 20px;
        margin-bottom: 12px;
        width: fit-content;
    }

    .sc-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 12px;
    }

    .ic-purple {
        background: #ede9fe;
        color: #6366f1;
    }

    .ic-green {
        background: #d1fae5;
        color: #10b981;
    }

    .sc-val {
        font-size: 1.25rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 3px;
        line-height: 1.2;
    }

    .sc-label {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-bottom: 15px;
        flex: 1;
        font-weight: 600;
    }

    .sc-btn {
        display: flex;
        justify-content: center;
        align-items: center;
        background: #6366f1;
        color: #fff;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 8px 0;
        border-radius: 12px;
        text-decoration: none;
        width: 100%;
        border: none;
        transition: 0.2s;
    }

    .sc-btn.green {
        background: #10b981;
    }

    .sc-btn:active {
        transform: scale(0.97);
    }

    /* Widgets (Kalender & Chart) */
    .widget-container {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-bottom: 25px;
    }

    .dashboard-widget {
        background: #ffffff;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(99, 102, 241, .05);
    }

    .widget-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        font-weight: 800;
        color: #475569;
    }

    .calendar-header button {
        background: #f1f5f9;
        border: none;
        border-radius: 10px;
        width: 35px;
        height: 35px;
        color: #64748b;
        cursor: pointer;
        transition: 0.2s;
    }

    .calendar-header button:active {
        background: #e2e8f0;
        color: #6366f1;
        transform: scale(0.9);
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 5px;
        text-align: center;
    }

    .calendar-day-name {
        font-size: 0.75rem;
        font-weight: 800;
        color: #94a3b8;
        padding-bottom: 8px;
    }

    .calendar-date {
        aspect-ratio: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 0.85rem;
        font-weight: 700;
        color: #334155;
        border-radius: 12px;
        transition: 0.2s;
    }

    .calendar-date.active {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        color: white;
        box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3);
    }

    .calendar-date.muted {
        color: #cbd5e1;
    }

    /* Menu card */
    .menu-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(99, 102, 241, .05);
        overflow: hidden;
    }

    .menu-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 16px 18px;
        text-decoration: none;
        color: #1e293b;
        transition: background .15s;
    }

    .menu-item:active {
        background: #f8fafc;
    }

    .menu-item+.menu-item {
        border-top: 1px dashed #f1f5f9;
    }

    .mi-ico {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .ico-p {
        background: #ede9fe;
        color: #6366f1;
    }

    .ico-g {
        background: #d1fae5;
        color: #10b981;
    }

    .ico-a {
        background: #fef3c7;
        color: #f59e0b;
    }

    .ico-r {
        background: #fee2e2;
        color: #ef4444;
    }

    .mi-text {
        flex: 1;
        min-width: 0;
    }

    .mi-name {
        font-size: 0.95rem;
        font-weight: 800;
        color: #1e293b;
    }

    .mi-sub {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: 2px;
        font-weight: 500;
    }

    .mi-arr {
        font-size: 20px;
        color: #cbd5e1;
        flex-shrink: 0;
        font-weight: bold;
    }

    @media (min-width: 768px) {
        .widget-container {
            flex-direction: row;
        }

        .chart-wrapper {
            flex: 2;
        }

        .calendar-wrapper {
            flex: 1;
        }
    }
</style>

<div class="dash">

    <!-- Welcome -->
    <div class="welcome-card">
        <div>
            <div class="wc-name">Halo, <?= $username ?> 👋</div>
            <div class="wc-sub">Login sebagai
                <span class="badge-role"><?= htmlspecialchars($level_user) ?></span>
            </div>
            <a href="../../../auth/proses_logout.php" class="btn-logout">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
        <div class="wc-avatar">
            <i class="bi bi-person-fill"></i>
        </div>
    </div>

    <!-- Stats -->
    <div class="sec-label">Ringkasan hari ini</div>
    <div class="stat-grid">
        <div class="stat-card">
            <div class="sc-badge">⭐ Stok</div>
            <div class="sc-icon ic-purple">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            <div class="sc-val"><?= $total_produk ?></div>
            <div class="sc-label">Jumlah Produk</div>
            <a href="produk.php" class="sc-btn shadow-sm">Lihat Detail</a>
        </div>

        <div class="stat-card">
            <div class="sc-badge">⭐ Hari ini</div>
            <div class="sc-icon ic-green">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="sc-val">Rp <?= number_format($total_penjualan, 0, ',', '.') ?></div>
            <div class="sc-label"><?= $jumlah_transaksi ?> Transaksi</div>
            <a href="laporan_penjualan_harian.php" class="sc-btn green shadow-sm">Lihat Laporan</a>
        </div>
    </div>

    <!-- WIDGET AREA (Kalender & Chart) -->
    <div class="widget-container">

        <!-- 1. WIDGET CHART (HANYA UNTUK ADMINISTRATOR SAJA) -->
        <?php if ($level_user === 'administrator'): ?>
            <div class="dashboard-widget chart-wrapper">
                <div class="widget-title">
                    <i class="bi bi-graph-up-arrow text-success"></i> Grafik Penjualan
                </div>
                <div style="position: relative; height: 220px; width: 100%;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        <?php endif; ?>

        <!-- 2. WIDGET KALENDER (TAMPIL UNTUK SEMUA ROLE) -->
        <div class="dashboard-widget calendar-wrapper">
            <div class="widget-title">
                <i class="bi bi-calendar-event text-primary"></i> Kalender Hari Ini
            </div>
            <div class="calendar-header">
                <button id="prevMonth"><i class="bi bi-chevron-left"></i></button>
                <span id="monthYear">Bulan Tahun</span>
                <button id="nextMonth"><i class="bi bi-chevron-right"></i></button>
            </div>
            <div class="calendar-grid" id="calendarGrid">
            </div>
        </div>

    </div>

    <!-- MENU CARD -->
    <div class="sec-label mt-2">Menu Cepat</div>
    <div class="menu-card">

        <!-- Menu Petugas/Kasir -->
        <?php if ($level_user === 'petugas' || $level_user === 'kasir'): ?>
            <a href="penjualan.php" class="menu-item">
                <div class="mi-ico ico-p"><i class="bi bi-cart-plus-fill"></i></div>
                <div class="mi-text">
                    <div class="mi-name">Transaksi Baru</div>
                    <div class="mi-sub">Buat penjualan sekarang</div>
                </div>
                <span class="mi-arr">›</span>
            </a>
            <a href="pelanggan.php" class="menu-item">
                <div class="mi-ico ico-r"><i class="bi bi-people-fill"></i></div>
                <div class="mi-text">
                    <div class="mi-name">Data Member</div>
                    <div class="mi-sub">Kelola data pelanggan</div>
                </div>
                <span class="mi-arr">›</span>
            </a>

            <!-- Menu Administrator/Manager/Owner -->
        <?php elseif (in_array($level_user, ['administrator', 'owner', 'manager'])): ?>
            <a href="stok.php" class="menu-item">
                <div class="mi-ico ico-a"><i class="bi bi-box2-fill"></i></div>
                <div class="mi-text">
                    <div class="mi-name">Manajemen Stok</div>
                    <div class="mi-sub">Update ketersediaan barang</div>
                </div>
                <span class="mi-arr">›</span>
            </a>
            <a href="produk.php" class="menu-item">
                <div class="mi-ico ico-g"><i class="bi bi-tags-fill"></i></div>
                <div class="mi-text">
                    <div class="mi-name">Master Produk</div>
                    <div class="mi-sub">Tambah, edit, hapus produk</div>
                </div>
                <span class="mi-arr">›</span>
            </a>
            <a href="laporan_penjualan_harian.php" class="menu-item">
                <div class="mi-ico ico-p"><i class="bi bi-bar-chart-fill"></i></div>
                <div class="mi-text">
                    <div class="mi-name">Laporan Pendapatan</div>
                    <div class="mi-sub">Rekap transaksi harian & bulanan</div>
                </div>
                <span class="mi-arr">›</span>
            </a>
            <a href="users.php" class="menu-item">
                <div class="mi-ico ico-r"><i class="bi bi-person-badge-fill"></i></div>
                <div class="mi-text">
                    <div class="mi-name">Manajemen Pengguna</div>
                    <div class="mi-sub">Kelola hak akses & akun</div>
                </div>
                <span class="mi-arr">›</span>
            </a>
        <?php endif; ?>

    </div>
</div>

<?php include '../../template/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        // ==========================
        // 1. LOGIKA KALENDER
        // ==========================
        const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        const dayNames = ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"];

        let currentDate = new Date();
        const today = new Date();

        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();

            document.getElementById('monthYear').textContent = `${monthNames[month]} ${year}`;

            const firstDayIndex = new Date(year, month, 1).getDay();
            const lastDay = new Date(year, month + 1, 0).getDate();
            const prevLastDay = new Date(year, month, 0).getDate();

            let html = '';

            // Render header nama hari
            dayNames.forEach(day => {
                html += `<div class="calendar-day-name">${day}</div>`;
            });

            // Render sisa hari dari bulan sebelumnya (Muted)
            for (let i = firstDayIndex; i > 0; i--) {
                html += `<div class="calendar-date muted">${prevLastDay - i + 1}</div>`;
            }

            // Render hari pada bulan ini
            for (let i = 1; i <= lastDay; i++) {
                if (i === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                    html += `<div class="calendar-date active shadow-sm">${i}</div>`; // Hari ini
                } else {
                    html += `<div class="calendar-date">${i}</div>`;
                }
            }

            document.getElementById('calendarGrid').innerHTML = html;
        }

        document.getElementById('prevMonth').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });

        document.getElementById('nextMonth').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });

        renderCalendar();

        // ==========================
        // 2. LOGIKA GRAFIK CHART.JS
        // ==========================
        const canvasChart = document.getElementById('salesChart');

        if (canvasChart) {
            const ctx = canvasChart.getContext('2d');

            const gradient = ctx.createLinearGradient(0, 0, 0, 220);
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
                    datasets: [{
                        label: 'Pendapatan',
                        data: [1200000, 1900000, 1500000, 2200000, 1800000, 2500000, 3100000],
                        borderColor: '#10b981',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#10b981',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return ' Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: "'Plus Jakarta Sans', sans-serif", size: 10 } }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [4, 4], color: '#f1f5f9' },
                            ticks: {
                                font: { family: "'Plus Jakarta Sans', sans-serif", size: 10 },
                                callback: function (value) {
                                    return (value / 1000000) + ' Jt';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>