<?php
session_start();
include("function/con.php");

$User_ID = isset($_SESSION['customers_id']) ? $_SESSION['customers_id'] : null;
$User_Name = isset($_SESSION['username']) ? $_SESSION['username'] : null;
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
    body {
        font-family: 'Rubik', sans-serif;
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

    .content-section {
        padding: 50px 0;
    }

    .content-section h2 {
        font-size: 2.5rem;
        margin-bottom: 20px;
    }

    .content-section p {
        font-size: 1.2rem;
        line-height: 1.6;
    }

    .team-section {
        background-color: #f9f9f9;
        padding: 50px 0;
    }

    .team-member {
        text-align: center;
        margin-bottom: 30px;
    }

    .team-member img {
        border-radius: 50%;
        max-width: 150px;
        margin-bottom: 20px;
    }

    .team-member h4 {
        font-size: 1.5rem;
        margin-bottom: 10px;
    }

    .team-member p {
        font-size: 1rem;
        color: #777;
    }
    </style>
    <title>About Us | Tech Arena</title>
</head>

<body>
    <?php require 'navbar.php'; ?>

    <div class="hero-section">
        <h1>About Us</h1>
        <p>Learn more about our company, mission, and values.</p>
    </div>

    <div class="container content-section">
        <div class="row mb-5">
            <div class="col-md-12 text-center">
                <h2>Our Company</h2>
                <p class="lead">Welcome to Tech Arena! We are a leading provider of innovative technology solutions,
                    offering a wide range of products including boards, modules, and sensors. Our mission is to empower
                    creators and innovators by providing them with the best tools and resources available.</p>
            </div>
        </div>
        <div class="row mb-5">
            <div class="col-md-6">
                <img src="img/our-mission.png" alt="Our Mission" class="img-fluid rounded shadow">
            </div>
            <div class="col-md-6 d-flex align-items-center">
                <div>
                    <h2>Our Mission</h2>
                    <p class="lead">Our mission is to drive innovation and make advanced technology accessible to
                        everyone. We strive to offer high-quality products that meet the needs of both professionals and
                        hobbyists. Whether you are building a prototype or creating a new product, we are here to
                        support you every step of the way.</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center">
                <h2>Our Values</h2>
            </div>
            <div class="col-md-4 mt-4">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <i class="fa fa-lightbulb-o fa-3x mb-3" aria-hidden="true"></i>
                        <h5 class="card-title">Innovation</h5>
                        <p class="card-text">Continuously pushing the boundaries of what is possible.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-4">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <i class="fa fa-check-circle fa-3x mb-3" aria-hidden="true"></i>
                        <h5 class="card-title">Quality</h5>
                        <p class="card-text">Providing reliable and high-performing products.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-4">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <i class="fa fa-user fa-3x mb-3" aria-hidden="true"></i>
                        <h5 class="card-title">Customer Focus</h5>
                        <p class="card-text">Putting our customers at the center of everything we do.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-4">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <i class="fa fa-shield fa-3x mb-3" aria-hidden="true"></i>
                        <h5 class="card-title">Integrity</h5>
                        <p class="card-text">Acting with honesty and transparency. <br> &nbsp;</p>

                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-4">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <i class="fa fa-users fa-3x mb-3" aria-hidden="true"></i>
                        <h5 class="card-title">Community</h5>
                        <p class="card-text">Building a strong community of creators and innovators.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="team-section">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h2>Meet Our Team</h2>
                    <p>We are a team of passionate professionals dedicated to making technology accessible to everyone.
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 team-member">
                    <img src="img/reymart.jpg" alt="Team Member">
                    <h4>Reymart Villacruz</h4>
                    <p>CEO</p>
                </div>
                <div class="col-md-4 team-member">
                    <img src="img/vienmar.jpg" alt="Team Member">
                    <h4>Vien Mar Gelantagaan</h4>
                    <p>CTO</p>
                </div>
                <div class="col-md-4 team-member">
                    <img src="img/ashlie.jpg" alt="Team Member">
                    <h4>Ashlie Diaz</h4>
                    <p>COO</p>
                </div>
            </div>


        </div>
    </div>

    <?php require 'footer.php'; ?>
</body>

</html>