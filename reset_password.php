<?php
require 'db.php';
require 'mailSender.php';

$msg = ""; // Komunikaty błędów lub sukcesów
$token = null;

// Obsługa przesłania formularza resetu hasła
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['token']) && !empty($_POST['token'])) {
        // Reset hasła za pomocą tokena
        $token = $_POST['token'];
        $new_password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);

        if ($new_password !== $confirm_password) {
            $msg = "Passwords do not match.";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $query = $db->prepare("SELECT * FROM users WHERE reset_token = :token AND token_expiry > NOW()");
            $query->bindParam(':token', $token);
            $query->execute();

            if ($query->rowCount() === 1) {
                $db->prepare("UPDATE users SET password = :password, reset_token = NULL, token_expiry = NULL WHERE reset_token = :token")
                    ->execute([':password' => $hashed_password, ':token' => $token]);
                header("Location: signin.php?message=Password reset successfully!");
                exit();
            } else {
                $msg = "Invalid or expired token.";
            }
        }
    } elseif (isset($_POST['email']) && !empty($_POST['email'])) {
        // Wysyłanie tokena resetu na email
        $email = trim($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $msg = "Invalid email address.";
        }
        else {
                $query = $db->prepare("SELECT * FROM users WHERE email = :email");
                $query->bindParam(':email', $email);
                $query->execute();

                if ($query->rowCount() === 1) {
                    $user = $query->fetch(PDO::FETCH_ASSOC);
                    $token = bin2hex(random_bytes(32)); // Generowanie unikalnego tokena
                    $expiry = date("Y-m-d H:i:s", strtotime("+1 hour")); // Ważność tokena 1 godzina

                    $db->prepare("UPDATE users SET reset_token = :token, token_expiry = :expiry WHERE email = :email")
                        ->execute([':token' => $token, ':expiry' => $expiry, ':email' => $email]);


                    // Wyślij email
                    sendPasswordResetEmail($email, $token);

                    $msg = "If the email exists, a password reset link has been sent.";
                } else {
                    $msg = "If the email exists, a password reset link has been sent.";
                }
              }
    }
} elseif (isset($_GET['token'])) {
    // Token przesłany w URL
    $token = urldecode($_GET['token']);
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body {
        background-color: #508bfc;
      }
      .card {
        border-radius: 1rem;
      }
      button {
        margin-bottom: 15px;
        width: 100%;
        max-width: 400px;
        height: 50px;
        font-size: 16px;
        border-radius: 0.375rem;
      }
    </style>
  </head>
  <body>
    <section class="vh-100">
      <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
          <div class="col-12 col-md-8 col-lg-6 col-xl-5">
            <div class="card shadow-2-strong">
              <div class="card-body p-5 text-center">
                <h3 class="mb-5"><?php echo $token ? "Reset Your Password" : "Forgot Password"; ?></h3>
                <!-- Display any error/success message -->
                <?php if ($msg) { ?>
                  <div class="alert <?php echo strpos($msg, 'success') !== false ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                    <?php echo htmlspecialchars($msg); ?>
                  </div>
                <?php } ?>

                <!-- Form for entering email -->
                <?php if (!$token) { ?>
                <form method="POST" action="reset_password.php">
                  <div class="form-outline mb-4">
                    <input type="email" name="email" placeholder="Enter your email" class="form-control form-control-lg" required />
                  </div>
                  <button class="btn btn-primary btn-lg btn-block" type="submit">Send Reset Link</button>
                </form>
                <?php } ?>

                <!-- Form for resetting password -->
                <?php if ($token) { ?>
                <form method="POST" action="reset_password.php">
                  <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                  <div class="form-outline mb-4">
                    <input type="password" name="password" placeholder="New Password" class="form-control form-control-lg" required />
                  </div>
                  <div class="form-outline mb-4">
                    <input type="password" name="confirm_password" placeholder="Confirm New Password" class="form-control form-control-lg" required />
                  </div>
                  <button class="btn btn-primary btn-lg btn-block" type="submit">Reset Password</button>
                </form>
                <?php } ?>

                <hr class="my-4">
                <p>Back to <a href="signin.php">Login</a></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
