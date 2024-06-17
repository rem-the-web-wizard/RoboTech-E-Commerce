<?php
session_start();
include("function/con.php");

// Check if there's an alert message in the session
$alertMessage = isset($_SESSION['alertMessage']) ? $_SESSION['alertMessage'] : "";
if ($alertMessage) {
    unset($_SESSION['alertMessage']);
}

$User_ID = isset($_SESSION['customers_id']) ? $_SESSION['customers_id'] : null;
$User_Name = isset($_SESSION['username']) ? $_SESSION['username'] : null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add-to-cart'])) {
        $Cart_Product_ID = $_POST['cart_product_id'];
        $Cart_Customer_ID = $_POST['cart_customer_id'];
        $query = "INSERT INTO `cart_tbl`(`product_id`, `customer_id`, `status`) VALUES (?, ?, 'On the cart')";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ii", $Cart_Product_ID, $Cart_Customer_ID);
        if ($stmt->execute()) {
            $_SESSION['alertMessage'] = "
            <div id='alertContainer' class='fixed-top mt-5'>
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    <strong>Item Added to the Cart!</strong> Click the shopping cart icon to checkout.
                </div>
            </div>";
            header("location: board.php");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    if (isset($_POST['buy-now'])) {

        $Buy_Now_Product_ID = $_POST['bn_product_id'];
        $Buy_Now_Total_Bill = $_POST['bn_total_bill'];
        $Buy_Now_Customer_ID = $_POST['bn_customer_id'];
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
        
            $buy_now_query = "INSERT INTO `order_tbl`(`order_id`, `product_id`, `customer_id`, `delivery_address`, `total_bill`, `payment_method`, `delivery_day`) 
            VALUES ('$generatedOrderID','$Buy_Now_Product_ID','$Buy_Now_Customer_ID','$Buy_Now_Delivery_Address', '$Buy_Now_Total_Bill','$Buy_Now_Payment_Method','$newDate')";
        
        
        if (mysqli_query($con, $buy_now_query)) {
            //echo "Item add to cart successfully!";
           
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
            header("location: board.php");
            exit;
        } else {
            $errorMsg = "Error registering item: " . mysqli_error($con);
            echo $errorMsg;
        }
        } elseif ($Buy_Now_Payment_Method == "G-Cash") {
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
              <strong>This feature is not available yet!</strong>
            </div>
          </div>
            ";
            header("location: board.php");
            exit;
        } elseif ($Buy_Now_Payment_Method == "Paypal") {
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
              <strong>This feature is not available yet!</strong>
            </div>
          </div>
            ";
            header("location: board.php");
            exit;
        }
    
    }
}

// Fetch delivery address
$deliveryAddress = '';
if ($User_ID) {
    $fetchAddressQuery = "SELECT customer_address FROM customer_tbl WHERE customers_id = ?";
    $stmt = $con->prepare($fetchAddressQuery);
    $stmt->bind_param("i", $User_ID);
    $stmt->execute();
    $result2 = $stmt->get_result();
    if ($row = $result2->fetch_assoc()) {
        $deliveryAddress = $row['customer_address'];
    }
}

// Fetch products
$searchQuery = isset($_POST['search_txt']) ? $_POST['search_txt'] : '';
$sql = "SELECT product_id, product_name, product_description, product_img, product_price, product_type 
        FROM products_tbl
        WHERE product_type = 'Board' AND (product_name LIKE ? OR product_description LIKE ?) && product_status = 'Available' ";
