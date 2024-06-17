<?php
session_start();
include('function/con.php');

if (!isset($_SESSION['seller_id'])) {
    header("location: seller_login.php");
    exit();
}

$Seller_ID = $_SESSION['seller_id'];
$errorMsg = "";
$Name = $Desc = $Price = $Type = $Status = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add-item'])) {
    $Name = $_POST['product_name'];
    $Desc = mysqli_real_escape_string($con, $_POST['product_description']);
    $Price = $_POST['product_price'];
    $Type = $_POST['product_type'];
    $Status = $_POST['product_status'];

    $file = $_FILES['file'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileExt = strtolower(end(explode('.', $fileName)));

    $allowed = ['png', 'jpg', 'jpeg'];
    if (in_array($fileExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 50000000) {
                $fileNameNew = $Name . " 1." . $fileExt;
                $baseDirectory = 'C:/xampp/htdocs/robotek/img/';
                $fileDestination = $baseDirectory . $Type . '/' . $fileNameNew;
                move_uploaded_file($fileTmpName, $fileDestination);

                $query = "INSERT INTO products_tbl (product_name, product_img, product_description, product_price, product_type, product_status, owner) 
                          VALUES ('$Name', '$Name', '$Desc', '$Price', '$Type', '$Status', '$Seller_ID')";
                if (mysqli_query($con, $query)) {
                    header("location: seller_products.php");
                    exit();
                } else {
                    $errorMsg = "Error registering item: " . mysqli_error($con);
                }
            } else {
                $errorMsg = "Your file is too big!";
            }
        } else {
            $errorMsg = "There was an error uploading your file!";
        }
    } else {
        $errorMsg = "You cannot upload files of this type!";
    }
}

if (isset($_POST['cancel'])) {
    header("location: seller_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Items</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <style>
    .container {
        margin-top: 50px;
    }

    .form-horizontal .form-group {
        margin-bottom: 15px;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }

    .form-control {
        border-radius: 4px;
    }
    </style>
</head>

<body>
    <?php include 'navbar_seller.php'; ?>

    <div class="container"><br>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Add Product</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($errorMsg != ""): ?>
                        <div class="alert alert-danger"><?php echo $errorMsg; ?></div>
                        <?php endif; ?>
                        <form class="form-horizontal" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="product_image">Product Image</label>
                                <input id="product_image" name="file" type="file" accept="image/*" class="form-control"
                                    required />
                            </div>

                            <div class="form-group">
                                <label for="product_name">Product Name</label>
                                <input id="product_name" name="product_name" type="text"
                                    placeholder="Enter Product Name" class="form-control" required />
                            </div>

                            <div class="form-group">
                                <label for="product_description">Product Description</label>
                                <textarea id="product_description" name="product_description"
                                    placeholder="Enter Product Description" class="form-control" rows="4"
                                    required></textarea>
                            </div>

                            <div class="form-group">
                                <label for="product_price">Product Price</label>
                                <input id="product_price" name="product_price" type="text"
                                    placeholder="Enter Product Price" class="form-control" required />
                            </div>

                            <div class="form-group">
                                <label for="product_type">Product Type</label>
                                <select id="product_type" name="product_type" class="form-control" required>
                                    <option value="">Select Product Type</option>
                                    <option value="Board">Board</option>
                                    <option value="Sensor">Sensor</option>
                                    <option value="Module">Module</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="product_status">Product Status</label>
                                <select id="product_status" name="product_status" class="form-control" required>
                                    <option value="">Select Product Status</option>
                                    <option value="Available">Available</option>
                                    <option value="Out of Stock">Out of Stock</option>
                                </select>
                            </div>

                            <div class="form-group text-center">
                                <button id="add-item" name="add-item" class="btn btn-primary">Add Item</button>
                                <a href="seller_products.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <?php require 'footer.php';?>
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="css/bootstrap_js.js"></script>
</body>

</html>