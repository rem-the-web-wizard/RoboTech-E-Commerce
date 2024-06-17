<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Seller Login</title>
    <link rel="stylesheet" href="css/bootstrap.css" />
    <style>
    body {
        background-color: #f8f9fa;
        font-family: 'Rubik', sans-serif;
    }

    .login-container {
        max-width: 400px;
        margin: auto;
        padding: 20px;
        background-color: white;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .login-container h2 {
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
    session_start();
    include 'function/con.php';
    $Eror_Msg = "";

    if (isset($_POST['log-in'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (!empty($username) && !empty($password)) {
            $query = "SELECT `seller_password`, `seller_id` FROM `seller_tbl` WHERE `seller_username` = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $hashedPassword = $row['seller_password'];
                $seller_id = $row['seller_id'];

                if (password_verify($password, $hashedPassword)) {
                    $_SESSION['seller_username'] = $username;
                    $_SESSION['seller_id'] = $seller_id;
                    header("location: seller_dashboard.php");
                    exit();
                } else {
                    $Eror_Msg = "<div class='alert alert-danger'>Wrong Username and Password!</div>";
                }
            } else {
                $Eror_Msg = "<div class='alert alert-warning'>No User Found!</div>";
            }
        } else {
            $Eror_Msg = "<div class='alert alert-info'>Enter Username and Password!</div>";
        }
    }
    ?>

    <div class="container mt-5">
        <div class="login-container">
            <h2>Seller Login</h2>
            <?php echo $Eror_Msg; ?>
            <form method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input id="username" name="username" type="text" placeholder="Enter Username" class="form-control"
                        required />
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" name="password" type="password" placeholder="Enter Password"
                        class="form-control" required />
                </div>
                <div class="d-flex justify-content-between">
                    <button id="Login" name="log-in" class="btn btn-success btn-custom">Login</button>
                    <button id="Signup" name="Signup" class="btn btn-info btn-custom">
                        <a href="seller_signup.php">I don't have an account</a>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>