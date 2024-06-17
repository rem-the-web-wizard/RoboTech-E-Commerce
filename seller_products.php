<?php
session_start();
include("function/con.php");

$seller_id = $_SESSION['seller_id'];

// Fetch products by status
function fetchProducts($con, $seller_id) {
    $query = "SELECT * FROM products_tbl WHERE owner = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $seller_id);
    $stmt->execute();
    return $stmt->get_result();
}

$products = fetchProducts($con, $seller_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit_product'])) {
        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price'];
        $product_description = $_POST['product_description'];
        $product_status = $_POST['product_status'];
        $product_type = $_POST['product_type'];

        $query = "UPDATE products_tbl SET product_name = ?, product_price = ?, product_description = ?, product_status = ?, product_type = ? WHERE product_id = ? AND owner = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ssssssi", $product_name, $product_price, $product_description, $product_status, $product_type, $product_id, $seller_id);
        $stmt->execute();
        header("Location: seller_products.php");
    }
}

// Fetch product image
function getProductImage($productType, $productImg) {
    $productImgPath = "img/$productType/" . $productImg . " 1";
    $extensions = ['png', 'jpeg', 'jpg'];
    foreach ($extensions as $extension) {
        $imagePath = $productImgPath . '.' . $extension;
        if (file_exists($imagePath)) {
            return $imagePath;
        }
    }
    return "img/default-image.png";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Products</title>
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
    .available {
        background-color: #d4edda;
    }

    .unavailable {
        background-color: #f8d7da;
    }

    .out-of-stock {
        background-color: #fff3cd;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }

    .modal-header {
        background-color: #007bff;
        color: white;
    }

    .modal-footer {
        justify-content: center;
    }

    .form-label {
        font-weight: bold;
    }

    .container {
        margin-top: 50px;
    }

    h1,
    h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    .add-item-btn {
        margin-bottom: 20px;
        text-align: center;
    }
    </style>
</head>

<body>
    <?php include 'navbar_seller.php'; ?>
    <div class="container">
        <br>
        <div class="add-item-btn">
            <a href="add_items.php" class="btn btn-success">Add New Item</a>
        </div>
        <br><br>
        <h1>My Products</h1>
        <hr>
        <br>
        <div class="row">
            <div class="col-md-12">
                <h2>Products</h2>
                <input type="text" id="search" class="form-control" placeholder="Search for products...">
                <br>
                <table class="table table-hover bg-transparent" id="productTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $products->fetch_assoc()): ?>
                        <?php 
                            $rowClass = '';
                            if ($row['product_status'] == 'Available') {
                                $rowClass = 'available';
                            } elseif ($row['product_status'] == 'Unavailable') {
                                $rowClass = 'unavailable';
                            } elseif ($row['product_status'] == 'Out of Stock') {
                                $rowClass = 'out-of-stock';
                            }
                        ?>
                        <tr class="<?php echo $rowClass; ?>">
                            <td><?php echo $row['product_name']; ?></td>
                            <td>₱<?php echo number_format($row['product_price'], 2, '.', ','); ?></td>
                            <td><?php echo $row['product_status']; ?></td>
                            <td><button class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#editModal<?php echo $row['product_id']; ?>">Edit</button></td>
                        </tr>
                        <div class="modal fade" id="editModal<?php echo $row['product_id']; ?>" tabindex="-1"
                            role="dialog" aria-labelledby="editModalLabel<?php echo $row['product_id']; ?>"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg"
                                role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel<?php echo $row['product_id']; ?>">
                                            Edit Product</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="seller_products.php">
                                            <input type="hidden" name="product_id"
                                                value="<?php echo $row['product_id']; ?>">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <img src="<?php echo getProductImage($row['product_type'], $row['product_img']); ?>"
                                                        class="img-fluid rounded" alt="Product Image">
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="product_name" class="form-label">Product
                                                            Name</label>
                                                        <input type="text" class="form-control" name="product_name"
                                                            value="<?php echo $row['product_name']; ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="product_price" class="form-label">Product
                                                            Price</label>
                                                        <input type="number" class="form-control" name="product_price"
                                                            value="<?php echo $row['product_price']; ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="product_description" class="form-label">Product
                                                            Description</label>
                                                        <textarea class="form-control" name="product_description"
                                                            rows="3"
                                                            required><?php echo $row['product_description']; ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="product_status" class="form-label">Product
                                                            Status</label>
                                                        <select class="form-control" name="product_status" required>
                                                            <option value="Available"
                                                                <?php echo $row['product_status'] == 'Available' ? 'selected' : ''; ?>>
                                                                Available</option>
                                                            <option value="Unavailable"
                                                                <?php echo $row['product_status'] == 'Unavailable' ? 'selected' : ''; ?>>
                                                                Unavailable</option>
                                                            <option value="Out of Stock"
                                                                <?php echo $row['product_status'] == 'Out of Stock' ? 'selected' : ''; ?>>
                                                                Out of Stock</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="product_type" class="form-label">Product
                                                            Type</label>
                                                        <select class="form-control" name="product_type" required>
                                                            <option value="Board"
                                                                <?php echo ($row['product_type'] == 'Board') ? 'selected' : ''; ?>>
                                                                Board</option>
                                                            <option value="Module"
                                                                <?php echo ($row['product_type'] == 'Module') ? 'selected' : ''; ?>>
                                                                Module</option>
                                                            <option value="Sensor"
                                                                <?php echo ($row['product_type'] == 'Sensor') ? 'selected' : ''; ?>>
                                                                Sensor</option>
                                                        </select>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" name="edit_product"
                                                            class="btn btn-primary">Save changes</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
        $("#search").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#productTable tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
    </script>
</body>

</html>