<?php
session_start();
require 'db.php';

if (!isset($_SESSION['session_id'])) {
    header("Location: signin.php");
    exit();
}

$sessionID = $_SESSION['session_id'];


try {
    $query = $db->prepare("SELECT id, name FROM categories");
    $query->execute();
    $categories = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Błąd podczas pobierania kategorii: " . $e->getMessage();
    exit();
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

    body {
        margin-top: 20px;
        background: #f5f5f5;
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

    @media (min-width: 768px) {
        .col-sm-9 {
            width: 69% !important;
        }
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
                <div class="container bootstrap snippets bootdeys">
                    <div class="row">
                        <div class="col-xs-12 col-sm-9">
                            <h3>Add announcement</h3>
                            <div id="message" class="alert" style="display: none;"></div>

                            <form id="editAccountForm" method="POST" enctype="multipart/form-data" action="accountSett.php" class="form-horizontal">
                                <!-- tytul kat -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <br>
                                        <h3 class="panel-title">The more details the better!</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Title of the ad:</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="title" name="title" class="form-control" placeholder="Iphone 11" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Category</label>
                                            <div class="col-sm-10">
                                                <select id="category" name="category" class="form-control" required>
                                                    <option value="">Choose category</option>
                                                    <?php foreach ($categories as $category): ?>
                                                        <option value="<?= htmlspecialchars($category['id']) ?>">
                                                            <?= htmlspecialchars($category['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- image -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <br>
                                        <h3 class="panel-title">Images</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Upload Images:</label>
                                            <div class="col-sm-10">
                                                <input type="file" id="images" name="images[]" class="form-control" accept="image/*" multiple required>
                                                <small>You can add max 5 photos.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- opis -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <br>
                                        <h3 class="panel-title">Description</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label for="description" class="col-sm-2 control-label">Opis</label>
                                            <div class="col-sm-10">
                                                <textarea id="description" name="description" class="form-control" rows="6"
                                                    placeholder="Enter any information that would be important to you when viewing such an advertisement."
                                                    maxlength="9000" required></textarea>
                                                <small id="descriptionHelp" class="form-text text-muted">
                                                    Please enter at least 40 characters. <span id="charCount">0</span>/9000
                                                </small>
                                                <div id="descriptionError" class="text-danger" style="display: none;">
                                                    Description is too short. Add more details.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- lokalizacja -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <br>
                                        <h3 class="panel-title">Location</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">location:</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="location" name="location" class="form-control" placeholder="Warsaw" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- osoba,email,tel -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <br>
                                        <h3 class="panel-title">Contact details</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Contact person:</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="contactPerson" name="contactPerson" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Email:</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="email" name="email" class="form-control" value="example@example.com" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Phone number:</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="phoneNumber" name="phoneNumber" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- przyciski -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <div  class="col-sm-10  col-sm-offset-2 text-right">
                                                <button id="cancel" type="button" class="btn btn-default">Cancel</button>
                                                <button id="submit" type="button" class="btn btn-primary">Add an advertisement</button>
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




        document.addEventListener('DOMContentLoaded', function() {
            const descriptionInput = document.getElementById('description');
            const charCount = document.getElementById('charCount');
            const descriptionError = document.getElementById('descriptionError');

            // Aktualizacja licznika znaków
            descriptionInput.addEventListener('input', function() {
                const length = descriptionInput.value.length;
                charCount.textContent = length;

                // Sprawdź, czy opis ma co najmniej 40 znaków
                if (length < 40) {
                    descriptionError.style.display = 'block';
                } else {
                    descriptionError.style.display = 'none';
                }
            });

            // Walidacja przed przesłaniem formularza
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                if (descriptionInput.value.length < 40) {
                    e.preventDefault();
                    descriptionError.style.display = 'block';
                    alert('Opis musi zawierać przynajmniej 40 znaków.');
                }
            });
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