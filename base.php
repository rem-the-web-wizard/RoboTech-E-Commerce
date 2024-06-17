<?php

session_start();

include("function/con.php");

$sql = "SELECT product_id, product_name, product_description, product_img, product_price, product_type FROM products_tbl";
$result = mysqli_query($con, $sql);

if(!$result){
   die("Invalid Query: ".mysqli_error($con));
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
    <link rel="stylesheet" href="css/products.css">
    <link rel="stylesheet" href="css/footer.css">
    <style>
    * {
        user-select: none;
        -webkit-user-drag: none;
        margin: 0;
        padding: 0;
        font-family: 'Rubik', sans-serif;
    }
    </style>
    <title>Boards | Tech Arena</title>
</head>

<body>
    <?php require 'navbar.php'; ?>
    <?php require 'search.php';?>
    <h1 id="title2">Sensor</h1>
    <div class="products-container">
        <?php
      
      while ($row = mysqli_fetch_assoc($result)) {
        $productName = $row['product_name'];
        $productDescription = $row['product_description'];
        $productPrice = $row['product_price'];
        $productId = $row['product_id'];
        $productType = $row['product_type'];
        $productImg = "img/$productType/" . $row['product_img'] . " 1.png";
        
        echo "
        <div class='tab' id='tab'>
            <a href='test.php?productId= $productId'>
        <div class='phone-container'>
            <img src='$productImg' alt='Product Image' id='product-image'>
        </div>
        <div class='product-info'>
            <div class='logo-product-name'>
                <h1 id='product-name'>$productName</h1>
                <p id='price'>₱" . number_format($productPrice, 2, '.', ',') . "</p>
            </div>
        </div>
        <div class='buttons'>
            <button id='add-to-cart'>Add to Cart</button>
            <button id='buy-now'>Buy Now</button>
        </div>
        </a>
    </div>
    ";
    }


    ?>
    </div>


    <?php require 'footer.php';?>

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


    // comma and decimal to product price
    var amount = parseFloat(document.getElementById("price").textContent);
    var formattedAmount = amount.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    document.getElementById("price").textContent = formattedAmount;
    </script>
</body>

</html>