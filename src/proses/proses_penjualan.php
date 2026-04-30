<?php
session_start();
include '../config/koneksi.php';

function tampilkanUI($status, $judul, $pesan, $redirect_url)
{
    $warna_bg = $status == 'success' ? '#dcfce7' : '#fee2e2';
    $warna_ikon = $status == 'success' ? '#22c55e' : '#ef4444';
    $ikon = $status == 'success' ? '✓' : '✕';

    echo "
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>{$judul}</title>
        <link href='https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap' rel='stylesheet'>
        <style>
            body { 
                font-family: 'Plus Jakarta Sans', sans-serif; 
                background: #f4f7fe; 
                display: flex; 
                justify-content: center; 
                align-items: center; 
                height: 100vh; 
                margin: 0; 
            }
            .card-animasi { 
                background: white; 
                padding: 40px 30px; 
                border-radius: 30px; 
                box-shadow: 0 20px 40px rgba(0,0,0,0.05); 
                text-align: center; 
                max-width: 350px;
                width: 90%;
                animation: popIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; 
            }
            .icon-box { 
                width: 90px; 
                height: 90px; 
                background: {$warna_bg}; 
                color: {$warna_ikon}; 
                border-radius: 50%; 
                display: flex; 
                justify-content: center; 
                align-items: center; 
                font-size: 45px; 
                font-weight: bold;
                margin: 0 auto 20px; 
                box-shadow: 0 10px 20px {$warna_bg};
                animation: scaleUp 0.5s 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
            }
            h2 { margin: 0 0 10px; color: #1e293b; font-weight: 700; font-size: 1.4rem;}
            p { color: #64748b; margin: 0; font-size: 0.95rem; line-height: 1.5; }
            .loader {
                margin-top: 20px;
                width: 40px;
                height: 4px;
                background: #e2e8f0;
                border-radius: 4px;
                overflow: hidden;
                position: relative;
                margin-left: auto;
                margin-right: auto;
            }
            .loader::after {
                content: '';
                position: absolute;
                left: 0; top: 0; height: 100%; width: 50%;
                background: {$warna_ikon};
                animation: loading 1.5s infinite ease-in-out;
            }
            @keyframes popIn { 
                0% { opacity: 0; transform: translateY(30px) scale(0.9); } 
                100% { opacity: 1; transform: translateY(0) scale(1); } 
            }
            @keyframes scaleUp {
                0% { transform: scale(0); }
                100% { transform: scale(1); }
            }
            @keyframes loading {
                0% { left: -50%; }
                100% { left: 100%; }
            }
        </style>
    </head>
    <body>
        <div class='card-animasi'>
            <div class='icon-box'>{$ikon}</div>
            <h2>{$judul}</h2>
            <p>{$pesan}</p>
            <div class='loader'></div>
        </div>
        <script>
            // Otomatis pindah halaman setelah 2 detik
            setTimeout(function() {
                window.location.href = '{$redirect_url}';
            }, 2000);
        </script>
    </body>
    </html>
    ";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pelanggan_id = $_POST['pelanggan_id'] ?? null;
    $total_harga = $_POST['total_harga'] ?? null;
    $produk_ids = $_POST['produk_id'] ?? [];
    $jumlahs = $_POST['jumlah'] ?? [];

    $url_kembali = '../resources/views/admin/penjualan.php';

    if (empty($pelanggan_id) || empty($total_harga) || empty($produk_ids)) {
        tampilkanUI('error', 'Data Tidak Lengkap!', 'Pastikan ada produk di keranjang dan pelanggan telah dipilih.', $url_kembali);
    }

    $tanggal_penjualan = date("Y-m-d");
    $user_id = $_SESSION['UserID'] ?? 0;

    try {
        $koneksi->beginTransaction();

        $query_penjualan = "INSERT INTO penjualan (TanggalPenjualan, TotalHarga, PelangganID, UserID) VALUES (?, ?, ?, ?)";
        $stmt = $koneksi->prepare($query_penjualan);
        $stmt->execute([$tanggal_penjualan, $total_harga, $pelanggan_id, $user_id]);

        $penjualan_id = $koneksi->lastInsertId();

        for ($i = 0; $i < count($produk_ids); $i++) {
            $produk_id = $produk_ids[$i];
            $jumlah = $jumlahs[$i];

            $query_produk = "SELECT Harga, Stok FROM produk WHERE ProdukID = ?";
            $stmt_produk = $koneksi->prepare($query_produk);
            $stmt_produk->execute([$produk_id]);
            $produk = $stmt_produk->fetch(PDO::FETCH_ASSOC);

            if (!$produk) {
                throw new Exception("Produk tidak ditemukan di database.");
            }

            $harga_produk = $produk['Harga'];
            $stok_produk = $produk['Stok'];
            $subtotal = $harga_produk * $jumlah;

            $stok_baru = $stok_produk - $jumlah;
            if ($stok_baru < 0) {
                throw new Exception("Stok tidak mencukupi untuk diproses.");
            }

            $query_detail = "INSERT INTO detailpenjualan (PenjualanID, ProdukID, JumlahProduk, Subtotal) VALUES (?, ?, ?, ?)";
            $stmt_detail = $koneksi->prepare($query_detail);
            $stmt_detail->execute([$penjualan_id, $produk_id, $jumlah, $subtotal]);

            $query_stok_update = "UPDATE produk SET Stok = ? WHERE ProdukID = ?";
            $stmt_stok = $koneksi->prepare($query_stok_update);
            $stmt_stok->execute([$stok_baru, $produk_id]);
        }

        $koneksi->commit();
        tampilkanUI('success', 'Pembayaran Berhasil!', 'Transaksi tersimpan. Mengembalikan ke sistem kasir...', $url_kembali);

    } catch (Exception $e) {
        $koneksi->rollBack();
        $pesan_error = htmlspecialchars($e->getMessage());
        tampilkanUI('error', 'Transaksi Gagal', $pesan_error, $url_kembali);
    }
}
?>