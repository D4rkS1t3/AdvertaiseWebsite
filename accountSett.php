<?php
session_start();
require 'db.php'; // Połączenie z bazą danych

// nie zalogwany to zaloguj sie
if (!isset($_SESSION['session_id'])) {
    header("Location: signin.php");
    exit();
}
$sessionID = $_SESSION['session_id'];
$query = $db->prepare("SELECT * FROM users WHERE session_id = :session_id");
$query->bindParam(':session_id', $sessionID);
$query->execute();

if ($query->rowCount() === 1)
{
    $row = $query->fetch(PDO::FETCH_ASSOC);
    $oldUsername = $row['username'];
    $oldEmail = $row['email'];
}
else 
{
    header("Location: dashboard.php"); // Przekierowanie na stronę użytkownika
    exit();
}

$newUsername = '';
$newEmail = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    $newUsername = $oldUsername;
    $newEmail = $oldEmail;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $postUsername = trim($_POST['username']);
    $postEmail = trim($_POST['email']);
    $postOldPassword = trim($_POST['oldPassword']);
    $postNewPassword = trim($_POST['newPassword']);
    if (empty($postUsername) || empty($postEmail) || empty($postOldPassword) || empty($postNewPassword)) {
        $msg = "Username/Email and password are required!";
    }
    if (!filter_var($postEmail, FILTER_VALIDATE_EMAIL))
    {
        $msg = "Invalid email!";
    }
    $query = $db->prepare("SELECT * FROM users WHERE session_id = :session_id");
    $query->bindParam(':session_id', $sessionID);
    $query->execute();
    if ($query->rowCount() != 1) {
        $msg = "Something goes wrong!";
    } else
    {
        $row = $query->fetch(PDO::FETCH_ASSOC);
        if (!password_verify($postOldPassword, $row['password']))
        {
            $msg = "Invalid password!";
        }
        else {
            $hashedPassword = password_hash($postNewPassword, PASSWORD_BCRYPT);
            $update = $db->prepare("UPDATE users SET username = :username, email = :email, password = :password WHERE session_id = :session_id");
            $update->bindParam(':session_id', $sessionID);
            $update->bindParam(':username', $postUsername);
            $update->bindParam(':email', $postEmail);
            $update->bindParam(':password', $hashedPassword);
            try {
                $update->execute();
                header("Location: dashboard.php");
                exit();
            } catch (PDOException $e) {
                $msg = "Wystąpił błąd podczas aktualizacji danych: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Your advertisement</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />

    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="css/mojestyle.css" rel="stylesheet" />
</head>
<style>
    body {
        padding-top: 47px;
        /* Przesuwa zawartość w dół o wysokość navbar */
        margin: 0;
    }
    
body{
    margin-top:20px;
    background:#f5f5f5;
}
/**
 * Panels
 */
/*** General styles ***/
.panel {
  box-shadow: none;
}
.panel-heading {
  border-bottom: 0;
}
.panel-title {
  font-size: 17px;
}
.panel-title > small {
  font-size: .75em;
  color: #999999;
}
.panel-body *:first-child {
  margin-top: 0;
}
.panel-footer {
  border-top: 0;
}

.panel-default > .panel-heading {
    color: #333333;
    background-color: transparent;
    border-color: rgba(0, 0, 0, 0.07);
}

form label {
    color: #999999;
    font-weight: 400;
}

.form-horizontal .form-group {
  margin-left: -15px;
  margin-right: -15px;
}
@media (min-width: 768px) {
  .form-horizontal .control-label {
    text-align: right;
    margin-bottom: 0;
    padding-top: 7px;
  }
}

.profile__contact-info-icon {
    float: left;
    font-size: 18px;
    color: #999999;
}
.profile__contact-info-body {
    overflow: hidden;
    padding-left: 20px;
    color: #999999;
}
.profile-avatar {
  width: 200px;
  position: relative;
  margin: 0px auto;
  margin-top: 196px;
  border: 4px solid #f3f3f3;
}
</style>


<body class="sb-nav-fixed">
    <!-- Nav -->

    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" id="navFirst" href="index.html">Advertise Website</a>
        <!-- Sidebar Toggle-->


        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">

            <li class="nav-item dropdown">
                <a style="text-decoration: none;" class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="ms-2">Your account</span><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="./accountSett.php">Settings</a></li>
                    <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li><a class="dropdown-item" href="#!">Logout</a></li>
                </ul>
            </li>
        </ul>
        <button type="button" class="btn btn-light" id="btn-add" onclick="window.location.href = '404.html';">
            <span class="ms-2">Add an advertisement</span></button>
    </nav>



    <!-- Main Layout -->
    <div style="min-height: 630px;" id="kontener" class="container-fluid bg-light mt-3">
        <div class="row">
            <!-- Left Sidebar (Empty) -->
            <div class="col-md-2 bg-light" id="sideBar1"></div>

            <!-- Main Content -->
            <main class="col-md-8 bg-light">
            <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
<div class="container bootstrap snippets bootdeys">
<div class="row">
  <div class="col-xs-12 col-sm-9">
    <form class="form-horizontal">
        <div class="panel panel-default">
          <div class="panel-body text-center">
           <img src="./img/account-icon-template-vector.jpg" class="img-circle profile-avatar" alt="User avatar">
          </div>
        </div>

      <div class="panel panel-default">
        <div class="panel-heading">
        <h4 class="panel-title">Security</h4>
        </div>
        <div class="panel-body">
        <div class="form-group">
            <label class="col-sm-2 control-label">Username</label>
            <div class="col-sm-10">
              <input type="text" id="username" class="form-control" value="<?=$newUsername?>">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">Email</label>
            <div class="col-sm-10">
              <input type="email" id="email" class="form-control" value="<?=$newEmail?>">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">Current password</label>
            <div class="col-sm-10">
              <input type="password" id="oldPassword" class="form-control" placeholder="old password">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">New password</label>
            <div class="col-sm-10">
              <input type="password" id="newPassword" class="form-control" placeholder="new password">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">Repeat new pass</label>
            <div class="col-sm-10">
              <input type="password" id="repeatNewPassword" class="form-control" placeholder="repeat new password">
            </div>
          </div>

          <div class="form-group">
            <div class="col-sm-10 col-sm-offset-2">
              <button id="submit" type="submit" class="btn btn-primary">Submit</button>
              <button id="reset" type="reset" class="btn btn-default">Reset</button>
              <button id="cancel" type="cancel" class="btn btn-default">Cancel</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
</div>
            </main>

            <!-- Right Sidebar (Empty) -->
            <div class="col-md-2 bg-light" id="sideBar2"></div>
        </div>
    </div>

    
    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-5 mt-auto">
        &copy; 2024 Advertise Website
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</body>

</html>

</div>
</div>
</div>
</div>
<script>
    $(document).ready(function () {
        // Obsługa przesyłania formularza
        $("#submit").click(function (e) {
            e.preventDefault(); // Zapobiega domyślnej akcji formularza

            // Pobierz dane z formularza
            var username = $("#username").val();
            var email = $("#email").val();
            var oldPassword = $("#oldPassword").val();
            var newPassword = $("#newPassword").val();
            var repeatNewPassword = $("#repeatNewPassword").val();

            // Prosta walidacja po stronie klienta
            if (username === "" || email === "" || oldPassword === "") {
                alert("Uzupełnij wszystkie wymagane pola!");
                return;
            }
            if (newPassword !== repeatNewPassword) {
                alert("Nowe hasła muszą być zgodne!");
                return;
            }

            // Wyślij dane do serwera za pomocą AJAX
            $.ajax({
                url: 'accountSett.php', // Ścieżka do pliku PHP
                type: 'POST', // Metoda HTTP
                data: {
                    username: username,
                    email: email,
                    oldPassword: oldPassword,
                    newPassword: newPassword
                },
                success: function (response) {
                    // Wyświetl komunikat zwrotny
                    alert(response);

                    // Opcjonalne resetowanie formularza po sukcesie
                    $("#username").val(username);
                    $("#email").val(email);
                    $("#oldPassword").val("");
                    $("#newPassword").val("");
                    $("#repeatNewPassword").val("");
                },
                error: function () {
                    alert("Wystąpił błąd podczas aktualizacji danych.");
                }
            });
        });

        // Obsługa resetowania formularza
        $("#reset").click(function () {
            // Opcjonalne: zapytaj użytkownika, czy na pewno chce zresetować
            if (confirm("Czy na pewno chcesz zresetować formularz?")) {
                $("#username").val("<?= $newUsername ?>"); // Przywróć stare dane
                $("#email").val("<?= $newEmail ?>");
                $("#oldPassword").val("");
                $("#newPassword").val("");
                $("#repeatNewPassword").val("");
            }
        });
    });
    //obsluga anulwanie formularza
    $("#cancel").click(function (e) {
    e.preventDefault(); // Zapobiega domyślnej akcji formularza

    // Wyświetlenie okna potwierdzenia
    if (confirm("Czy na pewno chcesz anulować zmiany?")) {
        // Jeśli użytkownik kliknie "OK", przekierowanie na dashboard
        window.location.href = "dashboard.php";
    }
});

    
</script>

<!-- Bootstrap core JavaScript-->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="js/sb-admin-2.min.js"></script>

<!-- Page level plugins -->
<script src="vendor/chart.js/Chart.min.js"></script>

<!-- Page level custom scripts -->
<script src="js/demo/chart-area-demo.js"></script>
<script src="js/demo/chart-pie-demo.js"></script>

</body>

</html>