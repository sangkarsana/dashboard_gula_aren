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
            tambahProduk();
            break;
        case 'edit':
            editProduk();
            break;
        case 'delete':
            hapusProduk();
            break;
    }
}

function tambahProduk() {
    global $conn;
    
    $id_kategori = $_POST['id_kategori'];
    $nama_produk = $_POST['nama_produk'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    
    // Handle file upload
    $url_gambar = '';
    if(isset($_FILES['url_gambar']) && $_FILES['url_gambar']['error'] == 0) {
        $target_dir = "uploads/";
        $file_extension = strtolower(pathinfo($_FILES["url_gambar"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["url_gambar"]["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($_FILES["url_gambar"]["tmp_name"], $target_file)) {
                $url_gambar = $new_filename;
            }
        }
    }
    
    $query = "INSERT INTO produk (id_kategori, nama_produk, deskripsi, harga, stok, url_gambar) 
              VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "issdis", $id_kategori, $nama_produk, $deskripsi, $harga, $stok, $url_gambar);
    
    if(mysqli_stmt_execute($stmt)) {
        header("Location: produk.php?status=success&message=Produk berhasil ditambahkan");
    } else {
        header("Location: produk.php?status=error&message=Gagal menambahkan produk");
    }
}

function editProduk() {
    global $conn;
    
    $id_produk = $_POST['id_produk'];
    $id_kategori = $_POST['id_kategori'];
    $nama_produk = $_POST['nama_produk'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    
    // Handle file upload if new image is uploaded
    if(isset($_FILES['url_gambar']) && $_FILES['url_gambar']['error'] == 0) {
        $target_dir = "uploads/";
        $file_extension = strtolower(pathinfo($_FILES["url_gambar"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        $check = getimagesize($_FILES["url_gambar"]["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($_FILES["url_gambar"]["tmp_name"], $target_file)) {
                // Delete old image if exists
                $query = "SELECT url_gambar FROM produk WHERE id_produk = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "i", $id_produk);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                
                if($row['url_gambar'] && file_exists($target_dir . $row['url_gambar'])) {
                    unlink($target_dir . $row['url_gambar']);
                }
                
                // Update with new image
                $query = "UPDATE produk SET id_kategori=?, nama_produk=?, deskripsi=?, harga=?, stok=?, url_gambar=? WHERE id_produk=?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "issdisi", $id_kategori, $nama_produk, $deskripsi, $harga, $stok, $new_filename, $id_produk);
            }
        }
    } else {
        // Update without changing image
        $query = "UPDATE produk SET id_kategori=?, nama_produk=?, deskripsi=?, harga=?, stok=? WHERE id_produk=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "issdii", $id_kategori, $nama_produk, $deskripsi, $harga, $stok, $id_produk);
    }
    
    if(mysqli_stmt_execute($stmt)) {
        header("Location: produk.php?status=success&message=Produk berhasil diupdate");
    } else {
        header("Location: produk.php?status=error&message=Gagal mengupdate produk");
    }
}

function hapusProduk() {
    global $conn;
    
    $id_produk = $_GET['id'];
    
    // Get image filename before deleting record
    $query = "SELECT url_gambar FROM produk WHERE id_produk = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_produk);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    // Delete the record
    $query = "DELETE FROM produk WHERE id_produk = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_produk);
    
    if(mysqli_stmt_execute($stmt)) {
        // Delete image file if exists
        if($row['url_gambar'] && file_exists("uploads/" . $row['url_gambar'])) {
            unlink("uploads/" . $row['url_gambar']);
        }
        header("Location: produk.php?status=success&message=Produk berhasil dihapus");
    } else {
        header("Location: produk.php?status=error&message=Gagal menghapus produk");
    }
}
?>