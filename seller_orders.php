<?php

session_start();
include("function/con.php");

if (!isset($_SESSION['seller_id'])) {
    header("location: seller_login.php");
    exit();
} else {
    $Seller_ID = $_SESSION['seller_id'];
}

$sql = "SELECT o.product_id, p.product_name, o.order_id, o.total_bill, o.order_status
        FROM order_tbl AS o
        INNER JOIN products_tbl AS p ON o.product_id = p.product_id
        WHERE p.owner = '$Seller_ID'";
$result = mysqli_query($con, $sql);

$TotalPending = 0;
$TotalConfirm = 0;
$TotalDelivered = 0;
$TotalReturn = 0;

$countProductsQuery = "SELECT 
    SUM(CASE WHEN o.order_status = 'Processing' THEN 1 ELSE 0 END) AS total_pending,
    SUM(CASE WHEN o.order_status = 'To Ship' THEN 1 ELSE 0 END) AS total_confirm,
    SUM(CASE WHEN o.order_status = 'Delivered' THEN 1 ELSE 0 END) AS total_delivered,
    SUM(CASE WHEN o.order_status = 'To Return' THEN 1 ELSE 0 END) AS total_return
FROM order_tbl AS o
INNER JOIN products_tbl AS p ON o.product_id = p.product_id
WHERE p.owner = '$Seller_ID'";

$result1 = mysqli_query($con, $countProductsQuery);

if ($result1) {
    $row = mysqli_fetch_assoc($result1);
    $TotalPending = $row['total_pending'];
    $TotalConfirm = $row['total_confirm'];
    $TotalDelivered = $row['total_delivered'];
    $TotalReturn = $row['total_return'];
} else {
    echo "Error executing query: " . mysqli_error($con);
}

if (!$result) {
    die("Invalid Query: " . mysqli_error($con));
}

if (isset($_POST['update_status'])) {
    $orderId = $_POST['orderId'];
    $productId = $_POST['productId'];
    $newStatus = $_POST['new_status'];

    $query = "UPDATE `order_tbl` SET `order_status` = ? WHERE `order_id` = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("si", $newStatus, $orderId);
    
    if ($stmt->execute()) {
        if ($newStatus == 'To Ship' || $newStatus == 'Delivered') {
            $query2 = "UPDATE `products_tbl` SET `product_sale` = `product_sale` + 1 WHERE `product_id` = ?";
            $stmt2 = $con->prepare($query2);
            $stmt2->bind_param("i", $productId);
            $stmt2->execute();
        }
        $_SESSION['alertMessage'] = "
        <div id='alertContainer' class='fixed-top mt-5'>
            <div class='alert alert-success alert-dismissible fade show' role='alert'>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                <strong>Updated successfully!</strong> Order status updated.
            </div>
        </div>";
        header("location: seller_orders.php");
        exit();
    } else {
        $_SESSION['alertMessage'] = "
        <div id='alertContainer' class='fixed-top mt-5'>
            <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                <strong>Error updating order: " . mysqli_error($con) . "</strong>
            </div>
        </div>";
    }
}

