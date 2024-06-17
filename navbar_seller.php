<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'function/con.php';

$Seller_ID = isset($_SESSION['seller_id']) ? $_SESSION['seller_id'] : null;
$Seller_Name = isset($_SESSION['seller_username']) ? $_SESSION['seller_username'] : null;
?>

<link rel="stylesheet" href="css/fa4/css/font-awesome.css">
<link rel="stylesheet" href="css/fa4/css/font-awesome.min.css">
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/bootstrap.min.css">

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

.footer {
    background-color: #343a40;
    color: white;
    padding: 40px 0;
    text-align: center;
    flex-shrink: 0;
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
</style>

<div class="navbar-fixed-top text-center">
    <nav class="navbar navbar-expand-sm navbar-light bg-light fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">RoboTech Seller</a>
            <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapsibleNavId" aria-controls="collapsibleNavId" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse bg-light" id="collapsibleNavId">
                <ul class="navbar-nav me-auto mt-2 mt-lg-0 d-flex justify-content-center">
                    <li class="nav-item">
                        <a class="nav-link active" href="seller_dashboard.php" aria-current="page">Home <span
                                class="visually-hidden">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="seller_products.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="seller_orders.php">Orders</a>
                    </li>
                    <?php if ($Seller_ID == 0): ?>
                    <li class="nav-item">
                        <a class="nav-link btn btn-warning" href="admin_message.php">View Messages</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <i id="dropdownId" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                            class="fa fa-fw fa-2x" id="dropdownIcon">&#xf007;</i>
                        <div class="dropdown-menu" aria-labelledby="dropdownId">
                            <h5><?php echo $Seller_Name; ?></h5>
                            <a class="dropdown-item" href="#">Profile</a>
                            <a class="dropdown-item" href="s_logout.php">Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>