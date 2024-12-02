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

// Query untuk mengambil data produk beserta nama kategorinya
$query = "SELECT p.*, k.nama_kategori 
          FROM produk p 
          JOIN kategori k ON p.id_kategori = k.id_kategori 
          ORDER BY p.id_produk DESC";
$result = mysqli_query($conn, $query);

// Query untuk dropdown kategori
$query_kategori = "SELECT * FROM kategori";
$kategori_list = mysqli_query($conn, $query_kategori);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Produk - Manajemen Gula Aren</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            padding-top: 20px;
            position: fixed;
            left: 0;
            height: 100vh;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #34495e;
        }

        .sidebar-header h2 {
            color: #3498db;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            padding: 15px 25px;
            display: flex;
            align-items: center;
            color: #ecf0f1;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .menu-item:hover {
            background: #34495e;
            border-left: 4px solid #3498db;
        }

        .menu-item.active {
            background: #34495e;
            border-left: 4px solid #3498db;
        }

        .menu-item i {
            margin-right: 10px;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            background: #f5f6fa;
        }

        .top-bar {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            font-size: 14px;
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-success {
            background: #2ecc71;
            color: white;
        }

        .btn-warning {
            background: #f1c40f;
            color: black;
        }

        .btn-danger {
            background: #e74c3c;
            color: white;
        }

        .product-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background: #f8f9fa;
            color: #2c3e50;
        }

        .table tr:hover {
            background: #f8f9fa;
        }

        .form-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: none;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar-header h2,
            .menu-item span {
                display: none;
            }
            
            .main-content {
                margin-left: 70px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>GulaAren</h2>
            </div>
            <div class="sidebar-menu">
                <a href="dashboard.php" class="menu-item">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="produk.php" class="menu-item active">
                    <i class="fas fa-box"></i>
                    <span>Kelola Produk</span>
                </a>
                <a href="kategori.php" class="menu-item">
                    <i class="fas fa-tags"></i>
                    <span>Kelola Kategori</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="top-bar">
                <h2>Kelola Produk</h2>
                <button class="btn btn-primary" onclick="showAddForm()">
                    <i class="fas fa-plus"></i> Tambah Produk
                </button>
            </div>

            <!-- Form Tambah/Edit Produk -->
            <div id="productForm" class="form-container">
                <h3 id="formTitle">Tambah Produk</h3>
                <form action="produk_action.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id_produk" id="id_produk">
                    
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="id_kategori" required>
                            <?php while($kategori = mysqli_fetch_assoc($kategori_list)) : ?>
                                <option value="<?php echo $kategori['id_kategori']; ?>">
                                    <?php echo $kategori['nama_kategori']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nama Produk</label>
                        <input type="text" name="nama_produk" id="nama_produk" required>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="4"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Harga</label>
                        <input type="number" name="harga" id="harga" required>
                    </div>

                    <div class="form-group">
                        <label>Stok</label>
                        <input type="number" name="stok" id="stok" required>
                    </div>

                    <div class="form-group">
                        <label>Gambar Produk</label>
                        <input type="file" name="url_gambar" id="url_gambar">
                    </div>

                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-danger" onclick="hideForm()">Batal</button>
                </form>
            </div>

            <!-- Tabel Produk -->
            <div class="product-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while($row = mysqli_fetch_assoc($result)) : 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <?php if($row['url_gambar']) : ?>
                                    <img src="uploads/<?php echo $row['url_gambar']; ?>" 
                                         alt="<?php echo $row['nama_produk']; ?>" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                <?php else : ?>
                                    <i class="fas fa-image" style="color: #ccc; font-size: 24px;"></i>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $row['nama_produk']; ?></td>
                            <td><?php echo $row['nama_kategori']; ?></td>
                            <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                            <td><?php echo $row['stok']; ?></td>
                            <td>
                                <button onclick="editProduk(<?php echo htmlspecialchars(json_encode($row)); ?>)" 
                                        class="btn btn-warning">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="produk_action.php?action=delete&id=<?php echo $row['id_produk']; ?>" 
                                   class="btn btn-danger"
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function showAddForm() {
            document.getElementById('productForm').style.display = 'block';
            document.getElementById('formTitle').innerHTML = 'Tambah Produk';
            document.getElementById('formAction').value = 'add';
            // Reset form
            document.getElementById('id_produk').value = '';
            document.getElementById('nama_produk').value = '';
            document.getElementById('deskripsi').value = '';
            document.getElementById('harga').value = '';
            document.getElementById('stok').value = '';
        }

        function hideForm() {
            document.getElementById('productForm').style.display = 'none';
        }

        function editProduk(data) {
            document.getElementById('productForm').style.display = 'block';
            document.getElementById('formTitle').innerHTML = 'Edit Produk';
            document.getElementById('formAction').value = 'edit';
            
            // Fill form with product data
            document.getElementById('id_produk').value = data.id_produk;
            document.getElementById('nama_produk').value = data.nama_produk;
            document.getElementById('deskripsi').value = data.deskripsi;
            document.getElementById('harga').value = data.harga;
            document.getElementById('stok').value = data.stok;
            
            // Set selected kategori
            const kategoriSelect = document.querySelector('select[name="id_kategori"]');
            for(let i = 0; i < kategoriSelect.options.length; i++) {
                if(kategoriSelect.options[i].value == data.id_kategori) {
                    kategoriSelect.options[i].selected = true;
                    break;
                }
            }
        }
    </script>
</body>
</html>