<?php
session_start();
include '../fungsi/autentikasi.php';
cekLogin();

function tampilkanUI($status, $judul, $pesan, $redirect_url)
{
    $warna_bg = $status == 'success' ? '#fef08a' : '#fee2e2';
    $warna_ikon = $status == 'success' ? '#eab308' : '#ef4444';
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
                animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; 
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
                animation: scaleUp 0.4s 0.1s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
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
                animation: loading 1s infinite ease-in-out;
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
            setTimeout(function() {
                window.location.href = '{$redirect_url}';
            }, 1800);
        </script>
    </body>
    </html>
    ";
    exit();
}

$url_kembali = '../resources/views/admin/user.php';

if (!isset($_SESSION['Level']) || !in_array(strtolower($_SESSION['Level']), ['administrator', 'owner'])) {
    tampilkanUI('error', 'Akses Ditolak!', 'Anda tidak memiliki hak untuk menghapus pengguna.', $url_kembali);
}

include '../config/koneksi.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_user = intval($_GET['id']);

    if ($id_user == $_SESSION['UserID']) {
        tampilkanUI('error', 'Aksi Dibatalkan!', 'Sistem menolak penghapusan akun yang sedang Anda gunakan saat ini.', $url_kembali);
    }

    try {
        $stmt = $koneksi->prepare("DELETE FROM user WHERE UserID = ?");
        $stmt->execute([$id_user]);

        if ($stmt->rowCount() > 0) {
            tampilkanUI('success', 'Akses Dicabut!', 'Data pengguna telah berhasil dihapus secara permanen dari sistem.', $url_kembali);
        } else {
            tampilkanUI('error', 'Gagal Menghapus', 'Data pengguna tidak ditemukan di database.', $url_kembali);
        }

    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            tampilkanUI('error', 'Tidak Bisa Dihapus!', 'Akun ini terkait dengan data riwayat transaksi kasir. Hapus riwayatnya terlebih dahulu.', $url_kembali);
        } else {
            tampilkanUI('error', 'Sistem Error', htmlspecialchars($e->getMessage()), $url_kembali);
        }
    }
} else {
    header("Location: " . $url_kembali);
    exit();
}
?>