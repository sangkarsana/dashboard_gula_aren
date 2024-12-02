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

// Query untuk mengambil data kategori
$query = "SELECT k.*, COUNT(p.id_produk) as jumlah_produk 
          FROM kategori k 
          LEFT JOIN produk p ON k.id_kategori = p.id_kategori 
          GROUP BY k.id_kategori 
          ORDER BY k.id_kategori DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Kategori - Manajemen Gula Aren</title>
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
            display: inline-flex;
            align-items: center;
            gap: 5px;
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

        .kategori-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .form-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: none;
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

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-primary {
            background: #3498db;
            color: white;
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
                <a href="produk.php" class="menu-item">
                    <i class="fas fa-box"></i>
                    <span>Kelola Produk</span>
                </a>
                <a href="kategori.php" class="menu-item active">
                    <i class="fas fa-tags"></i>
                    <span>Kelola Kategori</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="top-bar">
                <h2>Kelola Kategori</h2>
                <button class="btn btn-primary" onclick="showAddForm()">
                    <i class="fas fa-plus"></i> Tambah Kategori
                </button>
            </div>

            <!-- Form Tambah/Edit Kategori -->
            <div id="kategoriForm" class="form-container">
                <h3 id="formTitle">Tambah Kategori</h3>
                <form action="kategori_action.php" method="POST">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id_kategori" id="id_kategori">
                    
                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text" name="nama_kategori" id="nama_kategori" required>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="4"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-danger" onclick="hideForm()">
                        <i class="fas fa-times"></i> Batal
                    </button>
                </form>
            </div>

            <!-- Tabel Kategori -->
            <div class="kategori-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th>Jumlah Produk</th>
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
                            <td><?php echo $row['nama_kategori']; ?></td>
                            <td><?php echo $row['deskripsi']; ?></td>
                            <td>
                                <span class="badge badge-primary">
                                    <?php echo $row['jumlah_produk']; ?> Produk
                                </span>
                            </td>
                            <td>
                                <button onclick="editKategori(<?php echo htmlspecialchars(json_encode($row)); ?>)" 
                                        class="btn btn-warning">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if($row['jumlah_produk'] == 0) : ?>
                                    <a href="kategori_action.php?action=delete&id=<?php echo $row['id_kategori']; ?>" 
                                       class="btn btn-danger"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
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
            document.getElementById('kategoriForm').style.display = 'block';
            document.getElementById('formTitle').innerHTML = 'Tambah Kategori';
            document.getElementById('formAction').value = 'add';
            // Reset form
            document.getElementById('id_kategori').value = '';
            document.getElementById('nama_kategori').value = '';
            document.getElementById('deskripsi').value = '';
        }

        function hideForm() {
            document.getElementById('kategoriForm').style.display = 'none';
        }

        function editKategori(data) {
            document.getElementById('kategoriForm').style.display = 'block';
            document.getElementById('formTitle').innerHTML = 'Edit Kategori';
            document.getElementById('formAction').value = 'edit';
            
            // Fill form with category data
            document.getElementById('id_kategori').value = data.id_kategori;
            document.getElementById('nama_kategori').value = data.nama_kategori;
            document.getElementById('deskripsi').value = data.deskripsi;
        }
    </script>
</body>
</html>