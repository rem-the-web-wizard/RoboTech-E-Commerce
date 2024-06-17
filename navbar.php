<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'function/con.php';

$User_ID = isset($_SESSION['customers_id']) ? $_SESSION['customers_id'] : null;
$User_Name = isset($_SESSION['username']) ? $_SESSION['username'] : null;

if ($User_ID) {
    // Prepare the statement
    $stmt = $con->prepare("SELECT c.*, p.product_name, p.product_img, p.product_price, p.product_type
                           FROM cart_tbl c
                           JOIN products_tbl p ON c.product_id = p.product_id
                           WHERE c.customer_id = ?");
    $stmt->bind_param("i", $User_ID); // "i" indicates that the parameter is an integer
    $stmt->execute();
    $result1 = $stmt->get_result();

    if (!$result1) {
        die("Invalid Query: " . mysqli_error($con));
    }

    if (isset($_POST['delete-item'])) {
        $productId = $_POST['product_id'];
        $cartid = $_POST['cart_id'];

        $deleteQuery = "DELETE FROM `cart_tbl` WHERE product_id = ? AND ID = ?";
        $stmt = $con->prepare($deleteQuery);
        $stmt->bind_param("ii", $productId, $cartid);
        if ($stmt->execute()) {
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
                          <strong>Item deleted from cart successfully!</strong>
                        </div>
                      </div>
                        ";
                        header("location: board.php");
                        exit;
        } else {
            $_SESSION['alertMessage'] = 
                        "
                        <div id='alertContainer' class='fixed-top mt-5'>
                        <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                          <button
                            type='button'
                            class='btn-close'
                            data-bs-dismiss='alert'
                            aria-label='Close'
                          ></button>
                          <strong>Error registering item: " . mysqli_error($con) . "!</strong>
                        </div>
                      </div>
                        ";
                        header("location: board.php");
                        exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
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
    body,
    html {
        height: 100%;
        margin: 0;
        display: flex;
        flex-direction: column;
    }

    .content {
        flex: 1 0 auto;
    }

    .navbar-custom {
        background-color: #343a40;
    }

    .navbar-custom .navbar-brand,
    .navbar-custom .nav-link {
        color: white;
    }

    .navbar-custom .navbar-brand:hover,
    .navbar-custom .nav-link:hover {
        color: #007bff;
    }

    .navbar-custom .dropdown-menu {
        background-color: #343a40;
    }

    .navbar-custom .dropdown-item {
        color: white;
    }

    .navbar-custom .dropdown-item:hover {
        background-color: #007bff;
    }

    .navbar-toggler-icon {
        color: white;
    }

    .icon_wrapper {
        display: inline-block;
        margin-right: 10px;
        vertical-align: middle;
    }

    .modal-content img {
        max-width: 100%;
        height: auto;
    }

    .footer {
        background-color: #343a40;
        color: white;
        padding: 40px 0;
        text-align: center;
    }

    .footer .footer-links a {
        color: white;
        margin: 0 15px;
        text-decoration: none;
    }

    .footer .footer-links a:hover {
        text-decoration: underline;
    }

    .footer .social-icons a {
        color: white;
        margin: 0 10px;
        font-size: 20px;
        transition: color 0.3s;
    }

    .footer .social-icons a:hover {
        color: #007bff;
    }

    .dropdown-menu-end {
        right: 0;
        left: auto;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="about_us.php">RoboTech</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"><i class="fa fa-bars"></i></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="board.php">Boards</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="module.php">Modules</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sensor.php">Sensors</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if ($User_ID): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#bagModal">
                            <i class="fa fa-shopping-cart fa-2x"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-user fa-2x"></i> <?php echo $User_Name; ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="c_profile.php">Profile</a></li>
                            <li><a class="dropdown-item" href="c_logout.php">Logout</a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="customer_login.php">Login</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <form class="d-flex" method="post" action="">
                            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search"
                                name="search_txt">
                            <button class="btn btn-outline-success" type="submit" name="search_btn">Search</button>
                        </form>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownSort" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-sort"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownSort">
                            <li>
                                <form action="" method="post">
                                    <button id="sort_btn" name="price-low-to-high" type="submit" class="dropdown-item">
                                        Low Price To High Price
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form action="" method="post">
                                    <button id="sort_btn" type="submit" name="price-high-to-low" class="dropdown-item">
                                        High Price To Low Price
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form action="" method="post">
                                    <button id="sort_btn" type="submit" name="name-a-to-z" class="dropdown-item">
                                        A to Z
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form action="" method="post">
                                    <button id="sort_btn" type="submit" name="name-z-to-a" class="dropdown-item">
                                        Z to A
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="modal fade" id="bagModal" tabindex="-1" aria-labelledby="bagModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bagModalLabel">Your Bag</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">Product</th>
                                <th scope="col">Name</th>
                                <th scope="col">Price</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                        $Subtotal = 0;
                        if ($result1) {
                            while ($rows = mysqli_fetch_assoc($result1)) {
                                $CartId = $rows['ID'];
                                $productName = $rows['product_name'];
                                $productPrice = $rows['product_price'];
                                $productId = $rows['product_id'];
                                $productType = $rows['product_type'];
                                $productImg = "img/$productType/" . $rows['product_img'] . " 1.png";

                                // Update the total price
                                $Subtotal += $productPrice;

                                echo "
                                <tr>
                                    <td><img src='$productImg' class='img-fluid rounded' style='width: 50px; height: 50px;' alt='$productImg'/></td>
                                    <td>$productName</td>
                                    <td>₱" . number_format($productPrice, 2, '.', ',') . "</td>
                                    <td>
                                        <form action='' method='post'>
                                            <input type='hidden' name='product_id' value='$productId'>
                                            <input type='hidden' name='cart_id' value='$CartId'>
                                            <button type='submit' name='delete-item' class='btn btn-danger btn-sm'>Delete</button>
                                        </form>
                                    </td>    
                                </tr>";
                            }
                        } else {
                            echo "
                            <tr>
                                <td colspan='4' class='text-center'>Your cart is empty</td>
                            </tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <h4>Subtotal: <strong
                            style="color: red">₱<?php echo number_format($Subtotal, 2, '.', ','); ?></strong></h4>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="checkout.php" class="btn btn-primary">Checkout</a>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>
</body>

</html>