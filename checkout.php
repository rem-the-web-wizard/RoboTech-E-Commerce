<?php
session_start();
include("function/con.php");

if (!isset($_SESSION['customers_id'])) {
    header("location: customer_login.php");
    exit;
}

$User_ID = $_SESSION['customers_id'];
$User_Name = $_SESSION['username'];

$subtotal = 0;
$cart_items = [];

// Fetch items from cart
$stmt = $con->prepare("SELECT c.*, p.product_name, p.product_img, p.product_price, p.product_type
                       FROM cart_tbl c
                       JOIN products_tbl p ON c.product_id = p.product_id
                       WHERE c.customer_id = ?");
$stmt->bind_param("i", $User_ID);
$stmt->execute();
$result = $stmt->get_result();

while ($row = mysqli_fetch_assoc($result)) {
    $cart_items[] = $row;
    $subtotal += $row['product_price'];
}

if (isset($_POST['buy-now'])) {
    $Buy_Now_Total_Bill = $subtotal;
    $Buy_Now_Customer_ID = $User_ID;
    $Buy_Now_Delivery_Address = $_POST['bn_delivery_address'];
    $Buy_Now_Payment_Method = $_POST['bn_payment_method'];

    if ($Buy_Now_Payment_Method == "Cash on Delivery") {
        function generateOrderID($length = 7) {
            $Numbers = '0123456789';
            $generatedOrderID = '';
            $maxIndex = strlen($Numbers) - 1;
        
            for ($i = 0; $i < $length; $i++) {
                $generatedOrderID .= $Numbers[rand(0, $maxIndex)];
            }
        
            return $generatedOrderID;
        }
    
        function getDateAfterSevenDays() {
            date_default_timezone_set('Asia/Manila'); // Set the timezone to Philippine Time
        
            $currentDate = date('Y-m-d'); // Get the current date (today) in 'YYYY-MM-DD' format
        
            $newDate = date('Y-m-d', strtotime($currentDate . ' + 7 days')); // Add 7 days to the current date
        
            return $newDate;
        }
    
        $newDate = getDateAfterSevenDays();
        $generatedOrderID = generateOrderID();
          
        // Process each item in the cart
        foreach ($cart_items as $item) {
            $product_id = $item['product_id'];
            $buy_now_query = "INSERT INTO `order_tbl`(`order_id`, `product_id`, `customer_id`, `delivery_address`, `total_bill`, `payment_method`, `delivery_day`) 
                              VALUES ('$generatedOrderID','$product_id','$Buy_Now_Customer_ID','$Buy_Now_Delivery_Address', '{$item['product_price']}','$Buy_Now_Payment_Method','$newDate')";
            mysqli_query($con, $buy_now_query);
        }
          
        // Remove items from the cart
        $delete_cart_query = "DELETE FROM `cart_tbl` WHERE customer_id = '$User_ID'";
        mysqli_query($con, $delete_cart_query);
        
        $_SESSION['alertMessage'] = 
        "
        <div id='alertContainer' class='fixed-top mt-5'>
        <div class='alert alert-success alert-dismissible fade show' role='alert'>
          <button
            type='button'
            class='btn-close'
            data-bs-dismiss='alert'
            aria-label='Close'
          ></button>
          <strong>Please Prepare the exact amount!</strong>
        </div>
      </div>
        ";
        header("location: confirmation.php");
        exit;
    } elseif ($Buy_Now_Payment_Method == "G-Cash" || $Buy_Now_Payment_Method == "Paypal") {
        $_SESSION['alertMessage'] = 
        "
        <div id='alertContainer' class='fixed-top mt-5'>
        <div class='alert alert-danger alert-dismissible fade show' role='alert'>
          <button
            type='button'
            class='btn-close'
            data-bs-dismiss='alert'
            aria-label='Close'
          ></button>
          <strong>This feature is not available yet! Please Select other payment method</strong>
        </div>
      </div>
        ";
        header("location: checkout.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
    .container {
        max-width: 600px;
        margin-top: 50px;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .cart-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .cart-item img {
        width: 50px;
        height: auto;
    }

    .subtotal {
        font-weight: bold;
    }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <?php if (isset($_SESSION['alertMessage'])): ?>
    <?php echo $_SESSION['alertMessage']; unset($_SESSION['alertMessage']); ?>
    <?php endif; ?>
    <div class="container">
        <h2>Checkout</h2>
        <hr>
        <div class="cart-items">
            <?php foreach ($cart_items as $item): ?>
            <div class="cart-item">
                <div>
                    <img src="img/<?php echo $item['product_type'] . '/' . $item['product_img'] . ' 1.png'; ?>"
                        alt="Product Image">
                    <span><?php echo htmlspecialchars($item['product_name']); ?></span>
                </div>
                <span>₱<?php echo number_format($item['product_price'], 2, '.', ','); ?></span>
            </div>
            <?php endforeach; ?>
            <div class="subtotal">
                Subtotal: ₱<?php echo number_format($subtotal, 2, '.', ','); ?>
            </div>
        </div>
        <form action="checkout.php" method="POST">
            <div class="mb-3">
                <label for="deliveryAddress" class="form-label">Delivery Address</label>
                <input type="text" class="form-control" id="deliveryAddress" name="bn_delivery_address" required>
            </div>
            <div class="mb-3">
                <label for="paymentMethod" class="form-label">Payment Method</label>
                <select class="form-select" id="paymentMethod" name="bn_payment_method" required>
                    <option value="" selected disabled>Select one</option>
                    <option value="Cash on Delivery">Cash on Delivery</option>
                    <option value="G-Cash">G-Cash</option>
                    <option value="Paypal">Paypal</option>
                </select>
            </div>
            <button type="submit" name="buy-now" class="btn btn-primary">Checkout</button>
        </form>
    </div>
    <br><br><br>
    <?php include 'footer.php'; ?>
</body>

</html>