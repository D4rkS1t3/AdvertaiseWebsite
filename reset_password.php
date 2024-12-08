<?php
require 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $query = $db->prepare("SELECT * FROM users WHERE reset_token = :token AND token_expiry > NOW()");
    $query->bindParam(':token', $token);
    $query->execute();
    if ($query->rowCount() === 1) {
        $db->prepare("UPDATE users SET password = :password, reset_token = NULL, token_expiry = NULL WHERE reset_token = :token")
            ->execute([':password' => $new_password, ':token' => $token]);
        header("Location: login.php?message=Password reset successfully!");
        exit();
    } else {
        $msg = "Invalid or expired token.";
    }
} elseif (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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
                <h3 class="mb-5">Reset Your Password</h3>
                <!-- Display any error/success message -->
                <?php if (isset($msg)) { ?>
                  <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($msg); ?>
                  </div>
                <?php } ?>
                <!-- Reset Password Form -->
                <form method="POST" action="reset_password.php">
                  <input type="hidden" name="token" value="<?php echo htmlspecialchars($token ?? ''); ?>">
                  <div class="form-outline mb-4">
                    <input type="password" name="password" placeholder="New Password" class="form-control form-control-lg" required />
                  </div>
                  <div class="form-outline mb-4">
                    <input type="password" name="confirm_password" placeholder="Confirm New Password" class="form-control form-control-lg" required />
                  </div>
                  <button class="btn btn-primary btn-lg btn-block" type="submit">Reset Password</button>
                </form>
                <hr class="my-4">
                <p>Back to <a href="login.php">Login</a></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>

