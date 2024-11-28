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
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute SQL query to fetch user
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Verify password
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Set session variable and redirect to dashboard
            $_SESSION['user_id'] = $row['id'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with this email.";
    }

    $stmt->close();
}
$conn->close();

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>GenieMovie</title>
    <link href="style.css" rel="stylesheet">
    <link href="blue.css" id="theme" rel="stylesheet">
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
</head>

<body>
    <section class="bg-light p-3 p-md-4 p-xl-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-xxl-11">
                    <div class="card border-light-subtle shadow-sm">
                        <div class="row g-0">
                            <!-- Image Column -->
                            <div class="col-12 col-md-6">
                                <img class="img-fluid rounded-start w-100 h-100 object-fit-cover" loading="lazy"
                                    src="https://scontent.fdvo2-1.fna.fbcdn.net/v/t39.30808-6/401584586_1315257289159103_8678221443381256878_n.jpg?_nc_cat=108&ccb=1-7&_nc_sid=a5f93a&_nc_eui2=AeEK3IXjvDXWSnO1dgNyT8pu3elggTfMh0bd6WCBN8yHRs8nBmRCGkMDLwOlYqC2HFhTZhKu82-eCjWDv00SEwoI&_nc_ohc=j12bVnBFb_MQ7kNvgHHcxcP&_nc_zt=23&_nc_ht=scontent.fdvo2-1.fna&_nc_gid=AgMrAtddQlZzbcuXUphutd9&oh=00_AYAU90B4quJwBSRB_nQFvsU1K6bQBuVp1lZqObawIYxHng&oe=674E58C7"
                                    alt="Welcome back, you've been missed!">
                            </div>

                            <!-- Form Column -->
                            <div class="col-12 col-md-6 d-flex align-items-center justify-content-center">
                                <div class="col-12 col-lg-11 col-xl-10">
                                    <div class="card-body p-3 p-md-4 p-xl-5">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="mb-5">
                                                    <div class="text-center mb-4">
                                                        <a href="https://www.facebook.com/pakyuimongmama" target="_blank">
                                                            <img src="https://scontent.fdvo2-2.fna.fbcdn.net/v/t39.30808-6/466735943_1523414318343398_194845160556849356_n.jpg?_nc_cat=100&ccb=1-7&_nc_sid=a5f93a&_nc_eui2=AeGQWnlBQ5ygWvuKsDOQ4qA0MSedbrRoxWgxJ51utGjFaGmaZGLHk3vuCGbEHKrIqzjhdVE3Ac5WgRosks0-eGEV&_nc_ohc=T0UAtOW24ksQ7kNvgEreS-2&_nc_zt=23&_nc_ht=scontent.fdvo2-2.fna&_nc_gid=Az5PYKTHWNROFQCs1ewRS7r&oh=00_AYCyG60yFvhHK08uBr0ykTZStEfffnnqXq6atTFTvYAjhg&oe=674E5E1B"
                                                                alt="GenieMovie" class="rounded-circle"
                                                                style="width: 150px; height: 150px;">
                                                        </a>
                                                    </div>
                                                    <h4 class="text-center">GenieMovie</h4>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Login Form -->
                                        <form action="" method="POST">
                                            <div class="row gy-3 overflow-hidden">
                                                <div class="col-12">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="email" id="email"
                                                            placeholder="name@example.com" required>
                                                        <label for="email">Email</label>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-floating">
                                                        <input type="password" class="form-control" name="password"
                                                            id="password" placeholder="Password" required>
                                                        <label for="password">Password</label>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="remember_me"
                                                            id="remember_me">
                                                        <label class="form-check-label text-secondary" for="remember_me">
                                                            Keep me logged in
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="d-grid">
                                                        <button class="btn btn-dark btn-lg" type="submit">Log in now</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if (isset($error)): ?>
                                                <div class="alert alert-danger mt-3">
                                                    <?php echo htmlspecialchars($error); ?>
                                                </div>
                                            <?php endif; ?>
                                        </form>

                                        <!-- Footer Links -->
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="d-flex gap-2 gap-md-4 flex-column flex-md-row justify-content-center mt-5">
                                                    <a href="register.php" class="link-secondary text-decoration-none">Create new
                                                        account</a>
                                                    <a href="forgotpassword.php" class="link-secondary text-decoration-none">Forgot
                                                        password</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Form Column -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="../assets/plugins/popper/popper.min.js"></script>
    <script src="jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>

</html>