$searchParam = "%$searchQuery%";
$stmt = $con->prepare($sql);
$stmt->bind_param("ss", $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Invalid Query: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
    * {
        user-select: none;
        -webkit-user-drag: none;
        margin: 0;
        padding: 0;
        font-family: 'Rubik', sans-serif;
    }

    .fade-out {
        opacity: 0;
        transition: opacity 1s ease-in-out;
    }

    #product-image {
        max-width: 100%;
    }

    .phone-container {
        padding: 30px;
        overflow: hidden;
    }

    picture {
        width: 200px;
    }

    #description_img {
        max-height: 80%;
    }

    .custom-card {
        border-radius: 10px;
        background-color: #f4f4f6;
        border: 2px white solid;
        box-shadow: -2px 2px 5px 0px rgba(0, 0, 0, 0.35);
        margin: 10px;
        width: 300px;
        height: 450px;
        overflow: hidden;
        transition: 0.4s;
    }

    .col {
        flex: 1 0 0%;
        display: flex;
        flex-wrap: nowrap;
        justify-content: space-evenly;
        align-content: center;
        align-items: center;
    }

    .disable-button {
        pointer-events: none;
        opacity: 0.5;
        display: none;
    }

    .buttons {
        display: none;
    }

    /* Show the buttons when the card is hovered over */
    .tab:hover .buttons {
        display: flex;
    }
    </style>
    <title>Boards | Tech Arena</title>
</head>

