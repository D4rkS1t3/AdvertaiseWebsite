<?php
session_start();
require 'db.php'; // Połączenie z bazą danych

if (isset($_SESSION['session_id'])) {
  header("Location: index.php");
  exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $terms_accepted = isset($_POST['terms']); // Sprawdzanie, czy checkbox został zaznaczony

    // Walidacja backendowa
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $msg = "All fields are required!";
    } elseif (!$terms_accepted) {
        $msg = "You must accept the terms and conditions to register!";
    } elseif ($password !== $confirm_password) {
        $msg = "Passwords do not match!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $msg = "Invalid email";
    } else {
        // Sprawdź, czy użytkownik już istnieje
        $query = $db->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $query->bindParam(':username', $username);
        $query->bindParam(':email', $email);
        $query->execute();

        if ($query->rowCount() > 0) {
            $msg = "Username or email already taken!";
        } else {
            // Hashowanie hasła
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $insert = $db->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $insert->bindParam(':username', $username);
            $insert->bindParam(':email', $email);
            $insert->bindParam(':password', $hashed_password);

            if ($insert->execute()) {
                $msg = "Registration successful! You can now log in.";
                header("Location: signin.php"); // Przekierowanie na stronę logowania
                exit();
            } else {
                $msg = "Something went wrong. Please try again.";
            }
        }
    }
}

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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

              <h3 class="mb-5">Sign Up</h3>
              
              <?php if (isset($msg)) { ?>
              <div class="alert alert-danger" role="alert">
                <?php echo $msg; ?>
              </div>
              <?php } ?>

              <form method="POST" action="signup.php">
                <div data-mdb-input-init class="form-outline mb-4">
                  <input type="text" name="username" placeholder="Username" class="form-control form-control-lg" required />
                </div>

                <div data-mdb-input-init class="form-outline mb-4">
                  <input type="email" name="email" placeholder="Email" class="form-control form-control-lg" required />
                </div>

                <div data-mdb-input-init class="form-outline mb-4">
                  <input type="password" name="password" placeholder="Password" class="form-control form-control-lg" required />
                </div>

                <div data-mdb-input-init class="form-outline mb-4">
                  <input type="password" name="confirm_password" placeholder="Confirm Password" class="form-control form-control-lg" required />
                </div>

                <!-- Checkbox regulaminu -->
                <div class="form-check d-flex align-items-center mb-4">
                  <input class="form-check-input" type="checkbox" name="terms" id="terms">
                  <label class="form-check-label" for="terms">
                    I agree to the <a href="terms.php" target="_blank">terms and conditions</a>
                  </label>
                </div>

                <button class="btn btn-primary btn-lg btn-block" type="submit">Register</button>
              </form>

              <hr class="my-4">

              <p>Already have an account? <a href="signin.php">Sign In</a></p>

            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
