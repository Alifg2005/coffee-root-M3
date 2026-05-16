<?php
session_start();

$host = 'localhost';
$dbname = 'coffee_roots';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $dbname :" . $e->getMessage());
}

$error_msg = "";
$success_msg = "";

// --- HANDLE LOGIN SUBMISSION ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_submit'])) {
    $email = $_POST['email']; 
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    // Note: Using standard == check because your database insert used plain text passwords (like "ali")
    if ($user && $password === $user['users_password']) {
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['user_type']; // using your db column name
        $_SESSION['first_name'] = $user['first_name'];
        
        if ($user['user_type'] == 'admin') {
            header("Location: admin-dashboard.php");
        } else {
            header("Location: Main.php");
        }
        exit();
    } else {
        $error_msg = "Invalid email or password!";
    }
}

// --- HANDLE REGISTER SUBMISSION ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_submit'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $password = $_POST['password']; // In a real app you would use password_hash() here

    try {
        // user_type defaults to 'customer'
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, phone_number, email, users_password, user_type) VALUES (?, ?, ?, ?, ?, 'customer')");
        $stmt->execute([$first_name, $last_name, $phone_number, $email, $password]);
        
        $success_msg = "Registration successful! Please login.";
    } catch (PDOException $e) {
        $error_msg = "Registration failed. That email or phone number might already exist.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Signup form</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../css/loginstyle.css">
    <link rel="stylesheet" href="../css/Super-style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    
    <style>
        body {
            display: block; /* Turns off the horizontal Flexbox row */
            padding-top: 80px; /* Makes room for the fixed navbar */
            background: #F5F2EE;
        }
        #navbar-placeholder, #footer-placeholder {
            width: 100%;
        }
        .container {
            margin: 60px auto; /* Perfectly centers your login box horizontally */
        }
    </style>
</head>
<body>

    <div id="navbar-placeholder"></div>
    <div class="container">        
        <input type="checkbox" id="toggle-state" style="display: none;">

        <?php if($error_msg): ?>
            <p style="color: red; text-align: center; position: absolute; top: 10px; width: 100%; z-index: 10; font-weight: bold;"><?= $error_msg ?></p>
        <?php endif; ?>
        <?php if($success_msg): ?>
            <p style="color: green; text-align: center; position: absolute; top: 10px; width: 100%; z-index: 10; font-weight: bold;"><?= $success_msg ?></p>
        <?php endif; ?>

        <div class="form-box login"> 
            <form action="login.php" method="POST" id="loginForm" onsubmit="return validateLogin()">
                <img src="../img/Coffee Roots.png" alt="Logo" class="logo">
                <h1>Login</h1>
                <div class="input-box">
                    <input type="email" name="email" id="loginEmail" placeholder="Email" required>
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" id="loginPassword" placeholder="Password" required>
                    <i class="fa-solid fa-lock"></i>
                </div>
                <div class="forgot-password">
                    <a href="#">Forgot Password?</a>
                </div>
                <button type="submit" name="login_submit" class="btn">Login</button> 
            </form>
        </div>

        <div class="form-box register"> 
            <form action="login.php" method="POST"> 
                <img src="../img/Coffee Roots.png" alt="Logo" class="logo">
                <h1>Register</h1>
                
                <div style="display: flex; gap: 10px; margin: 20px 0;">
                    <div class="input-box" style="margin: 0;">
                        <input type="text" name="first_name" placeholder="First Name" required style="padding-left: 10px;">
                    </div>
                    <div class="input-box" style="margin: 0;">
                        <input type="text" name="last_name" placeholder="Last Name" required style="padding-left: 10px;">
                    </div>
                </div>

                <div class="input-box" style="margin-top: 10px;">
                    <input type="tel" name="phone_number" placeholder="Phone Number" required>
                    <i class="fa-solid fa-phone"></i>
                </div>

                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required>
                    <i class="fa-solid fa-envelope"></i>
                </div>

                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class="fa-solid fa-lock"></i>
                </div>
                <button type="submit" name="register_submit" class="btn">Register</button> 
            </form>
        </div>

        <div class="toggle-box"> 
             <div class="toggle-box-left"> 
                <h1>Hello, Welcome!</h1>
                <p>Don't have an account?</p>
                <label for="toggle-state" class="btn register-btn">Register</label>
            </div>
            <div class="toggle-box-right">
                <h1>Welcome Back!</h1>
                <p> Already have an account? </p>
                <label for="toggle-state" class="btn login-btn">Login</label>
            </div>
        </div>

    </div>

    <div id="footer-placeholder"></div>

    <script>
        const pageType = "guest";
    </script>
    <script src="../js/components.js"></script>

    <script>
        function validateLogin() {
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            
            if(email.trim() === "" || password.trim() === "") {
                alert("Please fill in both email and password.");
                return false; 
            }
            return true;
        }
    </script>
</body>
</html>