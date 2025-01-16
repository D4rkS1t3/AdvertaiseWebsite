<?php
session_start();
require 'db.php';

if (!isset($_SESSION['session_id'])) {
    header("Location: signin.php");
    exit();
}

$sessionID = $_SESSION['session_id'];

$query = $db->prepare("SELECT username, email, password FROM users WHERE session_id = :session_id");
$query->bindParam(':session_id', $sessionID);
$query->execute();
$row = $query->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo json_encode([
        "success" => false,
        "message" => "Incorrect session."
    ]);
    exit();
}

$oldUsername = $row['username'];
$oldEmail = $row['email'];
$newUsername = '';
$newEmail = '';


//wejscie w ustawienia zeby byly juz uzupelniony login i email
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $newUsername = $oldUsername;
    $newEmail = $oldEmail;
}


//usuwanie konta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deleteAccount') {
    $postPassword = trim($_POST['password']);

    if (empty($postPassword)) {
        echo json_encode([
            "success" => false,
            "message" => "Entering your password is required to delete your account."
        ]);
        exit();
    }

    if (!password_verify($postPassword, $row['password'])) {
        echo json_encode([
            "success" => false,
            "message" => "Incorrect password!"
        ]);
        exit();
    }

    try {
        $delete = $db->prepare("DELETE FROM users WHERE session_id = :session_id");
        $delete->bindParam(':session_id', $sessionID);

        if ($delete->execute()) {
            session_destroy();
            echo json_encode([
                "success" => true,
                "message" => "Your account has been deleted!",
                "redirect" => "signin.php"
            ]);
            exit();
        } else {
            echo json_encode([
                "success" => false,
                "message" => "An error occurred while deleting your account!"
            ]);
            exit();
        }
    } catch (PDOException $e) {
        echo json_encode([
            "success" => false,
            "message" => "Error:" . $e->getMessage()
        ]);
        exit();
    }
}



