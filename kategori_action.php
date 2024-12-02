<?php
session_start();

// Cek apakah user sudah login
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<?php
require_once 'config.php';

if(isset($_POST['action']) || isset($_GET['action'])) {
    $action = isset($_POST['action']) ? $_POST['action'] : $_GET['action'];
    
    switch($action) {
        case 'add':
            tambahKategori();
            break;
        case 'edit':
            editKategori();
            break;
        case 'delete':
            hapusKategori();
            break;
    }
}

function tambahKategori() {
    global $conn;
    
    $nama_kategori = $_POST['nama_kategori'];
    $deskripsi = $_POST['deskripsi'];
    
    $query = "INSERT INTO kategori (nama_kategori, deskripsi) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $nama_kategori, $deskripsi);
    
    if(mysqli_stmt_execute($stmt)) {
        header("Location: kategori.php?status=success&message=Kategori berhasil ditambahkan");
    } else {
        header("Location: kategori.php?status=error&message=Gagal menambahkan kategori");
    }
}

function editKategori() {
    global $conn;
    
    $id_kategori = $_POST['id_kategori'];
    $nama_kategori = $_POST['nama_kategori'];
    $deskripsi = $_POST['deskripsi'];
    
    $query = "UPDATE kategori SET nama_kategori=?, deskripsi=? WHERE id_kategori=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssi", $nama_kategori, $deskripsi, $id_kategori);
    
    if(mysqli_stmt_execute($stmt)) {
        header("Location: kategori.php?status=success&message=Kategori berhasil diupdate");
    } else {
        header("Location: kategori.php?status=error&message=Gagal mengupdate kategori");
    }
}

function hapusKategori() {
    global $conn;
    
    $id_kategori = $_GET['id'];
    
    // Check if category has products
    $query = "SELECT COUNT(*) as jumlah FROM produk WHERE id_kategori = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_kategori);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    if($row['jumlah'] > 0) {
        header("Location: kategori.php?status=error&message=Kategori tidak dapat dihapus karena masih memiliki produk");
        exit();
    }
    
    // Delete category if no products
    $query = "DELETE FROM kategori WHERE id_kategori = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_kategori);
    
    if(mysqli_stmt_execute($stmt)) {
        header("Location: kategori.php?status=success&message=Kategori berhasil dihapus");
    } else {
        header("Location: kategori.php?status=error&message=Gagal menghapus kategori");
    }
}
?>