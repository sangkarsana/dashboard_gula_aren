<?php
session_start();

// Cek apakah user sudah login
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<?php
// Koneksi database
require_once 'config.php';

// Query untuk mendapatkan jumlah produk
$query_produk = "SELECT COUNT(*) as total_produk FROM produk";
$result_produk = mysqli_query($conn, $query_produk);
$row_produk = mysqli_fetch_assoc($result_produk);
$total_produk = $row_produk['total_produk'];

// Query untuk mendapatkan jumlah kategori
$query_kategori = "SELECT COUNT(*) as total_kategori FROM kategori";
$result_kategori = mysqli_query($conn, $query_kategori);
$row_kategori = mysqli_fetch_assoc($result_kategori);
$total_kategori = $row_kategori['total_kategori'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Manajemen Produk</title>
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

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 8px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: #c0392b;
        }

        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-box {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-box:hover {
            transform: translateY(-5px);
        }

        .stat-box h3 {
            color: #2c3e50;
            font-size: 18px;
            margin-bottom: 15px;
        }

        .stat-box .number {
            color: #3498db;
            font-size: 36px;
            font-weight: bold;
        }

        .stat-box .icon {
            font-size: 40px;
            color: #3498db;
            margin-bottom: 15px;
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
            
            .dashboard-stats {
                grid-template-columns: 1fr;
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
                <a href="dashboard.php" class="menu-item active">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="produk.php" class="menu-item">
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
                <div class="user-info">
                <i class="fas fa-user-circle" style="font-size: 40px; color: #3498db; margin-right: 10px;"></i>
                    <div>
                        <h3>Welcome, <?php echo $_SESSION['name']; ?></h3>
                        <small><?php echo $_SESSION['username']; ?></small>
                    </div>
                </div>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>

            <div class="dashboard-stats">
                <div class="stat-box">
                    <i class="fas fa-box icon"></i>
                    <h3>Total Produk</h3>
                    <div class="number"><?php echo $total_produk; ?></div>
                </div>
                <div class="stat-box">
                    <i class="fas fa-tags icon"></i>
                    <h3>Total Kategori</h3>
                    <div class="number"><?php echo $total_kategori; ?></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>