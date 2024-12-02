<?php
session_start();
require_once 'config.php';

if(isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $query = "SELECT * FROM users WHERE username='$username' AND password=PASSWORD('$password')";
    $result = mysqli_query($conn, $query);
    
    if(mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['name'] = $row['name'];
            
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Manajemen Gula Aren</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(120deg, #3498db, #2980b9);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 15px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h2 {
            color: #2980b9;
            font-size: 2em;
            margin-bottom: 10px;
        }

        .login-header p {
            color: #666;
            font-size: 0.9em;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #555;
            margin-bottom: 5px;
            font-size: 0.9em;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e3e3e3;
            border-radius: 5px;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3498db;
        }

        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.9em;
            text-align: center;
        }

        .submit-btn {
            background: #3498db;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            width: 100%;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .submit-btn:hover {
            background: #2980b9;
        }

        .brand {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 0.8em;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 20px;
            }

            .login-header h2 {
                font-size: 1.5em;
            }
        }

        /* Animasi untuk input fields */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .error-shake {
            animation: shake 0.5s;
            border-color: #c62828 !important;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2>Selamat Datang</h2>
            <p>Silahkan login untuk melanjutkan</p>
        </div>

        <?php if(isset($error)) { ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php } ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required 
                       placeholder="Masukkan username">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required 
                       placeholder="Masukkan password">
            </div>

            <button type="submit" name="login" class="submit-btn">Login</button>
        </form>

        <div class="brand">
            &copy; 2024 Manajemen Gula Aren
        </div>
    </div>

    <script>
        // Menambahkan efek shake pada form jika ada error
        <?php if(isset($error)) { ?>
            document.querySelectorAll('input').forEach(input => {
                input.classList.add('error-shake');
                setTimeout(() => {
                    input.classList.remove('error-shake');
                }, 500);
            });
        <?php } ?>
    </script>
</body>
</html>