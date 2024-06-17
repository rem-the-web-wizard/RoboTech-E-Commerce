<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Seller Sign Up</title>
    <link rel="stylesheet" href="css/bootstrap.css" />
    <style>
    body {
        background-color: #f8f9fa;
        font-family: 'Rubik', sans-serif;
    }

    .sign-up-container {
        max-width: 600px;
        margin: auto;
        padding: 20px;
        background-color: white;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        margin-top: 50px;
    }

    .sign-up-container h2 {
        margin-bottom: 20px;
        color: #343a40;
    }

    .form-control {
        border-radius: 4px;
    }

    .btn-custom {
        border-radius: 4px;
        padding: 10px 20px;
    }

    .btn-success {
        background-color: #28a745;
        border: none;
    }

    .btn-success:hover {
        background-color: #218838;
    }

    .btn-info {
        background-color: #17a2b8;
        border: none;
    }

    .btn-info:hover {
        background-color: #138496;
    }

    .alert {
        margin-top: 20px;
        padding: 15px;
        border-radius: 4px;
        font-size: 14px;
        text-align: left;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }

    .alert-warning {
        background-color: #fff3cd;
        color: #856404;
    }

    .alert-info {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }

    a {
        color: white;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <?php

include 'function/con.php';

$errorMsg = "";
$shopname = "";
$firstname = "";
$lastname = "";
$email = "";
$contact = "";
$username = "";
$password = "";

if (isset($_POST['sign-up'])) {
    $shopname = $_POST['shopname'];
    $firstname = $_POST['first_name'];
    $lastname = $_POST['last_name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $ver = "SELECT * FROM `seller_tbl` WHERE 1";
    $total = mysqli_query($con, $ver);

    // Get the total number of customers
    $totalCustomers = mysqli_num_rows($total);

    // Check if the shop name already exists
    $query = "SELECT * FROM `seller_tbl` WHERE `shop_name` = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $shopname);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errorMsg = "Shop Name already exists. Please choose another Shop Name.";
    } else {
        // Check if the username already exists
        $query1 = "SELECT * FROM `seller_tbl` WHERE `seller_username` = ?";
        $stmt1 = $con->prepare($query1);
        $stmt1->bind_param("s", $username);
        $stmt1->execute();
        $result1 = $stmt1->get_result();

        if ($result1->num_rows > 0) {
            $errorMsg = "Username already exists. Please choose another username.";
        } else {
            // Insert the new seller
            $customerID = generateCustomerID($totalCustomers);
            $verificationCode = generateVerificationCode();

            $query2 = "INSERT INTO `seller_tbl`(`seller_id`, `shop_name`, `seller_first_name`, `seller_last_name`, `seller_email`, `verification_code`, `seller_contact`, `seller_username`, `seller_password`) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt2 = $con->prepare($query2);
            $stmt2->bind_param("sssssssss", $customerID, $shopname, $firstname, $lastname, $email, $verificationCode, $contact, $username, $hashedPassword);

            if ($stmt2->execute()) {
                header("Location: seller_login.php");
                exit();
            } else {
                $errorMsg = "Error registering user: " . mysqli_error($con);
            }
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
    $date = date('md');
    $customerID = $year . $date . str_pad($totalCustomers, 2, '0', STR_PAD_LEFT);
    return $customerID;
}
?>

    <div class="container mt-5">
        <div class="sign-up-container">
            <h2>Seller Sign Up</h2>
            <?php if ($errorMsg) echo "<div class='alert alert-danger'>$errorMsg</div>"; ?>
            <form method="post">
                <div class="mb-3">
                    <label for="shopname" class="form-label">Shop Name</label>
                    <input type="text" name="shopname" placeholder="Enter Shop Name" class="form-control"
                        value="<?php echo $shopname; ?>" required />
                </div>
                <div class="mb-3">
                    <label for="first_name" class="form-label">Seller First Name</label>
                    <input type="text" name="first_name" placeholder="Enter Seller First Name" class="form-control"
                        value="<?php echo $firstname; ?>" required />
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Seller Last Name</label>
                    <input type="text" name="last_name" placeholder="Enter Seller Last Name" class="form-control"
                        value="<?php echo $lastname; ?>" required />
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Seller Email</label>
                    <input type="email" name="email" placeholder="Enter Seller Email Address" class="form-control"
                        value="<?php echo $email; ?>" required />
                </div>
                <div class="mb-3">
                    <label for="contact" class="form-label">Seller Contact Number</label>
                    <input type="text" name="contact" placeholder="Enter Seller Contact Number" class="form-control"
                        value="<?php echo $contact; ?>" required />
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Seller Username</label>
                    <input type="text" name="username" placeholder="Enter Seller Username" class="form-control"
                        value="<?php echo $username; ?>" required />
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Seller Password</label>
                    <input type="password" name="password" placeholder="Enter Seller Password" class="form-control"
                        required />
                </div>
                <div class="d-flex justify-content-between">
                    <button id="Signup" name="sign-up" class="btn btn-success btn-custom">Sign Up</button>
                    <button id="Login" name="Login" class="btn btn-info btn-custom">
                        <a href="seller_login.php" style="color: white;">I have an Account</a>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>