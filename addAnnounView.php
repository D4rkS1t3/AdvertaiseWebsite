<?php
session_start();
require 'db.php';
require 'resizeImage.php';

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
    echo json_encode(['success' => false, 'message' => 'Error fetching categories.']);
    exit();
}

try {
    $select = $db->prepare("SELECT id, email, username FROM users WHERE session_id = :session_id");
    $select->bindParam(':session_id', $sessionID);

    if ($select->execute()) {

        $data = $select->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $email = $data['email'];
            $userId = $data['id'];
            $username = $data['username'];
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No user found for the given session ID!"
            ]);
            exit();
        }
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Error executing the query!"
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
                            <!-- Wyświetlanie błędów -->
                            <form id="adForm" method="POST" enctype="multipart/form-data" action="addAnnoun.php" class="form-horizontal">

                                <div id="message" class="alert" style="display: none; margin-bottom: 15px;"></div>

                                <!-- tytul, kat, cena -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <br>
                                        <h3 class="panel-title">The more details the better!</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Title of the ad:*</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="title" name="title" class="form-control" placeholder="Iphone 11" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Category*</label>
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
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Exp. price:*</label>
                                            <div class="col-sm-10">
                                                <input type="number" id="price" name="price" class="form-control" placeholder="2000" required>
                                                <div id="priceError" class="text-danger" style="display: none; margin-top: 5px;"></div>
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
                                                <small>Selected images: <span id="imageCount">0</span>/5</small>
                                                <div id="imageError" class="text-danger" style="display: none; margin-top: 5px;"></div>
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
                                            <label for="description" class="col-sm-2 control-label">Description:*</label>
                                            <div class="col-sm-10">
                                                <textarea id="description" name="description" class="form-control" rows="6"
                                                    placeholder="Enter any information that would be important to you when viewing such an advertisement."
                                                    maxlength="9000" required></textarea>
                                                <small>
                                                    Please enter at least 40 characters. <span id="charCount">0</span>/9000
                                                </small>
                                                <div id="descriptionError" class="text-danger" style="display: none;">
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
                                            <label class="col-sm-2 control-label">location:*</label>
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
                                        <h3 class="panel-title">Contact details </h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Contact:*</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="contactPerson" name="contactPerson" class="form-control" value="<?= $username ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Email:*</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="email" name="email" class="form-control" value="<?= $email ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Phone number:</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="phoneNumber" name="phoneNumber" class="form-control">
                                                <div id="phoneError" class="text-danger" style="display: none; margin-top: 5px;"></div>
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
                                            <div class="col-sm-10  col-sm-offset-2 text-right">
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
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
    <script>
        $(document).ready(function() {

            //obsluga licznika opisu i bledu gdy jest nie poprawnej dlugosci
            $('#description').on('input', function() {
                const maxlength = 9000;
                const currentLength = $(this).val().length;
                $('#charCount').text(currentLength);

                if (currentLength < 40) {
                    $('#descriptionError')
                        .text("Description is too short. Add at least 40 characters.")
                        .removeClass("text-success")
                        .addClass("text-danger")
                        .show();
                } else if (currentLength > maxlength) {
                    $('#descriptionError')
                        .text("Description exceeds the maximum allowed length of 9000 characters.")
                        .removeClass("text-success")
                        .addClass("text-danger")
                        .show();
                } else {
                    $('#descriptionError')
                        .text("Description length is valid.")
                        .removeClass("text-danger")
                        .addClass("text-success")
                        .show();
                }
            });


            // Obsługa przesyłania formularza
            $("#submit").click(function(e) {
                e.preventDefault(); // Zapobiega domyślnej akcji formularza

                // Przewiń stronę na samą górę
                window.scrollTo({ top: 0, behavior: 'smooth' });
                
                // Pobierz dane z formularza
                const formData = new FormData($('#adForm')[0]);
                const descriptionLength = $('#description').val().length;


                if (descriptionLength < 40 || descriptionLength > 9000) {
                    $('#descriptionError')
                        .text(descriptionLength < 40 ?
                            "Description is too short. Add at least 40 characters." :
                            "Description exceeds the maximum allowed length of 9000 characters.")
                        .removeClass("text-success")
                        .addClass("text-danger")
                        .show();
                    return;
                }

                //wysylanie danych do serwera

                $.ajax({
                    url: 'addAnnoun.php',
                    type: 'POST',
                    data: formData,
                    processData: false, //wysylamy plik wiec wylaczamy przetwarzania do url zapytania
                    contentType: false, //form data uswai sam odpowiedni naglowek w http
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $("#message")
                                .text(response.message)
                                .removeClass("alert-danger")
                                .addClass("alert-success")
                                .show();
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 3000);
                        } else {
                            $("#message")
                                .text(response.message)
                                .removeClass("alert-success")
                                .addClass("alert-danger")
                                .show();
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // Logowanie szczegółów błędu do konsoli
                        console.error('AJAX Error:', textStatus, errorThrown);
                        console.error('Response Text:', jqXHR.responseText);

                        // Wyświetlenie błędu w interfejsie użytkownika
                        $("#message")
                            .text("An error occurred while submitting the data. Check the console for more details.")
                            .removeClass("alert-success")
                            .addClass("alert-danger")
                            .show();
                    }
                });
            });


            $('#phoneNumber').on('input', function() {
                const phone = $(this).val();
                const isValid = /^\+?[0-9]{9,15}$/.test(phone);

                if (!isValid && phone !== '') {
                    $('#phoneError')
                        .text('Invalid phone number. Use 9-15 digits only.')
                        .removeClass("text-success")
                        .addClass("text-danger")
                        .show();

                } else {
                    $('#phoneError').hide();
                }
            });



            //liczenie liczby dodanych zdjec
            $('#images').on('change', function() {
                const files = this.files;
                const maxFiles = 5;
                const allowedExtensions = ['jpg', 'jpeg', 'png', 'svg'];
                let valid = true;

                Array.from(files).forEach(file => {
                    const ext = file.name.split('.').pop().toLowerCase();
                    if (!allowedExtensions.includes(ext)) {
                        valid = false;
                    }
                });

                if (!valid) {
                    $('#imageError')
                        .text('Only jpg, jpeg, png, and svg files are allowed.')
                        .removeClass("text-success")
                        .addClass("text-danger")
                        .show();
                    this.value = '';
                } else if (files.length > maxFiles) {
                    $('#imageError')
                        .text(`You can upload a maximum of ${maxFiles} images.`)
                        .removeClass("text-success")
                        .addClass("text-danger")
                        .show();
                    this.value = ''; // Reset wyboru plików
                    $('#imageCount').text('0');
                } else {
                    $('#imageError').hide();
                    $('#imageCount').text(files.length);
                }
            });


            $('#price').on('input', function() {
                const price = parseFloat($(this).val());
                if (isNaN(price) || price < 0) {
                    $('#priceError')
                        .text('Price must be a positive number.')
                        .removeClass("text-success")
                        .addClass("text-danger")
                        .show();
                } else {
                    $('#priceError').hide();
                }
            });

            //obsluga anulwanie formularza
            $("#cancel").click(function(e) {
                e.preventDefault(); // Zapobiega domyślnej akcji formularza

                // Wyświetlenie okna potwierdzenia
                if (confirm("Are you sure you want to cancel adding an advertisement?")) {
                    // Jeśli użytkownik kliknie "OK", przekierowanie na dashboard
                    window.location.href = "dashboard.php";
                }
            });





        });
    </script>

    <!-- Bootstrap core JavaScript-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap (jeśli potrzebne interaktywne komponenty) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>


</body>

</html>