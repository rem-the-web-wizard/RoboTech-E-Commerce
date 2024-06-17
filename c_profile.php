<?php
session_start();
include("function/con.php");

$User_ID = $_SESSION['customers_id'];

$sql = "SELECT * FROM customer_tbl WHERE customers_id = '$User_ID'";
$result = mysqli_query($con, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $User_Full_Name = $row['customer_first_name'] . " " . $row['customer_last_name'];
    $Customer_Email = $row['customer_email'];
    $Customer_Contact = "0" . $row['customer_contact'];
    $Customer_Address = $row['customer_address'];
}

// Define a function to fetch orders based on status
function fetchOrders($con, $User_ID, $status) {
    $sql = "SELECT c.*, p.product_name, p.product_img, p.product_price, p.product_type
            FROM order_tbl c
            JOIN products_tbl p ON c.product_id = p.product_id
            WHERE c.customer_id = ? AND c.order_status = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("is", $User_ID, $status);
    $stmt->execute();
    return $stmt->get_result();
}

// Fetch orders based on status
$result_to_pay = fetchOrders($con, $User_ID, 'To Pay');
$Total_to_pay = mysqli_num_rows($result_to_pay);

$result_processing = fetchOrders($con, $User_ID, 'Processing');
$Total_processing = mysqli_num_rows($result_processing);

$result_to_ship = fetchOrders($con, $User_ID, 'For Pick Up');
$Total_to_ship = mysqli_num_rows($result_to_ship);

$result_to_receive = fetchOrders($con, $User_ID, 'For Delivery');
$Total_to_receive = mysqli_num_rows($result_to_receive);

$result_to_review = fetchOrders($con, $User_ID, 'Delivered');
$Total_to_review = mysqli_num_rows($result_to_review);

// Fetch all completed orders for purchase history
$result_purchase_history = fetchOrders($con, $User_ID, 'Delivered');
$Total_purchase_history = mysqli_num_rows($result_purchase_history);

?>

<!doctype html>
<html lang="en">

