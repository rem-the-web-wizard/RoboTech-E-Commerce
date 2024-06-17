<?php
include 'function/con.php';

$errorMsg = "";
$firstname = "";
$lastname = "";
$email = "";
$contact = "";
$address = "";
$birthday = "";
$age = "";
$username = "";
$password = "";

if (isset($_POST['sign-up'])) {
    $firstname = $_POST['first_name'];
    $lastname = $_POST['last_name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $birthday = $_POST['birthday'];
    $age = $_POST['age'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if the username already exists using prepared statement
    $query = $con->prepare("SELECT * FROM `customer_tbl` WHERE `username` = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $errorMsg = "Username already exists. Please choose another username.";
    } else {
        $ver = "SELECT * FROM `customer_tbl`";
        $total = mysqli_query($con, $ver);
        $totalCustomers = mysqli_num_rows($total);
        $customerID = generateCustomerID($totalCustomers);
        $verificationCode = generateVerificationCode();

        $query = $con->prepare("INSERT INTO `customer_tbl`(`customers_id`, `customer_first_name`, `customer_last_name`, `customer_email`, `verification_code`, `customer_contact`, `customer_address`, `customer_birthday`, `customer_age`, `username`, `password`) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $query->bind_param("sssssssssss", $customerID, $firstname, $lastname, $email, $verificationCode, $contact, $address, $birthday, $age, $username, $hashedPassword);

        if ($query->execute()) {
            header("Location: customer_login.php");
            exit;
        } else {
            $errorMsg = "Error registering user: " . $query->error;
        }
    }
}

function generateVerificationCode($length = 5) {
    $characters = '0123456789';
    $code = '';

    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $code;
}

function generateCustomerID($totalCustomers) {
    $year = date('Y');
    $month = date('m');
    $customerID = $year . $month . str_pad($totalCustomers + 1, 2, '0', STR_PAD_LEFT);

    return $customerID;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign Up</title>
    <link rel="stylesheet" href="css/bootstrap.css" />
    <style>
    .alert-red {
        padding: 20px;
        background-color: #f44336;
        color: white;
        padding: 20px 40px;
        min-width: 420px;
        position: absolute;
        right: 0px;
        top: 10px;
        border-radius: 4px;
        border-left: 10px solid #f44336;
        animation: show_slide 1s ease forwards;
    }

    .alert-green {
        padding: 20px;
        background-color: #61f21e;
        color: white;
        padding: 20px 40px;
        min-width: 420px;
        position: absolute;
        right: 0px;
        top: 10px;
        border-radius: 4px;
        border-left: 10px solid #46a819;
        animation: show_slide 1s ease forwards;
    }

    .alert-blue {
        padding: 20px;
        background-color: #3d7ff1;
        color: white;
        padding: 20px 40px;
        min-width: 420px;
        position: absolute;
        right: 0px;
        top: 10px;
        border-radius: 4px;
        border-left: 10px solid #264b8d;
        animation: show_slide 1s ease forwards;
    }

    @keyframes show_slide {
        0% {
            transform: translateX(100%);
        }

        40% {
            transform: translateX(-10%);
        }

        80% {
            transform: translateX(0%);
        }

        100% {
            transform: translateX(0%);
        }
    }

    .closebtn {
        margin-left: 15px;
        color: white;
        font-weight: bold;
        float: right;
        font-size: 22px;
        line-height: 20px;
        cursor: pointer;
        transition: 0.3s;
    }

    .closebtn:hover {
        color: black;
    }

    .sign-up-container {
        max-width: 600px;
        margin: 50px auto;
        padding: 30px;
        background-color: #f4f4f6;
        border-radius: 10px;
        box-shadow: -2px 2px 5px 0px rgba(0, 0, 0, 0.35);
    }

    .sign-up-item {
        padding: 20px;
    }

    .form-horizontal {
        width: 100%;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .control-label {
        text-align: left;
    }

    .btn-success {
        margin-right: 10px;
    }

    .btn-info a {
        color: white;
        text-decoration: none;
    }
    </style>
</head>

<body>

    <?php require 'navbar.php'; ?>
    <center>
        <div class="sign-up-container">
            <div class="sign-up-item">
                <?php if ($errorMsg): ?>
                <div class="alert alert-red">
                    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                    <?php echo $errorMsg; ?>
                </div>
                <?php endif; ?>
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend>Sign Up</legend>
                        <hr>
                        <div class="form-group">
                            <label class="col-md-4 control-label">First Name</label>
                            <div class="col-md-5">
                                <input type="text" name="first_name" placeholder="Enter First Name"
                                    class="form-control input-md" value="<?php echo $firstname; ?>" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Last Name</label>
                            <div class="col-md-5">
                                <input type="text" name="last_name" placeholder="Enter Last Name"
                                    class="form-control input-md" value="<?php echo $lastname; ?>" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Email</label>
                            <div class="col-md-5">
                                <input type="email" name="email" placeholder="Enter Email Address"
                                    class="form-control input-md" value="<?php echo $email; ?>" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Contact Number</label>
                            <div class="col-md-5">
                                <input type="text" name="contact" placeholder="Enter Contact Number"
                                    class="form-control input-md" value="<?php echo $contact; ?>" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Address</label>
                            <div class="col-md-5">
                                <input type="text" name="address" placeholder="Enter Address"
                                    class="form-control input-md" value="<?php echo $address; ?>" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Birthday</label>
                            <div class="col-md-5">
                                <input type="date" name="birthday" id="birthday" placeholder="Enter Birthday"
                                    class="form-control input-md" value="<?php echo $birthday; ?>" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Age</label>
                            <div class="col-md-5">
                                <input type="text" name="age" id="age" placeholder="Enter Age"
                                    class="form-control input-md" value="<?php echo $age; ?>" required readonly />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Username</label>
                            <div class="col-md-5">
                                <input type="text" name="username" placeholder="Enter Username"
                                    class="form-control input-md" value="<?php echo $username; ?>" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Password</label>
                            <div class="col-md-5">
                                <input type="password" name="password" placeholder="Enter Password"
                                    class="form-control input-md" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="log-in"></label>
                            <div class="col-md-8">
                                <button id="Signup" name="sign-up" class="btn btn-success">Sign Up</button>
                                <button id="Login" name="Login" class="btn btn-info">
                                    <a href="customer_login.php">I already have an account</a>
                                </button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </center>

    <?php require 'footer.php'; ?>
    <script src="css/bootstrap_js.js"></script>
    <script>
    function computeAge() {
        var birthday = document.getElementById("birthday").value;
        var today = new Date();
        var birthDate = new Date(birthday);
        var age = today.getFullYear() - birthDate.getFullYear();
        var monthDiff = today.getMonth() - birthDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        document.getElementById("age").value = age;
    }

    document.getElementById("birthday").addEventListener("input", computeAge);
    </script>
</body>

</html>