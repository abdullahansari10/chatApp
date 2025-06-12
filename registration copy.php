<?php
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
        // echo $mobile_number;
        $gender = $_POST['gender'];
        // echo $gender;

        $male_status = 'unchecked';
        $female_status = 'unchecked';
        $other_status = 'unchecked';

        // if (isset($_POST['submit'])) {

        $selected_radio = $_POST['gender'];

        if ($selected_radio == 'male') {

            $male_status = 'checked';
        } else if ($selected_radio == 'female') {

            $female_status = 'checked';
        } else if ($selected_radio == 'other') {

            $female_status = 'checked';
        }
        // }

        // check for same entry
        // $checkForAll = $conn->prepare("SELECT name, email, username, mobile_number from register where name = ?, email= ?, username= ? and mobile_number= ?");
        // $checkForAll->bind_param("ssss", $name, $email, $username, $mobile_number);
        $checkForAll = $conn->prepare("SELECT email from register where email=?");
        $checkForAll->bind_param("s", $email);
        $checkForAll->execute();
        $checkForAll->store_result();
        if ($checkForAll->num_rows > 0) {
            $message = "Already Exist";
            $toastClass = "#007bff";
        } else {
            $stmt = $conn->prepare("INSERT INTO register(name, email, username, password, mobile_number, gender) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param('ssssss', $name, $email, $username, $password, $mobile_number, $gender);
            if ($stmt->execute()) {
                $message = "Account created successfully";
                $toastClass = "#28a745";
            } else {
                $message = "Error: " . $stmt->error;
                $toastClass = "#dc3545";
            }
        }
    }
}

?>
<!-- html -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php require('./required/css.php') ?>
</head>
<?php require('./required/header.php') ?>
<!-- body -->

<body>
    <div class="container-fluid registration">
        <div class="row h-100">
            <div class="col-lg-12">
                <div class="d-flex justify-content-center align-item-center vh-100">
                    <div class="registration-box">
                        <div class="reg-box">
                            <h3 class="">Registration</h3>
                            <form method="POST">
                                <div class="d-flex">
                                    <div class="col-lg-5 m-1">
                                        <div class="" style="margin: 0 0 15px 0">
                                            <label class="">Full Name</label> <br>
                                            <input type="text" name="name" id="name" placeholder="Enter your Name" class="w-100">
                                        </div>
                                        <div class="" style="margin: 0 0 15px 0">
                                            <label class="">Email</label> <br>
                                            <input type="text" name="email" id="email" placeholder="Enter your Email" class="w-100">
                                        </div>
                                        <div class="" style="margin: 0 0 15px 0">
                                            <label class="">Password</label> <br>
                                            <input type="text" name="password" id="password" placeholder="Enter your Password" class="w-100">
                                        </div>
                                        <div class="">
                                            <label class="gender">Gender</label> <br>
                                            <span><input type="radio" id="male" name="gender" value="male"> Male</span>
                                            <span><input type="radio" id="female" name="gender" value="female"> Female</span>
                                            <span><input type="radio" id="other" name="gender" value="other"> Other</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-5 mx-5 my-1">
                                        <div class="" style="margin: 0 0 15px 0">
                                            <label class="">Username</label> <br>
                                            <input type="text" name="username" id="username" placeholder="Enter your Username" class="w-100">
                                        </div>
                                        <div class="" style="margin: 0 0 15px 0">
                                            <label class="">Mobile Number</label> <br>
                                            <input type="text" name="mobile_number" id="mobile_number" placeholder="Enter your Mobile Number" class="w-100">
                                        </div>
                                        <div class="" style="margin: 0 0 15px 0">
                                            <label class="">Confirm Password</label> <br>
                                            <input type="text" id="confirmPassword" placeholder="Enter your Confirm Password" class="w-100">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center my-4 fw-bold mx-0">
                                    <button
                                        type="submit"
                                        class="reg-button text-white w-100 border-0">
                                        Submit
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- toast -->
    <?php if (!empty($message)): ?>
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055;">
            <div class="toast align-items-center text-white border-0 show" role="alert" aria-live="assertive" aria-atomic="true"
                style="background-color: <?php echo $toastClass; ?>;">
                <div class="d-flex">
                    <div class="toast-body">
                        <?php echo $message; ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <!--  -->
    <?php require('./required/footer.php') ?>
</body>
<?php require('./required/js.php') ?>
<script>
    let reg_button = document.querySelector('.reg-button')
    let toastElList = [].slice.call(document.querySelectorAll('.toast'))
    let toastList = toastElList.map(function(toastEl) {
        return new bootstrap.Toast(toastEl, {
            delay: 3000
        });
    });
    toastList.forEach(toast => toast.show());
</script>

</html>