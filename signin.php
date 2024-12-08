<?php
session_start();
require 'db.php'; // Połączenie z bazą danych

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier']); // Może być login lub email
    $password = trim($_POST['password']);

    // Walidacja backendowa
    if (empty($identifier) || empty($password)) {
        $msg = "Username/Email and password are required!";
    } else {
        // Zapytanie sprawdzające zarówno username, jak i email
        $query = $db->prepare("SELECT * FROM users WHERE username = :identifier OR email = :identifier");
        $query->bindParam(':identifier', $identifier);
        $query->execute();

        if ($query->rowCount() === 1) {
            $details = $query->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $details['password'])) {
                $_SESSION['username'] = $details['username'];
                header("Location: dashboard.php"); // Przekierowanie na stronę użytkownika
                exit();
            } else {
                $msg = "Incorrect password.";
            }
        } else {
            $msg = "That username or email does not exist!";
        }
    }
}

if (!isset($_SESSION['username'])) {
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
      button {
        margin-bottom: 15px;
        width: 100%;
        max-width: 400px;
        height: 50px;
        font-size: 16px;
        border-radius: 0.375rem;
      }

      .form-check-input {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        border: 2px solid #508bfc;
        background-color: white;
        cursor: pointer;
      }
      .form-check-input:checked {
        background-color: #508bfc;
        border-color: #508bfc;
      }
      .form-check-label {
        margin-left: 8px;
        font-size: 16px;
        color: #333;
      }
    </style>
  </head>
  <body>
  <section class="vh-100" style="background-color: #508bfc;">
    <div class="container py-5 h-100">
      <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-12 col-md-8 col-lg-6 col-xl-5">
          <div class="card shadow-2-strong" style="border-radius: 1rem;">
            <div class="card-body p-5 text-center">

              <h3 class="mb-5">Sign In</h3>
              
              <?php if (isset($msg)) { ?>
              <div class="alert alert-danger" role="alert">
                <?php echo $msg; ?>
              </div>
              <?php } ?>

              <form method="POST" action="">
                <div class="form-outline mb-4">
                  <input type="text" name="identifier" placeholder="Username or Email" class="form-control form-control-lg" required />
                </div>

                <div class="form-outline mb-4">
                  <input type="password" name="password" placeholder="Password" class="form-control form-control-lg" required />
                </div>

                <!-- Checkbox -->
                <div class="form-check d-flex align-items-center mb-4">
                  <input class="form-check-input" type="checkbox" id="rememberMe">
                  <label class="form-check-label" for="rememberMe">Remember me</label>
                </div>

                <button class="btn btn-primary btn-lg btn-block" type="submit">Login</button>
              </form>

              <hr class="my-4">

              <p>Forgot your password? <a href="reset_password.php">Click here</a></p>
              <p>Don't have an account? <a href="signup.php">Register here</a></p>

              <button class="btn btn-lg btn-block btn-primary" style="background-color: #dd4b39; border-color: #dd4b39;" type="button">
              <a style="text-decoration: none; color:white; display: block; text-align: center;" href="https://accounts.google.com/">
                <i class="fab fa-google me-2"></i> Sign in with Google
              </a>
            </button>

            <button class="btn btn-lg btn-block btn-primary mb-2" style="background-color: #3b5998; border-color:#3b5998" type="button">
              <a style="text-decoration: none; color:white; display: block; text-align: center;" href="https://www.facebook.com/login.php/">
                <i class="fab fa-facebook-f me-2"></i> Sign in with Facebook
              </a>
            </button>

            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
<?php
}
?>
