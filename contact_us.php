<?php
session_start();
include("function/con.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $query = "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    if ($stmt->execute()) {
        $_SESSION['contact_message'] = "Your message has been sent successfully!";
    } else {
        $_SESSION['contact_message'] = "There was an error sending your message. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/phones.css">
    <link rel="stylesheet" href="css/products.css">
    <link rel="stylesheet" href="css/footer.css">
    <style>
    .contact-section {
        padding: 50px 0;
    }

    .contact-section h2 {
        text-align: center;
        margin-bottom: 30px;
        font-size: 2.5em;
    }

    .contact-form {
        background: #f9f9f9;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .contact-form .form-group {
        margin-bottom: 20px;
    }

    .contact-form .form-control {
        border-radius: 20px;
    }

    .contact-form button {
        border-radius: 20px;
        padding: 10px 30px;
    }

    .contact-info {
        text-align: center;
        margin-top: 30px;
    }

    .contact-info p {
        margin: 10px 0;
    }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container contact-section">
        <h2>Contact Us</h2>
        <?php
        if (isset($_SESSION['contact_message'])) {
            echo "<div class='alert alert-info' role='alert'>" . $_SESSION['contact_message'] . "</div>";
            unset($_SESSION['contact_message']);
        }
        ?>
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <form class="contact-form" method="post" action="contact_us.php">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="email">Your Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            placeholder="Enter your email address" required>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" placeholder="Enter subject"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5"
                            placeholder="Enter your message" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
        <div class="contact-info">
            <h3>Our Contact Information</h3>
            <p><i class="fa fa-map-marker"></i> Address: 123 Main Street, Anytown, USA</p>
            <p><i class="fa fa-phone"></i> Phone: +1 234 567 890</p>
            <p><i class="fa fa-envelope"></i> Email: info@robotech.com</p>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>
</body>

</html>