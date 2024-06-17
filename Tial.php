<?php 



$User_ID =  $_SESSION['customers_id'];

$sql1 = "SELECT c.*, p.product_name, p.product_img, p.product_price, p.product_type
FROM cart_tbl c
JOIN products_tbl p ON c.product_id = p.product_id; ";
$result1 = mysqli_query($con, $sql1);

if(!$result1){
   die("Invalid Query: ".mysqli_error($con));
}


if (isset($_POST['delete-item'])) {
    $productId = $_POST['product_id'];

    $deleteQuery = "DELETE FROM `cart_tbl` WHERE product_id = '$productId'";

    if (mysqli_query($con, $deleteQuery)) {
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
                      <strong>Item deleted to cart successfully!</strong>
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

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
</head>
<link rel="stylesheet" href="css/fa4/css/font-awesome.css" />
<link rel="stylesheet" href="css/bootstrap.css" />

<body>
    <nav class="navbar navbar-expand-sm navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">RoboTech</a>
            <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapsibleNavId" aria-controls="collapsibleNavId" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="collapsibleNavId">
                <ul class="navbar-nav me-auto mt-2 mt-lg-0 d-flex justify-content-center">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" aria-current="page">Home <span
                                class="visually-hidden">(current)</span></a>
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
                    <li>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#bagModal">
                            <div class="icon_wrapper hoverable col-md-1 col-sm-2 col-lg-1">
                                <i class="fa fa-fw fa-lg">&#xf07a;</i>
                                <span class="classname"></span>
                            </div>
                        </a>
                    </li>

                    <li>
                        <div class="icon_wrapper hoverable col-md-1 col-sm-2 col-lg-1">
                            <i class="fa fa-fw fa-lg">&#xf007;</i>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdownId" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownId">
                            <a class="dropdown-item" href="#">Profile</a>
                            <a class="dropdown-item" href="c_logout.php">Logout</a>
                        </div>
                    </li>
                </ul>
                <form class="d-flex my-2 my-lg-0">
                    <input class="form-control me-sm-2" type="text" placeholder="Search" />
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">
                        Search
                    </button>
                </form>
            </div>
        </div>
    </nav>
    <script src="css/bootstrap_js.js"></script>
</body>

</html>