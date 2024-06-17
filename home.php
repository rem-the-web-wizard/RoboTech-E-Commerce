<?php

session_start();

include("function/con.php");

if (!isset($_SESSION['customers_id'])) {
    header("location: customer_login.php");
} else {
    $User_ID =  $_SESSION['customers_id'];
    $User_Name =  $_SESSION['username'];
}

// Fetch top 5 products for each type
$types = ['Board', 'Module', 'Sensor'];
$topProducts = [];

foreach ($types as $type) {
    $sql = "SELECT product_id, product_name, product_description, product_img, product_price, product_type, product_sale
            FROM products_tbl
            WHERE product_type = '$type'
            ORDER BY product_sale DESC
            LIMIT 5";
    $result = mysqli_query($con, $sql);
    
    if (!$result) {
        die("Invalid Query: " . mysqli_error($con));
    }
    
    $topProducts[$type] = mysqli_fetch_all($result, MYSQLI_ASSOC);
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
    <link rel="stylesheet" href="css/fa4/css/font-awesome.css">
    <link rel="stylesheet" href="css/bootstrap.css">
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

    .hero-section {
        background: url('img/hero-bg.jpg') no-repeat center center;
        background-size: cover;
        color: white;
        padding: 100px 0;
        text-align: center;
    }

    .hero-section h1 {
        font-size: 3rem;
        margin-bottom: 20px;
    }

    .hero-section p {
        font-size: 1.5rem;
        margin-bottom: 40px;
    }

    .hero-section .btn {
        font-size: 1.2rem;
        padding: 10px 20px;
    }

    .carousel-item img {
        max-height: 400px;
        object-fit: cover;
        width: 100%;
    }

    .product-card {
        border: none;
    }

    .product-card img {
        max-height: 300px;
        object-fit: cover;
        width: 100%;
    }

    .product-card .card-body {
        text-align: center;
    }
    </style>
    <title>Home | Tech Arena</title>
</head>

<body>
    <?php require 'navbar.php'; ?>

    <div class="hero-section">
        <h1>Welcome to Tech Arena</h1>
        <p>Your one-stop shop for the latest in tech and electronics</p>
        <a href="products.php" class="btn btn-primary">Shop Now</a>
    </div>

    <div class="container-fluid p-5">

        <br><br><br>
        <?php foreach ($topProducts as $type => $products) : ?>
        <div class="row">
            <div class="row">
                <center>
                    <h1>Top <?php echo $type; ?>s</h1>
                </center>
            </div>
            <div class="row">
                <div id="carouselId_<?php echo $type; ?>" class="carousel slide" data-bs-ride="carousel">
                    <ol class="carousel-indicators">
                        <?php foreach ($products as $index => $product) : ?>
                        <li data-bs-target="#carouselId_<?php echo $type; ?>" data-bs-slide-to="<?php echo $index; ?>"
                            class="<?php echo $index === 0 ? 'active' : ''; ?>"></li>
                        <?php endforeach; ?>
                    </ol>
                    <div class="carousel-inner" role="listbox">
                        <?php foreach ($products as $index => $product) : ?>
                        <?php
                            $productImg = "img/{$product['product_type']}/{$product['product_img']} 1";
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
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <div class="card product-card">
                                <div class="card-body p-5">
                                    <h4 class="card-title"><?php echo $product['product_name']; ?></h4>

                                    <center>
                                        <img src="<?php echo $productImg; ?>" class="w-25 d-block img-fluid"
                                            alt="Product Image" />
                                    </center>
                                    <h5 class="mt-3">₱<?php echo $product['product_price']; ?></h5>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button"
                        data-bs-target="#carouselId_<?php echo $type; ?>" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button"
                        data-bs-target="#carouselId_<?php echo $type; ?>" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

    </div>

    <?php require 'footer.php'; ?>

</body>

</html>