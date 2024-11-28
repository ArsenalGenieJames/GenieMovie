<?php
session_start();

// Database credentials
$host = 'localhost';
$db = 'genie_movie';
$user = 'root';
$pass = '';

// Create a connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    // Validate input
    if (empty($email)) {
        $error = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Prepare the query
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");

        if ($stmt === false) {
            die('MySQL prepare error: ' . $conn->error);  // Output MySQL error message
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // User exists, proceed to send the reset link
            $success = "If this email exists, we will send a reset link shortly.";
        } else {
            $error = "No user found with that email address.";
        }

        $stmt->close();
    }
}
$conn->close();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Forgot Password | GenieMovie</title>
    <link href="style.css" rel="stylesheet">
    <link href="blue.css" id="theme" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
</head>

<body>
    <section class="bg-light p-3 p-md-4 p-xl-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-6">
                    <div class="card border-light-subtle shadow-sm">
                        <div class="card-body p-4">
                            <h4 class="text-center">Forgot Password</h4>
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            <?php if (isset($success)): ?>
                                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                            <?php endif; ?>
                            <form action="" method="POST">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com" required>
                                    <label for="email">Email</label>
                                </div>
                                <div class="d-grid">
                                    <button class="btn btn-dark btn-lg" type="submit">Reset Password</button>
                                </div>
                            </form>
                            <div class="text-center mt-3">
                                <a href="index.php" class="link-secondary">Back to login</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>