<?php
session_start();

/**
 * Memastikan user sudah login sebelum mengakses halaman
 */
function cekLogin() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: ../index.php?pesan=belum_login");
        exit();
    }
}

/**
 * Memastikan user memiliki hak akses yang tepat
 * @param string|array $level_required Bisa berupa string 'administrator' atau array ['administrator', 'petugas']
 */
function cekHakAkses($level_required) {
    $allowed_levels = is_array($level_required) ? $level_required : [$level_required];

    if (!isset($_SESSION['Level']) || !in_array($_SESSION['Level'], $allowed_levels)) {
        header("Location: ../halaman/dashboard.php?pesan=hak_akses_ditolak");
        exit();
    }
}

/**
 * Helper untuk mengecek role di dalam tampilan (UI)
 */
function isAdmin() {
    return isset($_SESSION['Level']) && $_SESSION['Level'] === 'administrator';
}

function isPetugas() {
    return isset($_SESSION['Level']) && $_SESSION['Level'] === 'petugas';
}