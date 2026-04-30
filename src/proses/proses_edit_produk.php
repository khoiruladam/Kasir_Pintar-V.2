<?php
session_start();
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_produk = $_POST['id'] ?? '';
    $nama_produk = $_POST['nama_produk'] ?? '';
    $harga = $_POST['harga'] ?? '';
    $stok = $_POST['stok'] ?? '';

    if (empty($id_produk) || empty($nama_produk) || $harga === '' || $stok === '') {
        echo "<script>alert('Semua kolom harus diisi!'); window.history.back();</script>";
        exit;
    }

    try {
        $query = "UPDATE produk SET NamaProduk = ?, Harga = ?, Stok = ? WHERE ProdukID = ?";
        $stmt = $koneksi->prepare($query);
        
        $stmt->execute([$nama_produk, $harga, $stok, $id_produk]);
        
        header('Location: ../resources/views/admin/produk.php?pesan=edit_sukses');
        exit;

    } catch (PDOException $e) {
        die("Gagal memperbarui data: " . htmlspecialchars($e->getMessage()));
    }
} else {
    header('Location: ../resources/views/admin/produk.php');
    exit;
}
?>