<body>
    <?php require 'navbar.php'; ?>

    <?php echo $alertMessage; ?>

    <br>

    <div class="container-fluid products-container p-5">
        <div class='row'>
            <div class='col text-center'>
                <h1 id='title2'>Boards</h1>
            </div>
        </div>
        <div class='row'>
            <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <?php
                    $productName = $row['product_name'];
                    $productDescription = $row['product_description'];
                    $productPrice = $row['product_price'];
                    $productId = $row['product_id'];
                    $productType = $row['product_type'];
                    $productImg = "img/$productType/" . $row['product_img'] . " 1";
                    $extensions = ['png', 'jpeg', 'jpg'];
                    foreach ($extensions as $extension) {
                        $imagePath = $productImg . '.' . $extension;
                        if (file_exists($imagePath)) {
                            $productImg = $imagePath;
                            break;
                        }
                    }
                    if (!file_exists($productImg)) {
                        $productImg = "img/default-image.png";
                    }
                    ?>
            <div class='col'>
                <div class='tab' id='tab'>
                    <div class='phone-container' data-bs-toggle='modal'
                        data-bs-target='#modalId_Description_<?php echo $productId; ?>'>
                        <picture>
                            <source srcset='<?php echo $productImg; ?>' type='image/svg+xml'>
                            <img src='<?php echo $productImg; ?>' class='img-fluid' alt='Product Image'
                                id='product-image'>
                        </picture>
                    </div>
                    <div class='product-info'>
                        <div class='logo-product-name'>
                            <h1 id='product-name'><?php echo htmlspecialchars($productName); ?></h1>
                            <p id='price'>₱<?php echo number_format($productPrice, 2, '.', ','); ?></p>
                        </div>
                    </div>
                    <div class='buttons'>
                        <form method='post' action=''>
                            <input type='hidden' name='cart_product_id' value='<?php echo $productId; ?>'>
                            <input type='hidden' name='cart_customer_id' value='<?php echo $User_ID; ?>'>
                            <input type='hidden' name='cart_total_bill' value='<?php echo $productPrice; ?>'>
                            <button type='submit' name='add-to-cart' id='add-to-cart'
                                class='<?php echo !$User_ID ? "disable-button" : ""; ?>'>Add to Cart</button>
                            <button type='button' id='buy-now'
                                class=' <?php echo !$User_ID ? "disable-button" : "btn btn-primary btn-lg"; ?>'
                                data-bs-toggle='modal' data-bs-target='#modalId<?php echo $productId; ?>'>
                                Buy Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal for Buy Now -->
            <div class='modal fade' id='modalId<?php echo $productId; ?>' tabindex='-1' data-bs-backdrop='static'
                data-bs-keyboard='false' role='dialog' aria-labelledby='modalTitleId' aria-hidden='true'>
                <div class='modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg' role='document'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='modal-title' id='modalTitleId'>Checkout</h5>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='modal-body'>
                            <form action='' method='post'>
                                <div class="row">
                                    <div class="col-md-6">
                                        <img src='<?php echo $productImg; ?>' class='img-fluid rounded'
                                            alt='Product Image' />
                                    </div>
                                    <div class="col-md-6">
                                        <h1 class="display-6"><?php echo htmlspecialchars($productName); ?></h1>
                                        <h4 class="text-success">
                                            ₱<?php echo number_format($productPrice, 2, '.', ','); ?></h4>
                                        <div class='mb-3'>
                                            <label class='form-label'>Delivery Address</label>
                                            <input type='hidden' name='bn_product_id' value='<?php echo $productId; ?>'>
                                            <input type='hidden' name='bn_customer_id' value='<?php echo $User_ID; ?>'>
                                            <input type='hidden' name='bn_total_bill'
                                                value='<?php echo $productPrice; ?>'>
                                            <input type='text' class='form-control' name='bn_delivery_address'
                                                value='<?php echo htmlspecialchars($deliveryAddress); ?>' required />
                                        </div>
                                        <div class='mb-3'>
                                            <label class='form-label'>Payment Method</label>
                                            <select class='form-select form-select-lg' name='bn_payment_method'
                                                required>
                                                <option selected>Select one</option>
                                                <option value='Cash on Delivery'>Cash on Delivery</option>
                                                <option value='G-Cash'>G-Cash</option>
                                                <option value='Paypal'>Paypal</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class='modal-footer'>
                                    <button type='button' class='btn btn-secondary'
                                        data-bs-dismiss='modal'>Close</button>
                                    <button type='submit' name='buy-now' class='btn btn-primary'>Checkout</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Modal for Description -->
            <div class='modal fade' id='modalId_Description_<?php echo $productId; ?>' tabindex='-1' role='dialog'
                aria-labelledby='modalTitleId' aria-hidden='true'>
                <div class='modal-dialog modal-dialog-centered modal-lg' role='document'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='modal-title' id='modalTitleId'><?php echo htmlspecialchars($productName); ?></h5>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='modal-body'>
                            <div class='container-fluid'>
                                <div class='row'>
                                    <center>
                                        <div class='col' id='description_img_container'>
                                            <img src='<?php echo $productImg; ?>' id='description_img'
                                                class='img-fluid rounded-top' alt='Product Image' />
                                        </div>
                                    </center>
                                </div>
                                <div class='row'>
                                    <p><strong><?php echo htmlspecialchars($productName); ?></strong></p>
                                    <p><?php echo nl2br(htmlspecialchars($productDescription)); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            <?php else: ?>
            <div class='col text-center'>
                <div class='card p-5'>
                    <div class='card-body'>
                        <div class='icon_wrapper'>
                            <i class='fa fa-fw fa-5x'>&#xf071</i>
                        </div>
                        <hr>
                        <br>
                        <h1 class='card-title'>No products found</h1>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php require 'footer.php'; ?>

    <script>
    // Handle fade-out alert
    setTimeout(function() {
        var alertContainer = document.getElementById("alertContainer");
        if (alertContainer) {
            alertContainer.classList.add("fade-out");
            setTimeout(function() {
                alertContainer.remove();
            }, 1000);
        }
    }, 10000); // 10 seconds

    // Handle cursor style on drag
    const tabsbox = document.querySelector(".tabs-box");
    let isDragging = false;

    const dragging = (e) => {
        if (!isDragging) return;
        tabsbox.scrollLeft -= e.movementX;
    }

    const dragStop = () => {
        isDragging = false;
        tabsbox.style.cursor = 'grab';
    }

    tabsbox.addEventListener("mousedown", () => {
        isDragging = true;
        tabsbox.style.cursor = 'grabbing';
    });
    tabsbox.addEventListener("mousemove", dragging);
    document.addEventListener("mouseup", dragStop);
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>
</body>

</html>