//edycja uzytkownika
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'editAccount') {
    $postUsername = trim($_POST['username']);
    $postEmail = trim($_POST['email']);
    $postOldPassword = trim($_POST['oldPassword']);
    $postNewPassword = trim($_POST['newPassword']);
    $repeatNewPassword = trim($_POST['repeatNewPassword']);
    if (empty($postUsername) || empty($postEmail) || empty($postOldPassword) || empty($postNewPassword) || empty($repeatNewPassword)) {
        echo json_encode([
            "success" => false,
            "message" => "All fields are required"
        ]);
        exit();
    } elseif (!filter_var($postEmail, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid e-mail!"
        ]);
        exit();
    } elseif ($postNewPassword !== $repeatNewPassword) {
        echo json_encode([
            "success" => false,
            "message" => "New passwords must match!"
        ]);
        exit();
    } elseif (!password_verify($postOldPassword, $row['password'])) {
        echo json_encode([
            "success" => false,
            "message" => "Current password is incorrect!"
        ]);
        exit();
    } else {
        try {
            $hashedPassword = password_hash($postNewPassword, PASSWORD_BCRYPT);
            $update = $db->prepare("
                UPDATE users 
                SET username = :username, email = :email, password = :password 
                WHERE session_id = :session_id
            ");
            $update->bindParam(':username', $postUsername);
            $update->bindParam(':email', $postEmail);
            $update->bindParam(':password', $hashedPassword);
            $update->bindParam(':session_id', $sessionID);
            if ($update->execute()) {
                echo json_encode([
                    "success" => true,
                    "message" => "Your password has been changed",
                    "redirect" => "dashboard.php"
                ]);
                exit();
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Error changing password."
                ]);
                exit();
            }
        } catch (PDOException $e) {
            echo json_encode([
                "success" => false,
                "message" => "Error: " . $e->getMessage()
            ]);
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsywna Nawigacja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel='stylesheet' href='https://netdna.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>

    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <style>
        .search-sec {
            padding: 2rem;
        }

        .search-slt {
            display: block;
            width: 100%;
            height: 22px;
            font-size: 1rem;
            line-height: 1.5;
            color: #55595c;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ccc;
            height: calc(4rem + 3px) !important;
            border-radius: 0;
        }

        .btn-search {
            width: 100%;
            font-size: 1rem;
            font-weight: 400;
            text-transform: capitalize;
            height: calc(4rem + 3px) !important;
            border-radius: 1%;
            background-color: var(--bs-body-color);
            color: white !important;
        }

        .btn-search:hover {

            background-color: rgb(21 106 191) !important;
            color: white !important;
        }

        @media (min-width: 992px) {
            .search-sec {
                position: relative;
                top: -114px;
                background: rgba(26, 70, 104, 0.51);
            }
        }

        @media (max-width: 992px) {
            .search-sec {
                background: #1A4668;
            }
        }

        .social-icons {
            margin: 20px 0;
        }

        .social-icons h4 {
            margin-bottom: 15px;
            font-size: 18px;
            color: #333;
        }

        .social-link {
            display: inline-block;
            margin: 0 10px;
            font-size: 24px;
            color: #555;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .social-link:hover {
            color: #007bff;
            /* Kolor na hover (np. niebieski) */
        }

        .promoEle {
            border: 1px solid #f8f9fa;
            background-color: white;

        }

        .promoEle:hover {
            background-color: #f8f9fa;
            border: none;
        }

        .katEle {
            border: 1px solid #f8f9fa;
            background-color: white;
            text-align: center;
            text-decoration: none;
            color: black;
        }

        .katEle:hover {
            background-color: #f8f9fa;
            border: none;
            text-decoration: none;
            color: black;
        }



        .container-custom {
            padding-left: 260px;
            padding-right: 260px;
        }

        .navbar-brand {
            margin-right: auto;
        }

        .btn-add-ad {
            margin-left: 10px;
        }

        .container-custom {
            padding-left: 320px;
            padding-right: 320px;
        }

        @media (max-width: 1900px) {
            .container-custom {
                padding-left: 300px;
                padding-right: 300px;
            }
        }

        @media (max-width: 1600px) {
            .container-custom {
                padding-left: 255px;
                padding-right: 255px;
            }
        }

        /* @media (max-width: 1300px) {
            .container-custom {
                padding-left: 180px;
                padding-right: 180px;
            }
        } */

        @media (max-width: 1200px) {
            .container-custom {
                padding-left: 50px;
                padding-right: 50px;
            }
        }

        @media (max-width: 576px) {
            .container-custom {
                padding-left: 20px;
                padding-right: 20px;
            }

            /* Wymuszenie odpowiedniego układu */
            .navbar-collapse {
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                justify-content: space-between;
                align-items: center;
                gap: 10px;
            }

            /* Linki w poziomie */
            .navbar-nav {
                flex-direction: row !important;
            }

            .dropdown-menu {
                position: absolute !important;
                /* Menu nie rozciąga nawigacji */
                top: 100%;
                left: 0;
                z-index: 1000;
            }

            .nav-item {
                margin-bottom: 0 !important;
            }

            /* Przycisk obok menu */

            .navbar {
                flex-wrap: nowrap !important;
            }
        }

        /* Globalne poprawki */
        .navbar-collapse {
            flex-grow: 0;
        }

        .btn-add-ad {
            flex-grow: 0;
        }

        .navbar-brand,
        #navbarDropdown,
        .btn-add-ad {
            font-size: 18px !important;

            font-weight: 400 !important;
            color: white !important;
        }

        .btn-add-ad {
                margin-left: 10px;
                background-color: #0b5ed7!important;
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

        .panel-title>small {
            font-size: .75em;
            color: #999999;
        }

        .panel-body *:first-child {
            margin-top: 0;
        }

        .panel-footer {
            border-top: 0;
        }

        .panel-default>.panel-heading {
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
</head>

<body>
    <nav style="margin-bottom: 0;" class="navbar navbar-expand-sm bg-dark">
        <div class="container-fluid container-custom">
            <!-- Logo -->
            <a class="navbar-brand" href="./index.php">Advertise Website</a>

            <!-- Menu -->
            <div class="" id="navbarSupportedContent">
            <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a style="text-decoration: none;" class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Your Account
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="./accountSett.php">Settings</a></li>
                            <li><a class="dropdown-item" href="./dashboard.php">My advertise</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="./logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <!-- Przycisk -->
            <a class="btn btn-primary btn-add-ad" href="./addAnnounView.php">Add Advertisement</a>
        </div>
    </nav>
    <!-- Main Layout -->
    <div style="min-height: 630px;" id="kontener" class="container-fluid bg-light">
        <div class="row">
            <!-- Left Sidebar (Empty) -->
            <div class="col-md-2 bg-light" id="sideBar1"></div>
            <main class="col-md-8 bg-light">
                <div class="container bootstrap snippets bootdeys my-4">
                    <div class="row">
                        <div class="col-xs-12 col-sm-9">
                            <div id="message" class="alert" style="display: none;"></div>
                            <form id="editAccountForm" method="POST" action="accountSett.php" class="form-horizontal">
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
                                                <input type="text" id="username" name="username" class="form-control" value="<?= $newUsername ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Email</label>
                                            <div class="col-sm-10">
                                                <input type="email" id="email" name="email" class="form-control" value="<?= $newEmail ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Current password</label>
                                            <div class="col-sm-10">
                                                <input type="password" id="oldPassword" name="oldPassword" class="form-control" placeholder="old password" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">New password</label>
                                            <div class="col-sm-10">
                                                <input type="password" id="newPassword" name="newPassword" class="form-control" placeholder="new password" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Repeat new pass</label>
                                            <div class="col-sm-10">
                                                <input type="password" id="repeatNewPassword" name="repeatNewPassword" class="form-control" placeholder="repeat new password" required>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-10 col-sm-offset-2">
                                                <button id="submit" type="button" class="btn btn-primary">Submit</button>
                                                <button id="reset" type="reset" class="btn btn-default">Reset</button>
                                                <button id="cancel" type="button" class="btn btn-default">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>

                            <!-- usuwanie konta -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Delete Account</h4>
                                </div>
                                <div class="panel-body">
                                    <div id="deleteMessage" class="alert" style="display: none;"></div>
                                    <form id="deleteAccountForm" class="form-horizontal">
                                        <div class="form-group">
                                            <label for="password" class="col-sm-2 control-label">Password</label>
                                            <div class="col-sm-10">
                                                <input type="password" id="deletePassword" name="deletePassword" class="form-control" placeholder="Enter your password" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="password" class="col-sm-2 control-label">Repeat password</label>
                                            <div class="col-sm-10">
                                                <input type="password" id="repeatDeletePassword" name="repeatDeletePassword" class="form-control" placeholder="Repeat your password" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-10 col-sm-offset-2 ">

                                                <button id="deleteAccount" type="button" class="btn btn-danger">Delete Account</button>
                                                <button id="cancel" type="button" class="btn btn-default">Cancel</button>

                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>



                        </div>
                    </div>
                </div>
            </main>



            <div class="col-md-2 bg-light" id="sideBar2"></div>
        </div>

    </div>




    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 mt-auto">
        &copy; 2024 Advertise Website
    </footer>

    <script>
        $(document).ready(function() {
            // Obsługa przesyłania formularza
            $("#submit").click(function(e) {
                e.preventDefault(); // Zapobiega domyślnej akcji formularza

                // Pobierz dane z formularza
                var username = $("#username").val();
                var email = $("#email").val();
                var oldPassword = $("#oldPassword").val();
                var newPassword = $("#newPassword").val();
                var repeatNewPassword = $("#repeatNewPassword").val();

                // Prosta walidacja po stronie klienta
                if (username === "" || email === "" || oldPassword === "" || newPassword === "" || repeatNewPassword === "") {
                    $("#message").text("Please complete all fields!").removeClass("alert-success").addClass("alert-danger").show();
                    return;
                }
                if (newPassword !== repeatNewPassword) {
                    $("#message").text("Password must match!").removeClass("alert-success").addClass("alert-danger").show();
                    return;
                }

                // Wyślij dane do serwera za pomocą AJAX
                $.ajax({
                    url: 'accountSett.php', // Ścieżka do pliku PHP
                    type: 'POST', // Metoda HTTP
                    dataType: 'json',
                    data: {
                        action: 'editAccount',
                        username: username,
                        email: email,
                        oldPassword: oldPassword,
                        newPassword: newPassword,
                        repeatNewPassword: repeatNewPassword
                    },
                    success: function(response) {
                        // Wyświetl komunikat zwrotny
                        if (response.success) {
                            $("#message").text(response.message).removeClass("alert-danger").addClass("alert-success").show();
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 3000);
                        } else {
                            $("#message").text(response.message).removeClass("alert-success").addClass("alert-danger").show();
                        }


                    },
                    error: function() {
                        $("#message").text("An error occurred while connecting to the server.").removeClass("alert-success").addClass("alert-danger").show();
                    }
                });
            });

            // Obsługa resetowania formularza
            $("#reset").click(function() {
                // Opcjonalne: zapytaj użytkownika, czy na pewno chce zresetować
                if (confirm("Are you sure you want to reset the form?")) {
                    $("#username").val("<?= $newUsername ?>"); // Przywróć stare dane
                    $("#email").val("<?= $newEmail ?>");
                    $("#oldPassword").val("");
                    $("#newPassword").val("");
                    $("#repeatNewPassword").val("");
                }
            });

            //obsluga anulwanie formularza
            $("#cancel").click(function(e) {
                e.preventDefault(); // Zapobiega domyślnej akcji formularza

                // Wyświetlenie okna potwierdzenia
                if (confirm("Are you sure you want to cancel the changes?")) {
                    // Jeśli użytkownik kliknie "OK", przekierowanie na dashboard
                    window.location.href = "dashboard.php";
                }
            });



            $("#deleteAccount").click(function() {
                if (!confirm("Czy na pewno chcesz usunąć swoje konto? Tego procesu nie można cofnąć!")) {
                    return;
                }

                var password = $("#deletePassword").val();
                var repeatPassword = $("#repeatDeletePassword").val();

                if (password === "" || repeatPassword === "") {
                    $("#deleteMessage").text("Passwords are required!!!").removeClass("alert-success").addClass("alert-danger").show();
                    return;
                }

                if (password !== repeatPassword) {
                    $("#deleteMessage").text("Passwords must match!!!").removeClass("alert-success").addClass("alert-danger").show();
                    return;
                }

                $.ajax({
                    url: 'accountSett.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'deleteAccount',
                        password: password
                    },
                    success: function(response) {
                        if (response.success) {
                            $("#deleteMessage").text(response.message).removeClass("alert-danger").addClass("alert-success").show();
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 3000);
                        } else {
                            $("#deleteMessage").text(response.message).removeClass("alert-success").addClass("alert-danger").show();

                        }
                    },
                    error: function() {
                        $("#deleteMessage").text("Server error!").removeClass("alert-success").addClass("alert-danger").show();

                    }
                });
            });



        });
    </script>

    <!-- Poprawiony skrypt Bootstrap 5 (bez jQuery) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src='https://netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>
</body>

</html>