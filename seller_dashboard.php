<?php
session_start();
include("function/con.php");

if (!isset($_SESSION['seller_id'])) {
    header("location: seller_login.php");
    exit();
} else {
    $Seller_ID = $_SESSION['seller_id'];
}

// Fetch total items
$query_total_items = "SELECT COUNT(*) as total_items FROM products_tbl WHERE owner = ?";
$stmt_total_items = $con->prepare($query_total_items);
$stmt_total_items->bind_param("s", $Seller_ID);
$stmt_total_items->execute();
$result_total_items = $stmt_total_items->get_result();
$total_items = $result_total_items->fetch_assoc()['total_items'];

// Fetch total sales
$query_total_sales = "SELECT SUM(product_sale) as total_sales FROM products_tbl WHERE owner = ?";
$stmt_total_sales = $con->prepare($query_total_sales);
$stmt_total_sales->bind_param("s", $Seller_ID);
$stmt_total_sales->execute();
$result_total_sales = $stmt_total_sales->get_result();
$total_sales = $result_total_sales->fetch_assoc()['total_sales'];

// Fetch total revenue
$query_total_revenue = "SELECT SUM(total_bill) as total_revenue FROM order_tbl WHERE product_id IN (SELECT product_id FROM products_tbl WHERE owner = ?) AND order_status = 'Delivered'";
$stmt_total_revenue = $con->prepare($query_total_revenue);
$stmt_total_revenue->bind_param("s", $Seller_ID);
$stmt_total_revenue->execute();
$result_total_revenue = $stmt_total_revenue->get_result();
$total_revenue = $result_total_revenue->fetch_assoc()['total_revenue'];

// Fetch pending orders
$query_pending_orders = "SELECT COUNT(*) as pending_orders FROM order_tbl WHERE order_status = 'Processing' AND product_id IN (SELECT product_id FROM products_tbl WHERE owner = ?)";
$stmt_pending_orders = $con->prepare($query_pending_orders);
$stmt_pending_orders->bind_param("s", $Seller_ID);
$stmt_pending_orders->execute();
$result_pending_orders = $stmt_pending_orders->get_result();
$pending_orders = $result_pending_orders->fetch_assoc()['pending_orders'];

// Fetch recent orders
$query_recent_orders = "SELECT o.order_id, o.order_status, o.total_bill, p.product_name FROM order_tbl AS o INNER JOIN products_tbl AS p ON o.product_id = p.product_id WHERE p.owner = ? ORDER BY o.order_id DESC LIMIT 5";
$stmt_recent_orders = $con->prepare($query_recent_orders);
$stmt_recent_orders->bind_param("s", $Seller_ID);
$stmt_recent_orders->execute();
$result_recent_orders = $stmt_recent_orders->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Seller Dashboard</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/phones.css">
    <link rel="stylesheet" href="css/products.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/fa4/css/font-awesome.css">
    <link rel="stylesheet" href="css/bootstrap.css">

    <style>
    body {
        background-color: #f8f9fa;
        font-family: 'Rubik', sans-serif;
    }

    .dashboard-container {
        margin-top: 50px;
    }

    .card {
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        font-weight: bold;
    }

    .recent-orders {
        margin-top: 30px;
    }

    .recent-orders table {
        border-radius: 10px;
        overflow: hidden;
    }

    .table thead {
        background-color: #343a40;
        color: white;
    }

    .table tbody tr {
        background-color: white;
        transition: background-color 0.3s;
    }

    .table tbody tr:hover {
        background-color: #f1f1f1;
    }

    .table td,
    .table th {
        padding: 15px;
        text-align: center;
    }

    .table img {
        max-width: 50px;
        border-radius: 5px;
    }

    .text-center {
        text-align: center;
    }
    </style>
</head>

<body>
    <?php include 'navbar_seller.php'; ?>
    <div class="container dashboard-container">
        <h1 class="text-center">Seller Dashboard</h1>
        <hr>
        <div class="row">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Total Items</div>
                    <div class="card-body">
                        <h5 class="card-title text-center"><?php echo !empty($total_items) ? $total_items : '0'; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Total Sales</div>
                    <div class="card-body">
                        <h5 class="card-title text-center"><?php echo !empty($total_sales) ? $total_sales : '0'; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">Total Revenue</div>
                    <div class="card-body">
                        <h5 class="card-title text-center">₱<?php echo number_format($total_revenue, 2, '.', ','); ?>
                        </h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">Pending Orders</div>
                    <div class="card-body">
                        <h5 class="card-title text-center"><?php echo $pending_orders; ?></h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="recent-orders">
            <h3 class="text-center">Recent Orders</h3>
            <table class="table table-hover table-dark table-striped m-auto">
                <thead>
                    <tr>
                        <th scope="col">Order ID</th>
                        <th scope="col">Product</th>
                        <th scope="col">Total Bill</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_recent_orders->num_rows > 0) {
                        while ($row = $result_recent_orders->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['order_id']}</td>
                                    <td>{$row['product_name']}</td>
                                    <td>₱" . number_format($row['total_bill'], 2, '.', ',') . "</td>
                                    <td>{$row['order_status']}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center'>No recent orders</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

        </div>
    </div>
    <br>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

</body>

</html>