<head>
    <title>Customer Profile</title>
    <!-- Required meta tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/phones.css">
    <link rel="stylesheet" href="css/products.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/fa4/css/font-awesome.css">
    <link rel="stylesheet" href="css/fa4/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <style>
    .modal-header {
        background-color: #007bff;
        color: white;
    }

    .modal-header h5 {
        margin: 0;
    }


    .modal-content {
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f9f9f9;
    }

    .table-striped tbody tr:hover {
        background-color: #f1f1f1;
    }

    .btn-close {
        background: transparent;
        border: none;
    }

    .btn-secondary,
    .btn-primary {
        border-radius: 20px;
    }


    .container-fluid {
        margin-top: 20px;
    }

    .profile-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .profile-header h1 {
        font-size: 2.5em;
    }

    .order-button {
        text-align: center;
        margin-bottom: 30px;
    }

    .modal-body {
        padding: 30px;
    }

    .table {
        margin-bottom: 0;
    }

    .profile-info {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 30px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Specific styles for modal header */
    .modal-header {
        background-color: #007bff;
        color: white;
    }

    .modal-header h5 {
        margin: 0;
    }

    /* Add this to ensure the navbar is always on top */
    .navbar {
        z-index: 1000;
    }

    /* To avoid any conflict, specify the container's padding */
    .container-fluid {
        padding: 0 15px;
    }

    .ads-section img {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 10px 0;
    }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container-fluid p-5">
        <div class="profile-header">
            <h1><?php echo $User_Full_Name; ?></h1>
        </div>
        <div class="profile-info">
            <h4>Profile Information</h4>
            <p><strong>Email:</strong> <?php echo $Customer_Email; ?></p>
            <p><strong>Contact:</strong> <?php echo $Customer_Contact; ?></p>
            <p><strong>Address:</strong> <?php echo $Customer_Address; ?></p>
        </div>
        <div class="order-button">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ordersModal">
                View Orders
            </button>
            <button type="button" class="btn btn-secondary" data-bs-toggle="modal"
                data-bs-target="#purchaseHistoryModal">
                View Purchase History
            </button>
        </div>
        <div class="ads-section">
            <h4>Advertisements</h4>
            <img src="img/ad1.jpg" alt="Ad 1">
            <img src="img/ad2.jpg" alt="Ad 2" style="margin-top: 20px;">
        </div>
    </div>

    <!-- Orders Modal -->
    <div class="modal fade" id="ordersModal" tabindex="-1" aria-labelledby="ordersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ordersModalLabel">Orders</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="ordersTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="to-pay-tab" data-bs-toggle="tab"
                                data-bs-target="#to-pay" type="button" role="tab" aria-controls="to-pay"
                                aria-selected="true">To Pay (<?php echo $Total_to_pay; ?>)</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="to-ship-tab" data-bs-toggle="tab" data-bs-target="#to-ship"
                                type="button" role="tab" aria-controls="to-ship" aria-selected="false">To Ship
                                (<?php echo $Total_to_ship; ?>)</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="to-receive-tab" data-bs-toggle="tab"
                                data-bs-target="#to-receive" type="button" role="tab" aria-controls="to-receive"
                                aria-selected="false">To Receive (<?php echo $Total_to_receive; ?>)</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="to-review-tab" data-bs-toggle="tab" data-bs-target="#to-review"
                                type="button" role="tab" aria-controls="to-review" aria-selected="false">To Review
                                (<?php echo $Total_to_review; ?>)</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="to-return-tab" data-bs-toggle="tab" data-bs-target="#to-return"
                                type="button" role="tab" aria-controls="to-return" aria-selected="false">To Return
                                (<?php echo $Total_to_return; ?>)</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="processing-tab" data-bs-toggle="tab"
                                data-bs-target="#processing" type="button" role="tab" aria-controls="processing"
                                aria-selected="false">All Orders (<?php echo $Total_processing; ?>)</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="ordersTabContent">
                        <?php
                            $statuses = [
                                'to-pay' => $result_to_pay,
                                'processing' => $result_processing,
                                'to-ship' => $result_to_ship,
                                'to-receive' => $result_to_receive,
                                'to-review' => $result_to_review,
                                'to-return' => $result_to_return
                            ];
                            foreach ($statuses as $status => $result) {
                                echo "
                                <div class='tab-pane fade ".($status === 'to-pay' ? 'show active' : '')."' id='$status' role='tabpanel' aria-labelledby='$status-tab'>
                                    <div class='table-responsive'>
                                        <table class='table table-striped'>
                                            <thead>
                                                <tr>
                                                    <th scope='col'>Image</th>
                                                    <th scope='col'>Product</th>";
                                echo $status === 'processing' ? "<th scope='col'>Status</th>" : "<th scope='col'>Price</th>";
                                echo "              </tr>
                                            </thead>
                                            <tbody>";
                                if ($result->num_rows > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $productImg = "img/{$row['product_type']}/{$row['product_img']} 1.png";
                                        echo "
                                        <tr>
                                            <td><img src='$productImg' class='img-fluid rounded' alt='Product Image' style='width: 50px; height: auto;'></td>
                                            <td>{$row['product_name']}</td>";
                                        echo $status === 'processing' ? "<td>{$row['order_status']}</td>" : "<td>₱".number_format($row['product_price'], 2, '.', ',')."</td>";
                                        echo "  </tr>";
                                    }
                                } else {
                                    echo "
                                    <tr>
                                        <td colspan='3'>No items in this list.</td>
                                    </tr>";
                                }
                                echo "
                                            </tbody>
                                        </table>
                                    </div>
                                </div>";
                            }
                        ?>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase History Modal -->
    <div class="modal fade" id="purchaseHistoryModal" tabindex="-1" aria-labelledby="purchaseHistoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="purchaseHistoryModalLabel">Purchase History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class='table table-striped'>
                            <thead>
                                <tr>
                                    <th scope='col'>Image</th>
                                    <th scope='col'>Product</th>
                                    <th scope='col'>Price</th>
                                    <th scope='col'>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result_purchase_history->num_rows > 0) {
                                    while ($row = mysqli_fetch_assoc($result_purchase_history)) {
                                        $productImg = "img/{$row['product_type']}/{$row['product_img']} 1.png";
                                        echo "
                                        <tr>
                                            <td><img src='$productImg' class='img-fluid rounded' alt='Product Image' style='width: 50px; height: auto;'></td>
                                            <td>{$row['product_name']}</td>
                                            <td>₱".number_format($row['product_price'], 2, '.', ',')."</td>
                                            <td>{$row['delivery_day']}</td>
                                        </tr>";
                                    }
                                } else {
                                    echo "
                                    <tr>
                                        <td colspan='4'>No purchase history available.</td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>