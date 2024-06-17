<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
    .container {
        max-width: 600px;
        margin-top: 50px;
        text-align: center;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        margin-top: 20px;
    }

    .alert {
        margin-top: 20px;
    }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h2>Order Confirmation</h2>
        <div class="alert alert-success">
            <strong>Your order has been processed successfully!</strong> Please prepare the exact amount on the delivery
            day.
        </div>
        <a href="home.php" class="btn btn-primary">Continue Shopping</a>
    </div>
</body>

</html>