if (isset($_SESSION['alertMessage'])) {
    $alertMessage = $_SESSION['alertMessage'];
    unset($_SESSION['alertMessage']);
} else {
    $alertMessage = "";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/phones.css">
    <link rel="stylesheet" href="css/products.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/bootstrap.css" />
    <style>
    * {
        user-select: none;
        -webkit-user-drag: none;
        margin: 0;
        padding: 0;
        font-family: 'Rubik', sans-serif;
    }
    </style>
    <title>Seller Orders | Tech Arena</title>
</head>

<body>
    <?php require 'navbar_seller.php';
    echo $alertMessage;
    ?>

    <div class="container-fluid p-5">
        <br><br>
        <div class="card-group g-5">
            <div class="card">
                <div class="card-body">
                    <center><i class="fa fa-fw fa-4x">&#xf00b</i>
                        <h5 class="card-title">Pending Orders</h5>
                        <p class="card-text"><?php echo $TotalPending; ?></p>
                    </center>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <center><i class="fa fa-fw fa-4x">&#xf046</i>
                        <h4 class="card-title">Confirm Orders</h4>
                        <p class="card-text"><?php echo $TotalConfirm; ?></p>
                    </center>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <center><i class="fa fa-fw fa-4x">&#xf0d1</i>
                        <h4 class="card-title">Delivered Orders</h4>
                        <p class="card-text"><?php echo $TotalDelivered ?></p>
                    </center>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <center><i class="fa fa-fw fa-4x">&#xf112</i>
                        <h4 class="card-title">Return Orders</h4>
                        <p class="card-text"><?php echo $TotalReturn ?></p>
                    </center>
                </div>
            </div>
        </div>
        <hr>
        <br><br>

        <div class="table-responsive-xl">
            <table class="table table-hover table-light table-striped m-auto">
                <thead>
                    <tr>
                        <th scope="col">Product Name</th>
                        <th scope="col">Order ID</th>
                        <th scope="col">Total Price</th>
                        <th scope="col">Order Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        $productName = $row['product_name'];
                        $orderId = $row['order_id'];
                        $totalPrice = $row['total_bill'];
                        $order_status = $row['order_status'];
                        $productId = $row['product_id'];
                        ?>
                    <tr>
                        <td><?php echo $productName; ?></td>
                        <td><?php echo $orderId; ?></td>
                        <td><?php echo "₱ " . number_format($totalPrice, 2, '.', ','); ?></td>
                        <td><?php echo $order_status; ?></td>
                        <td>
                            <?php if ($order_status == "Processing") { ?>
                            <form action="" method="post">
                                <input type="hidden" name="orderId" value="<?php echo $orderId; ?>">
                                <input type="hidden" name="productId" value="<?php echo $productId; ?>">
                                <input type="hidden" name="new_status" value="To Ship">
                                <button type="submit" name="update_status" class="btn btn-success">Accept</button>
                            </form>
                            <?php } elseif ($order_status == "To Ship") { ?>
                            <form action="" method="post">
                                <input type="hidden" name="orderId" value="<?php echo $orderId; ?>">
                                <input type="hidden" name="productId" value="<?php echo $productId; ?>">
                                <input type="hidden" name="new_status" value="For Delivery">
                                <button type="submit" name="update_status" class="btn btn-warning">To Pick Up</button>
                            </form>
                            <?php } elseif ($order_status == "For Delivery") { ?>
                            <form action="" method="post">
                                <input type="hidden" name="orderId" value="<?php echo $orderId; ?>">
                                <input type="hidden" name="productId" value="<?php echo $productId; ?>">
                                <input type="hidden" name="new_status" value="Delivered">
                                <button type="submit" name="update_status" class="btn btn-primary">Delivered</button>
                            </form>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>



    <script>
    const tabsbox = document.querySelector(".tabs-box");
    const tab = document.getElementById('tab');

    let isDragging = false;

    const dragging = (e) => {
        if (!isDragging) return;
        console.log("Dragging...");
        tabsbox.scrollLeft -= e.movementX;
    }

    const dragStop = () => {
        tab.classList.remove('grabbing');
        isDragging = false;
    }

    tabsbox.addEventListener("mousedown", () => isDragging = true);
    tabsbox.addEventListener("mousemove", dragging);
    document.addEventListener("mouseup", dragStop);

    tabsbox.addEventListener('mousedown', function(event) {
        tabsbox.style.cursor = 'grabbing';
    })
    tabsbox.addEventListener('mouseup', function(event) {
        tabsbox.style.cursor = 'grab';
    })

    var amount = parseFloat(document.getElementById("price").textContent);
    var formattedAmount = amount.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    document.getElementById("price").textContent = formattedAmount;
    </script>
</body>

</html>