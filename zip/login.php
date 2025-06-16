<?php
session_start();
include('./required/config.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT password FROM register WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_password);
        $stmt->fetch();

        if ($password ===  $db_password) {
            $_SESSION['username'] = $username;
            $_SESSION['toastMessage'] = "Login Successful";
            $_SESSION['toastColor'] = "#28a745";
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['toastMessage'] = "Incorrect password";
            $_SESSION['toastColor'] = "#dc3545";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['toastMessage'] = "Username not found";
        $_SESSION['toastColor'] = "#dc3545";
        header("Location: login.php");
        exit();
    }
}

$message = "";
$toastClass = "";
if (isset($_SESSION['toastMessage']) && isset($_SESSION['toastColor'])) {
    $message = $_SESSION['toastMessage'];
    $toastClass = $_SESSION['toastColor'];
    unset($_SESSION['toastMessage'], $_SESSION['toastColor']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <?php require('./required/css.php') ?>
</head>

<body>
    <?php require('./required/header.php') ?>
    
    <div class="container-fluid login-container">
        <div class="row min-vh-100">
            <!-- Welcome Section -->
            <div class="col-lg-6 col-md-6 d-none d-md-block" id="loginWelcome">
                <div class="loginMessage h-100">
                    <div class="loginContent text-center">
                        <h2 class="mb-4">Welcome To ChatApp</h2>
                        <p class="lead">Connect with friends and family through our secure messaging platform. Experience seamless communication with real-time messaging.</p>
                        <div class="features mt-4">
                            <div class="feature-item mb-2">
                                <i class="fas fa-shield-alt me-2"></i>
                                Secure & Private
                            </div>
                            <div class="feature-item mb-2">
                                <i class="fas fa-bolt me-2"></i>
                                Real-time Messaging
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-users me-2"></i>
                                Connect with Anyone
                            </div>
                        </div>
                    </div>
                    <div class="shapes">
                        <div id="curved-corner-bottomleft"></div>
                        <div id="curved-corner-bottomright"></div>
                        <div id="curved-corner-topleft"></div>
                        <div id="curved-corner-topright"></div>
                    </div>
                </div>
            </div>
            
            <!-- Login Form Section -->
            <div class="col-lg-6 col-md-6 col-12">
                <div class="d-flex justify-content-center align-items-center min-vh-100 p-4">
                    <div class="loginPage w-100">
                        <div class="text-center loginBox">
                            <div class="login-header mb-4">
                                <h3 class="basicColor mb-2">Welcome Back</h3>
                                <p class="text-muted">Sign in to your account</p>
                            </div>
                            
                            <form action="" method="POST">
                                <div class="form-group mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <input type="text" name="username" placeholder="Username" value="abd" 
                                               class="form-control" required>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input type="password" name="password" placeholder="Password" value="12122" 
                                               class="form-control" required>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="rememberMe">
                                        <label class="form-check-label text-muted" for="rememberMe">
                                            Remember me
                                        </label>
                                    </div>
                                    <a href="#" class="text-decoration-none">Forgot password?</a>
                                </div>
                                
                                <button type="submit" class="btn reg-button w-100 py-2 mb-3">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Sign In
                                </button>
                                
                                <div class="text-center">
                                    <span class="text-muted">Don't have an account? </span>
                                    <a href="registration.php" class="text-decoration-none">Sign up</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Section -->
    <?php if (!empty($message)): ?>
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055;">
            <div class="toast align-items-center text-white border-0 show" role="alert" style="background-color: <?= $toastClass ?>;">
                <div class="d-flex">
                    <div class="toast-body"><?= $message ?></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php require('./required/footer.php') ?>
    <?php require('./required/js.php') ?>

    <script>
        let toastElList = [].slice.call(document.querySelectorAll('.toast'));
        let toastList = toastElList.map(toastEl => new bootstrap.Toast(toastEl, { delay: 3000 }));
        toastList.forEach(toast => toast.show());
    </script>
</body>
</html>
