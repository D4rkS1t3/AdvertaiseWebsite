<?php
session_start();
require 'db.php';

if (!isset($_SESSION['session_id'])) {
    header("Location: signin.php");
    exit();
}

$adId = $_GET['id'] ?? null;

if (!$adId) {
    die("Invalid ad ID");
}

$sessionID = $_SESSION['session_id'];

//pobranie danych o uztkowniku z danej sesji
try {
    $select = $db->prepare("SELECT id, username, email FROM users WHERE session_id = :session_id");
    $select->bindParam(':session_id', $sessionID);
    $select->execute();
    $userData = $select->fetch(PDO::FETCH_ASSOC);
    $userId = $userData['id'];
    $username = $userData['username'];
    $email = $userData['email'];
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching data.']);
    exit();
}

//pobranie kategorii z bazy
try {
    $query = $db->prepare("SELECT id, name FROM categories");
    $query->execute();
    $categories = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching categories.']);
    exit();
}

//pobranie informacji o ogloszeniu do edycji sprawdzenie czy nalezy do usera takze
try {
    $query = $db->prepare("SELECT * FROM ads WHERE id = :id AND user_id = :user_id AND active = 1");
    $query->bindParam(':id', $adId, PDO::PARAM_INT);
    $query->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $query->execute();
    $adData = $query->fetch(PDO::FETCH_ASSOC);

    if ($query->rowCount() <= 0) {
        echo json_encode(['success' => false, 'message' => "the announcement does not exist or belongs to another user"]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$title = $adData['title'];
$description = $adData['description'];
$price = $adData['price'];
$localization = $adData['localization'];
$categoryId = $adData['category_id'];
$imagePaths = explode(',', $adData['image_path']);
$phoneNumber = $adData['phone_number'] ?? '';



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
        .thumbnail img {
            border: 1px solid #ddd;
            padding: 5px;
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
        }




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
            background-color: #0b5ed7 !important;
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
            <!-- Main Content -->
            <main class="col-md-8 bg-light mt-2">
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
                                                <input type="text" id="title" name="title" class="form-control" placeholder="Iphone 11" value="<?= htmlspecialchars($title) ?>" required>
                                                <div id="titleError" class="text-danger" style="display: none; margin-top: 5px;"></div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Category*</label>
                                            <div class="col-sm-10">
                                                <select id="category" name="category" class="form-control" required>
                                                    <option value="">Choose category</option>
                                                    <?php foreach ($categories as $category): ?>
                                                        <option value="<?= htmlspecialchars($category['id']) ?>" <?= $category['id'] == $categoryId ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($category['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Exp. price:*</label>
                                            <div class="col-sm-10">
                                                <input type="number" id="price" name="price" class="form-control" placeholder="2000" value="<?= htmlspecialchars($price) ?>" required>
                                                <div id="priceError" class="text-danger" style="display: none; margin-top: 5px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- image -->
                                
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Existing Images</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <?php foreach ($imagePaths as $index => $imagePath): ?>
                                                <div class="col-md-3">
                                                    <div class="thumbnail">
                                                        <img src="./uploads/<?= htmlspecialchars(trim($imagePath)) ?>" style="width: 100px; height: 100px;" alt="Image <?= $index + 1 ?>">
                                                        <div class="caption text-center">
                                                            <button type="button" class="btn btn-danger btn-sm remove-image" data-index="<?= $index ?>">Remove</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Add New Images</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Upload Images:</label>
                                            <div class="col-sm-10">
                                                <input type="file" id="newImages" name="newImages[]" class="form-control" accept="image/*" multiple>
                                                <small>Selected images: <span id="newImageCount">0</span>/5</small>
                                                <div id="newImageError" class="text-danger" style="display: none; margin-top: 5px;"></div>
                                                <!-- Kontener na podgląd -->
                                                <div id="previewContainer" class="row mt-3"></div>
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
                                                    maxlength="9000" required><?= htmlspecialchars($description) ?></textarea>
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
                                                <input type="text" id="location" name="location" class="form-control" placeholder="Warsaw" value="<?= htmlspecialchars($localization) ?>" required>
                                                <div id="locationError" class="text-danger" style="display: none; margin-top: 5px;"></div>
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
                                                <input type="text" id="contactPerson" name="contactPerson" class="form-control" value="<?= htmlspecialchars($username) ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Email:*</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Phone number:</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="phoneNumber" name="phoneNumber" class="form-control" value="<?= htmlspecialchars($phoneNumber) ?>">
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



            <div class="col-md-2 bg-light" id="sideBar2"></div>
        </div>

    </div>




    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 mt-auto">
        &copy; 2024 Advertise Website
    </footer>

    <script>

    </script>

    <!-- Poprawiony skrypt Bootstrap 5 (bez jQuery) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src='https://netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>
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



            $('#title').on('input', function() {
                const title = $(this).val().trim();

                if (!title || title.length < 5) {
                    $('#titleError')
                        .text('Title must be at least 5 characters long')
                        .removeClass("text-success")
                        .addClass("text-danger")
                        .show();

                } else {
                    $('#titleError').hide();
                }
            });



            $('#location').on('input', function() {
                const location = $(this).val().trim();

                if (!location || location.length < 3) {
                    $('#locationError')
                        .text('Title must be at least 3 characters long')
                        .removeClass("text-success")
                        .addClass("text-danger")
                        .show();

                } else {
                    $('#locationError').hide();
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

            // Funkcja walidacji
            function validateForm() {
                let isValid = true;

                // Lista wymaganych pól z komunikatami błędów
                const requiredFields = [{
                        id: '#title',
                        errorId: '#titleError',
                        errorMessage: 'Title is required and must be at least 5 characters long.',
                        minLength: 5
                    },
                    {
                        id: '#category',
                        errorId: '#categoryError',
                        errorMessage: 'Please select a category.',
                        minLength: 1
                    },
                    {
                        id: '#price',
                        errorId: '#priceError',
                        errorMessage: 'Price is required and must be a positive number.',
                        minLength: 1
                    },
                    {
                        id: '#description',
                        errorId: '#descriptionError',
                        errorMessage: 'Description is too short. Add at least 40 characters.',
                        minLength: 40
                    },
                    {
                        id: '#location',
                        errorId: '#locationError',
                        errorMessage: 'Location is required and must be at least 3 characters long.',
                        minLength: 3
                    },
                ];

                // Przechodzimy przez każde pole i sprawdzamy walidację
                requiredFields.forEach(function(field) {
                    const value = $(field.id).val().trim();
                    const isNumberField = $(field.id).attr('type') === 'number';

                    if (!value || value.length < field.minLength || (isNumberField && parseFloat(value) <= 0)) {
                        isValid = false;
                        $(field.errorId).text(field.errorMessage).show();
                        $(field.id).addClass('is-invalid'); // Opcjonalnie dodaj klasę CSS dla błędów
                    } else {
                        $(field.errorId).hide();
                        $(field.id).removeClass('is-invalid'); // Usuń klasę CSS, jeśli pole jest poprawne
                    }
                });

                return isValid;
            }
            // Obsługa przesyłania formularza
            $("#submit").click(function(e) {
                e.preventDefault(); // Zapobiega domyślnej akcji formularza

                // Przewiń stronę na górę (opcjonalne)
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });

                // Wywołaj walidację formularza
                if (!validateForm()) {
                    $("#message")
                        .text("Please fill in all required fields correctly.")
                        .removeClass("alert-success")
                        .addClass("alert-danger")
                        .show();
                    return;
                }

                // Jeśli walidacja przejdzie, wysyłamy dane do serwera
                const formData = new FormData($('#adForm')[0]);

                $.ajax({
                    url: 'editAnnoun.php',
                    type: 'POST',
                    data: formData,
                    processData: false, // Wyłączamy przetwarzanie danych w URL
                    contentType: false, // FormData samodzielnie ustawia odpowiedni nagłówek
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
                        console.error('AJAX Error:', textStatus, errorThrown);
                        console.error('Response Text:', jqXHR.responseText);

                        $("#message")
                            .text("An error occurred while submitting the data. Check the console for more details.")
                            .removeClass("alert-success")
                            .addClass("alert-danger")
                            .show();
                    }
                });
            });









        });
    </script>
    <script>
$(document).ready(function () {
    const maxImages = 5; // Maksymalna liczba obrazów
    let uploadedFiles = []; // Tablica przechowująca pliki do wysłania

    // Funkcja aktualizująca licznik zdjęć
    function updateImageCount() {
        const existingImagesCount = document.querySelectorAll('.thumbnail img:not([hidden])').length;
        const totalImagesCount = existingImagesCount + uploadedFiles.length;
        $('#newImageCount').text(totalImagesCount);
    }

    $('#newImages').on('change', function (event) {
        const files = Array.from(event.target.files); // Przekształcenie FileList na tablicę
        const previewContainer = $('#previewContainer');
        const allowedExtensions = ['jpg', 'jpeg', 'png', 'svg'];

        // Resetuj błędy
        $('#newImageError').hide();

        // Sprawdź bieżącą liczbę obrazów w podglądzie
        const existingImagesCount = document.querySelectorAll('.thumbnail img:not([hidden])').length;

        // Oblicz dostępne sloty na nowe zdjęcia
        let remainingSlots = maxImages - existingImagesCount;

        if (remainingSlots <= 0) {
            $('#newImageError')
                .text(`You can't add more images. Maximum allowed is ${maxImages}.`)
                .addClass("text-danger")
                .show();
            return;
        }

        // Filtrowanie i walidacja wybranych plików
        const validFiles = files.filter((file) => {
            const ext = file.name.split('.').pop().toLowerCase();
            return allowedExtensions.includes(ext);
        });

        if (validFiles.length !== files.length) {
            $('#newImageError')
                .text('Some files have invalid extensions. Only jpg, jpeg, png, and svg are allowed.')
                .addClass("text-danger")
                .show();
            return;
        }

        // Jeśli liczba nowych obrazów przekracza dostępne sloty
        if (validFiles.length > remainingSlots) {
            $('#newImageError')
                .text(`You can upload a maximum of ${remainingSlots} more images.`)
                .addClass("text-danger")
                .show();
            return;
        }

        // Zastąp pliki w tablicy uploadedFiles nowymi plikami
        uploadedFiles = validFiles;

        // Wyczyść poprzedni podgląd
        previewContainer.empty();

        // Wyświetl podgląd nowych obrazów
        validFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewContainer.append(`
                    <div class="col-md-3">
                        <div class="thumbnail">
                            <img src="${e.target.result}" alt="Preview" class="img-fluid rounded" style="width: 100px; height: 100px; object-fit: cover;">
                            <div class="caption text-center">
                                <button type="button" class="btn btn-danger btn-sm remove-image" data-index="${index}">Remove</button>
                            </div>
                        </div>
                    </div>
                `);
            };
            reader.readAsDataURL(file); // Odczyt pliku jako URL
        });

        // Aktualizuj licznik zdjęć
        updateImageCount();

        // Zresetuj input file, aby użytkownik mógł dodać te same pliki ponownie
        this.value = '';
    });

    // Obsługa usuwania obrazów z podglądu
    $(document).on('click', '.remove-image', function () {
        const imageIndex = $(this).data('index');

        // Usuń obraz z tablicy uploadedFiles i podglądu
        uploadedFiles.splice(imageIndex, 1);
        $(this).closest('.col-md-3').remove();

        // Aktualizuj licznik zdjęć
        updateImageCount();
    });

    // Początkowa aktualizacja licznika zdjęć
    updateImageCount();
});







    </script>
</body>

</html>