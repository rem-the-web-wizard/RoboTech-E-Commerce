<?php
session_start();
include("function/con.php");

if (!isset($_SESSION['customers_id']) || !isset($_SESSION['bn_total_bill'])) {
    header("location: customer_login.php");
    exit;
}

$User_ID = $_SESSION['customers_id'];
$Buy_Now_Total_Bill = $_SESSION['bn_total_bill'];
$Buy_Now_Delivery_Address = $_SESSION['bn_delivery_address'];
$cart_items = $_SESSION['cart_items'];

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
                      VALUES ('$generatedOrderID','$product_id','$User_ID','$Buy_Now_Delivery_Address', '{$item['product_price']}','Paypal','$newDate')";
    mysqli_query($con, $buy_now_query);
}

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
  <strong>Payment successful!</strong>
</div>
</div>
";
header("location: home.php");
exit;
?>