<?php
session_start();
include('./required/config.php');

$message = "";
$toastClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['name'], $_POST['email'], $_POST['username'], $_POST['password'], $_POST['mobile_number'], $_POST['gender'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $mobile_number = $_POST['mobile_number'];
        $gender = $_POST['gender'];

        $checkForAll = $conn->prepare("SELECT email FROM register WHERE email = ?");
        $checkForAll->bind_param("s", $email);
        $checkForAll->execute();
        $checkForAll->store_result();

        if ($checkForAll->num_rows > 0) {
            $_SESSION['message'] = "Email already exists!";
            $_SESSION['toastClass'] = "#007bff";
        } else {
            $stmt = $conn->prepare("INSERT INTO register(name, email, username, password, mobile_number, gender) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $name, $email, $username, $password, $mobile_number, $gender);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Account created successfully!";
                $_SESSION['toastClass'] = "#28a745";
                $_SESSION['success_redirect'] = true;
                $_SESSION['username'] = $username;  //pass username in index page

                // header("location: login.php");
                // exit();
            } else {
                $_SESSION['message'] = "Error: " . $stmt->error;
                $_SESSION['toastClass'] = "#dc3545";
            }
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $toastClass = $_SESSION['toastClass'];
    $success_redirect = $_SESSION['success_redirect'] ?? false;
    unset($_SESSION['message'], $_SESSION['toastClass']);
} else {
    $success_redirect = false;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <?php require('./required/css.php') ?>
</head>

<body>
    <?php require('./required/header.php') ?>

    <div class="container-fluid registration">
        <div class="row h-100">
            <div class="col-lg-12">
                <div class="d-flex justify-content-center align-items-center vh-100">
                    <div class="registration-box">
                        <div class="reg-box">
                            <h3>Registration</h3>
                            <form method="POST" action="">
                                <div class="d-flex">
                                    <div class="col-lg-5 m-1">
                                        <div class="mb-3">
                                            <label>Full Name</label><br>
                                            <input type="text" name="name" value="<?= $_POST['name'] ?? '' ?>" placeholder="Enter your Name" class="w-100">
                                        </div>
                                        <div class="mb-3">
                                            <label>Email</label><br>
                                            <input type="email" name="email" value="<?= $_POST['email'] ?? '' ?>" placeholder="Enter your Email" class="w-100">
                                        </div>
                                        <div class="mb-3">
                                            <label>Password</label><br>
                                            <input type="text" name="password" placeholder="Enter your Password" class="w-100">
                                        </div>
                                        <div class="mb-3">
                                            <label>Gender</label><br>
                                            <label><input type="radio" name="gender" value="male" <?= (($_POST['gender'] ?? '') === 'male') ? 'checked' : '' ?>> Male</label>
                                            <label><input type="radio" name="gender" value="female" <?= (($_POST['gender'] ?? '') === 'female') ? 'checked' : '' ?>> Female</label>
                                            <label><input type="radio" name="gender" value="other" <?= (($_POST['gender'] ?? '') === 'other') ? 'checked' : '' ?>> Other</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-5 mx-5 my-1">
                                        <div class="mb-3">
                                            <label>Username</label><br>
                                            <input type="text" name="username" value="<?= $_POST['username'] ?? '' ?>" placeholder="Enter your Username" class="w-100">
                                        </div>
                                        <div class="mb-3">
                                            <label>Mobile Number</label><br>
                                            <input type="text" name="mobile_number" value="<?= $_POST['mobile_number'] ?? '' ?>" placeholder="Enter your Mobile Number" class="w-100">
                                        </div>
                                        <div class="mb-3">
                                            <label>Confirm Password</label><br>
                                            <input type="text" id="confirmPassword" placeholder="Enter your Confirm Password" class="w-100">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center my-4 fw-bold mx-0">
                                    <button type="submit" class="reg-button text-white w-100 border-0">Submit</button>
                                </div>
                            </form>

                            <?php if (!empty($message)): ?>
                                <div id="successModal" class="position-fixed top-0 end-0 p-3" style="z-index: 1055;">
                                    <div class="toast align-items-center text-white border-0 show"
                                        role="alert" aria-live="assertive" aria-atomic="true"
                                        style="background-color: <?= $toastClass ?>;">
                                        <div class="d-flex">
                                            <div class="toast-body">
                                                <?= $message ?>
                                            </div>
                                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    $(document).ready(function() {
                                        setTimeout(() => {
                                            // $('#successModal').fadeOut(500);
                                            <?php if ($success_redirect): ?>
                                                window.location.href = 'index.php'
                                            <?php endif; ?>
                                        }, 4000);
                                    })
                                </script>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require('./required/footer.php') ?>
    <?php require('./required/js.php') ?>

    <script>
        // Auto-show toast if present
        let toastElList = [].slice.call(document.querySelectorAll('.toast'))
        let toastList = toastElList.map(function(toastEl) {
            return new bootstrap.Toast(toastEl, {
                delay: 3000
            });
        });
        toastList.forEach(toast => toast.show());
    </script>

</body>

</html>