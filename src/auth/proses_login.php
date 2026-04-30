<?php
session_start();
require_once '../config/koneksi.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

try {
    $stmt = $koneksi->prepare("SELECT * FROM user WHERE Username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($password == $user['Password']) { 
            
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['UserID']   = $user['UserID'];
            $_SESSION['Username'] = $user['Username'];
            $_SESSION['Level']    = $user['Level'];

            session_write_close();
            if ($_SESSION['Level'] == 'administrator') {
                header("Location: ../resources/views/admin/dashboard.php");
            } else if ($_SESSION['Level'] == 'petugas') {
                header("Location: ../resources/views/admin/dashboard.php"); 
            } else {
                header("Location: ../resources/views/public/dashboard.php");
            }
            exit(); 
            
        } else {
            header("Location: ../index.php?pesan=password_salah");
            exit();
        }
    } else {
        header("Location: ../index.php?pesan=user_tidak_ada");
        exit();
    }
} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage()); 
}