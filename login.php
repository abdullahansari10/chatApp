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
            $_SESSION['toastColor'] = "#28a745"; // Green
            header("Location: index.php"); // Redirect after success
            exit();
        } else {
            $_SESSION['toastMessage'] = "Incorrect password";
            $_SESSION['toastColor'] = "#dc3545"; // Red
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

// Handle toast display only once
$message = "";
$toastClass = "";
if (isset($_SESSION['toastMessage']) && isset($_SESSION['toastColor'])) {
    $message = $_SESSION['toastMessage'];
    $toastClass = $_SESSION['toastColor'];
    unset($_SESSION['toastMessage'], $_SESSION['toastColor']); // Clear after showing
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <?php require('./required/css.php') ?>
</head>
<?php require('./required/header.php') ?>

<body>
    <div class="container-fluid">
        <div class="row h-100">
            <div class="col-lg-6" id="loginWelcome">
                <div class="loginMessage h-100">
                    <div class="loginContent">
                        <h2>Welcome To The Website</h2>
                        <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Praesentium perferendis error qui nostrum facere porro odio, ullam distinctio voluptatum perspiciatis consequuntur unde molestias dignissimos sed eum itaque nihil nam vel?</p>
                    </div>
                    <div class="shapes">
                        <div id="curved-corner-bottomleft"></div>
                        <div id="curved-corner-bottomright"></div>
                        <div id="curved-corner-topleft"></div>
                        <div id="curved-corner-topright"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-flex justify-content-center align-items-center h-100">
                    <div class="loginPage">
                        <div class="text-center loginBox">
                            <h3 class="basicColor">Log in</h3>
                            <form action="" method="POST">
                                <div class="py-4">
                                    <input type="text" name="username" placeholder="Username" value="abd" class="rounded-1 border-1 p-1 px-3" required>
                                </div>
                                <div>
                                    <input type="password" name="password" placeholder="Password" value="12122" class="rounded-1 border-1 p-1 px-3" required>
                                </div>
                                <div id="sign-up">
                                    <a href="registration.php">Sign-up</a>
                                </div>
                                <div class="text-center py-4">
                                    <button type="submit" class="reg-button border-0 p-2 text-white rounded-1 w-100">Login</button>
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
