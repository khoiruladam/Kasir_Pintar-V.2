<?php
include '../config/koneksi.php';

if (isset($_GET['id'])) {
    $penjualan_id = $_GET['id'];

    $query_detail = "SELECT
                        p.NamaProduk,
                        dp.JumlahProduk,
                        dp.Subtotal
                    FROM detailpenjualan dp
                    JOIN produk p ON dp.ProdukID = p.ProdukID
                    WHERE dp.PenjualanID = ?";
    $stmt = mysqli_prepare($koneksi, $query_detail);
    mysqli_stmt_bind_param($stmt, "i", $penjualan_id);
    mysqli_stmt_execute($stmt);
    $result_detail = mysqli_stmt_get_result($stmt);

    $output = '<h5>Detail Barang:</h5>';
    $output .= '<table class="table table-striped">';
    $output .= '<thead><tr><th>Nama Produk</th><th>Jumlah</th><th>Subtotal</th></tr></thead>';
    $output .= '<tbody>';
    
    while ($row = mysqli_fetch_assoc($result_detail)) {
        $output .= '<tr>';
        $output .= '<td>' . $row['NamaProduk'] . '</td>';
        $output .= '<td>' . $row['JumlahProduk'] . '</td>';
        $output .= '<td>Rp ' . number_format($row['Subtotal'], 0, ',', '.') . '</td>';
        $output .= '</tr>';
    }

    $output .= '</tbody></table>';

    echo $output;
    mysqli_stmt_close($stmt);
}
?>