<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['adId'])) {
    echo json_encode(['success' => false, 'message' => 'Error fetching ad data.']);
    exit();
}

$adId = trim($_GET['adId']);

// Fetch ad data
try {
    $sql = $db->prepare("SELECT * FROM ads WHERE id = :adId");
    $sql->bindParam(':adId', $adId);
    $sql->execute();
    $adData = $sql->fetch(PDO::FETCH_ASSOC);
    //userData
    $select = $db->prepare("SELECT * FROM users WHERE id = :userId");
    $select->bindParam(':userId', $adData['user_id']);
    $select->execute();
    $userData = $select->fetch(PDO::FETCH_ASSOC);

    if (!$adData) {
        echo json_encode(['success' => false, 'message' => 'Advertisement not found.']);
        exit();
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
    exit();
}

$condition = 'New';
if ($adData['condit'] == 1) {
    $condition = 'Used';
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
                margin-bottom: 0;
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
            .btn-add-ad {
                margin-left: 10px;
            }

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
            ;
            font-weight: 400 !important;
            color: white !important;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-sm bg-dark">
        <div class="container-fluid container-custom">
            <!-- Logo -->
            <a class="navbar-brand" href="./index.php">Advertise Website</a>

            <!-- Menu -->
            <div class="collapse navbar-collapse justify-content-center" id="navbarSupportedContent">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
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
    <div style="min-height: 100vh;" id="kontener" class="container-fluid bg-light">
        <div class="row h-100">
            <!-- Left Sidebar (Empty) -->
            <div class="col-md-2 bg-light" id="sideBar1"></div>

            <!-- Main Content -->
            <main class="col-md-8 bg-light">
                <!-- Search Section -->
                <div style="padding: 30px 20px;" class="col-12">
                    <form id="searchForm" action="./oferts.php" method="GET">
                        <div class="row justify-content-center align-items-center">
                            <div class="col-lg-8 col-md-8 col-sm-8 p-0">
                                <input type="text" class="form-control search-slt" id="searchQuery" name="search" placeholder="Find something for you!">
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 p-0">
                                <input type="text" class="form-control search-slt" id="locationQuery" name="location" placeholder="Location">
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 p-0">
                                <button type="submit" class="btn btn-light btn-search" id="searchButton">Search</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Advertisements Section -->
                <div class="container-fluid">
                    <div class="row">
                        <!-- Lewa Kolumna -->
                        <div class="col-lg-8">
                            <div class="d-flex flex-column gap-3">
                                <!-- Sekcja Obrazka -->
                                <div class="bg-white p-3" style="border-radius: 5px;">
                                    <?php
                                    if (!empty($adData['image_path'])) {
                                        $imagePaths = explode(',', $adData['image_path']);
                                        $baseImagePath = './uploads/';
                                    } else {
                                        $baseImagePath = './uploads/';
                                        $imagePaths = ['noImage.jpg']; // Domyślny obraz, jeśli brak zdjęć
                                    }
                                    ?>

                                    <!-- Bootstrap Carousel -->
                                    <div id="adImagesCarousel" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-inner">
                                            <?php foreach ($imagePaths as $index => $image): ?>
                                                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                                    <img src="<?= $baseImagePath . trim($image) ?>" class="d-block w-100" alt="Ad Image">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <!-- Carousel Controls -->
                                        <button class="carousel-control-prev" type="button" data-bs-target="#adImagesCarousel" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#adImagesCarousel" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </button>
                                    </div>
                                </div>


                                <!-- Sekcja Informacji i Opisu -->
                                <div class="bg-white p-3" style="border-radius: 5px;">
                                    <!-- Przyciski -->
                                    <div class="d-flex mb-3">
                                        <button class="btn btn-outline-dark me-2" style="border-radius: 20px; font-size: 12px;">Business</button>
                                        <button class="btn btn-outline-dark" style="border-radius: 20px; font-size: 12px;">Condition: <?= $condition ?></button>
                                    </div>

                                    <!-- Nagłówek -->
                                    <h4 class="fw-bold mb-3">Description</h4>

                                    <!-- Opis Tekstu -->
                                    <p style="font-size: 14px; color: #333;">
                                        <!-- Treść pobrana z bazy danych -->
                                        <?= $adData['description'] ?>
                                    </p>
                                </div>

                                <!-- Sekcja Dodatkowa -->
                                <div class="bg-white p-3 mb-3" style="border-radius: 5px;">
                                    <h6 class="text-uppercase mb-3" style="font-weight: bold; font-size: 14px; color: #333;">Contact us</h6>

                                    <div class="d-flex align-items-center mb-3">
                                        <!-- Awatar i informacje -->
                                        <div class="me-3">
                                            <img src="./img/account-icon-template-vector.jpg" alt="Awatar" style="width: 50px; height: 50px; border-radius: 50%; background-color: #ccc;">
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-bold" style="font-size: 14px;"><?= $userData['username'] ?></p>
                                            <p class="mb-0 text-muted" style="font-size: 12px;">No rating yet</p>
                                        </div>
                                    </div>

                                    <!-- Weryfikacja informacji -->
                                    <p class="text-muted mb-3" style="font-size: 12px;">
                                        All ratings are verified. Only people who bought the item with Przesyłka OLX can leave them.
                                    </p>

                                    <!-- Przyciski akcji -->
                                    <div id="contact" class="d-flex align-items-center justify-content-between">
                                        <!-- Przycisk Telefonu -->
                                        <div id="contact" class="d-flex align-items-center">
                                            <button class="btn btn-outline-dark me-2" style="border-radius: 50%; padding: 10px; width: 40px; height: 40px;">
                                                <i class="fas fa-phone" style="font-size: 16px;"></i>
                                            </button>
                                            <div class="mx-2">
                                                <p id="phone-number" class="mb-0 fw-bold" style="font-size: 14px;">xxx xxx xxx</p>
                                            </div>
                                            <!-- Przycisk Pokaż -->
                                            <button id="phone-btn" class="btn btn-outline-dark" style="border-radius: 5px; font-size: 12px; padding: 5px 10px;">Pokaż</button>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Prawa Kolumna -->
                        <div class="col-lg-4">
                            <div class="d-flex flex-column gap-3">
                                <!-- Sekcja Podstawowa -->
                                <div class="bg-white p-3" style="border-radius: 5px;">
                                    <!-- Data Dodania -->
                                    <p class="text-muted mb-2" style="font-size: 12px;">Added: <?= date('d F Y', strtotime($adData['created_at'])) ?></p>
                                    <!-- Tytuł -->
                                    <p class="fw-bold mb-2" style="font-size: 16px; line-height: 1.4;">
                                        <?= $adData['title'] ?>
                                    </p>
                                    <!-- Cena -->
                                    <p class="fw-bold mb-3" style="font-size: 20px; color: #333;"><?= $adData['price'] ?>zł</p>
                                    <!-- Przyciski Akcji -->
                                    <!-- <button class="btn btn-dark w-100 mb-2" style="border-radius: 5px;">Wyślij wiadomość</button> -->
                                    <a href="#contact" class="btn btn-outline-dark w-100" style="border-radius: 5px;">Call</a>
                                </div>


                                <!-- Sekcja Przedsiębiorcy -->
                                <div class="bg-white p-3" style="border-radius: 5px;">
                                    <h6 class="mb-3 text-uppercase" style="font-weight: bold; font-size: 14px; color: #333;">Businessperson</h6>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <img src="./img/account-icon-template-vector.jpg" class="rounded-circle me-3" style="width: 50px; height: 50px;" alt="Avatar">
                                        </div>
                                        <div>
                                            <p class="mb-0" style="font-weight: bold;"><?= $userData['username'] ?></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Sekcja Lokalizacji -->
                                <div class="bg-white p-3" style="border-radius: 5px;">
                                    <h6 class="mb-3 text-uppercase" style="font-weight: bold; font-size: 14px; color: #333;">Localization</h6>
                                    <div class="d-flex align-items-center">
                                        <!-- Ikona Lokalizacji i Opis -->
                                        <div class="me-3">
                                            <p class="mb-0" style="font-size: 14px; font-weight: bold; color: #333;"><?= $adData['localization'] ?></p>
                                        </div>
                                        <!-- Obrazek Mapy -->
                                        <div class="ms-auto">
                                            <img src="./img/staticMap.svg" class="img-fluid" style="width: 120px; height: auto; border-radius: 5px;" alt="Mapa">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



            </main>

            <!-- Right Sidebar -->
            <div class="col-md-2 bg-light" id="sideBar2"></div>
        </div>
    </div>





    <!-- Kategorie i popularne wyszukiwania -->
    <div class="py-5" style="background-color: #cbf7ee;min-height: 200px;" class="popular">
        <div class="container ">
            <div class="row text-center">
                <!-- Pierwsza kolumna -->
                <div class="col-6">
                    <div class="mb-3" style="align-items: center;"><img style="height:100px; width:100px" src="./img/1.svg" alt=""></div>
                    <div class="m-4" style="font-size:12px;">
                        <span>
                            Main Categories: Antiques & CollectiblesBusiness & IndustryAutomotiveReal EstateWorkHome & GardenElectronicsFashionFarmingAnimalsFor KidsSports & HobbiesMusic & EducationHealth & BeautyServicesAccommodationRentalI'll Give Away for Free </span>
                    </div>
                </div>

                <!-- Druga kolumna -->
                <div class="col-6">
                    <div class="mb-3" style="align-items: center;"><img style="height:100px; width:100px" src="./img/2.svg" alt=""></div>
                    <div class="m-4" style="font-size:12px;"><span>
                            Popular searches: passenger carsrenault capturused carspassenger carskia ceedhyundai i20hyundai i30hyundai ix35bmw x1ford kugavw tiguankia sportagepeugeot 3008hyundai tucsonnissan qashqaitoyota corollatoyota aurisnissan jukeaudi q5volvo xc60 </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 mt-auto">
        &copy; 2024 Advertise Website
    </footer>



    <!-- Poprawiony skrypt Bootstrap 5 (bez jQuery) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Pobieramy elementy
            const phoneButton = document.getElementById('phone-btn');
            const phoneNumberElement = document.getElementById('phone-number');

            // Pobieramy numer telefonu z PHP
            const phoneNumber = "<?= isset($adData['phone_number']) ? $adData['phone_number'] : ''; ?>";

            // Sprawdzamy, czy numer telefonu istnieje
            if (phoneNumber === '') {
                // Ukrywamy przycisk, jeśli numer telefonu nie istnieje
                phoneButton.style.display = 'none';
                return;
            }

            // Funkcja do formatowania numeru telefonu
            function formatPhoneNumber(number) {
                return number.replace(/(\d{3})(?=\d)/g, '$1 '); // Dodaje spację co 3 cyfry
            }
            // Dodajemy nasłuchiwanie na kliknięcie
            phoneButton.addEventListener('click', function() {
                phoneNumberElement.textContent = formatPhoneNumber(phoneNumber); // Wyświetlamy numer telefonu
                phoneButton.style.display = 'none'; // Ukrywamy przycisk
            });
        });
    </script>
</body>

</html>