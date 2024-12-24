<?php
session_start();
require 'db.php';

//pobranie kategorii
try {
    $query = $db->prepare("SELECT id, name FROM categories");
    $query->execute();
    $categories = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching categories.']);
    exit();
}
//losowe ogloszenia do promoted ads
try {
    // Pobranie wszystkich aktywnych ogłoszeń
    $query = $db->prepare("SELECT id FROM ads WHERE active = 1");
    $query->execute();
    $allIds = $query->fetchAll(PDO::FETCH_COLUMN);

    if (empty($allIds)) {
        // Jeśli brak ogłoszeń, zwróć pustą tablicę
        $randomAds = [];
    } else {
        // Jeśli jest mniej niż 8 ogłoszeń, losuj tyle, ile jest
        $count = min(8, count($allIds));
        $randomIds = array_rand(array_flip($allIds), $count);
        
        if (!is_array($randomIds)) {
            $randomIds = [$randomIds]; // Gdy tylko jedno ogłoszenie
        }

        $ids = implode(',', $randomIds);

        // Pobranie ogłoszeń na podstawie wylosowanych ID
        $query = $db->prepare("SELECT * FROM ads WHERE id IN ($ids)");
        $query->execute();
        $randomAds = $query->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching random ads.']);
    exit();
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
    <div style="min-height: 630px;" id="kontener" class="container-fluid bg-light">
        <div class="row">
            <!-- Left Sidebar (Empty) -->
            <div class="col-md-2 bg-light" id="sideBar1"></div>
            <main class="col-md-8 bg-light">
                <!-- wyszukiwanie -->
                <div style="padding: 30px 0;" class="col-md-12 bg-light">
                    <form id="searchForm" action="./oferts.php" method="GET">
                        <div class=" justify-content-center align-items-center">
                            <div class="row">
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

                        </div>
                    </form>
                </div>
                <!-- kategorie -->

                <div class="col-md-12 bg-light">

                    <h2 style="text-align: center;">Main categories</h2>
                    <div class="mt-5">
                        <div class="row">
                            <?php foreach ($categories as $category): ?>
                                <a class="col-4 col-sm-4 col-lg-2 mb-3 katEle" href="oferts.php?category=<?= htmlspecialchars($category['id']) ?>">
                                    <img style="width: 70px;" src="./img/kategorie/<?= strtolower($category['name']) ?>.png" alt="<?= htmlspecialchars($category['name']) ?>">
                                    <p><?= htmlspecialchars($category['name']) ?></p>
                                </a>
                            <?php endforeach; ?>



                        </div>
                    </div>
                </div>

                <!-- promowane -->

                <div class="col-md-12 bg-light">

                    <h2 style="margin:5% 0%;text-align: center;">Promoted Ads</h2>
                    <div class="mt-5">
                        <div class="row">


                            <!-- pierwszy ele -->
                            <?php foreach ($randomAds as $ad): ?>
                                <a class="promoEle col-6 col-sm-6 col-md-4 col-lg-3 my-1" href="ofert.php?adId=<?= htmlspecialchars($ad['id']) ?>" style="text-decoration: none; color:black;">
                                    <div class="d-flex justify-content-center">
                                        <?php
                                        if (!empty($ad['image_path'])) {
                                            $imagePaths = explode(',', $ad['image_path']);
                                            $firstImage = $imagePaths[0];
                                            // Przyjmijmy, że mamy różne rozmiary obrazów
                                            $baseImagePath = './uploads/';
                                        } else {
                                            $baseImagePath = './uploads/';
                                            $firstImage = 'noImage.jpg';
                                        }
                                        ?>
                                        <img
                                            src="<?= $baseImagePath ?>thumb_300x200/<?= htmlspecialchars($firstImage) ?>"
                                            srcset="
                <?= $baseImagePath ?>thumb_150x113/<?= htmlspecialchars($firstImage) ?> 150w, 
                <?= $baseImagePath ?>thumb_300x200/<?= htmlspecialchars($firstImage) ?> 300w, 
                <?= $baseImagePath ?>thumb_600x400/<?= htmlspecialchars($firstImage) ?> 600w, 
            "
                                            sizes="(max-width: 768px) 150px, (max-width: 1024px) 300px, 600px"
                                            alt="<?= htmlspecialchars($ad['title']) ?>"
                                            class="img-fluid">
                                    </div>
                                    <div>
                                        <div class="m-4">
                                            <p style="font-size:13px;height:30px; word-wrap: break-word; white-space: normal;">
                                                <?= htmlspecialchars(strlen($ad['title']) > 50) ? substr($ad['title'], 0, 50) . '...' : $ad['title']  ?>
                                            </p>
                                            <br>
                                            <p><b><?= number_format($ad['price'], 2) ?> zł</b></p>
                                        </div>
                                        <div class="m-4">
                                            <small style="font-size: 10px;"><?= htmlspecialchars(ucfirst($ad['localization'])) ?></small><br>
                                            <small style="font-size: 10px;">Odświeżono dnia <?= date('d F Y', strtotime($ad['updated_at'])) ?></small>
                                        </div>
                                    </div>
                                </a>

                            <?php endforeach; ?>


                        </div>
                    </div>
                </div>

            </main>
            <div class="col-md-2 bg-light" id="sideBar2"></div>
        </div>

    </div>
 <!-- promocja -->

 <div style="background-color: #cbf7ee; min-height: 200px;" class="promo">
        <div class="container">
            <div class="row">
                <div class="col-12 mt-5" style="text-align: center; align-items: center;font-size:12px;">
                    <div class="col-12 mb-5"><img style="height: 50px; width:200px;" src="./img/logo.PNG" alt=""></div>
                    <div class="col-12 my-5">
                        <span>
                            advertise.pl is a free local classified ad in the following categories: Fashion, Animals, For Children, Sports and Hobbies, Music and Education.
                            You will quickly find interesting ads here and easily contact the advertiser. Office work, apartments, rooms, cars are waiting for you on advertise.pl.
                            If you want to sell something - you can easily add free ads. If you want to buy something - you will find interesting opportunities here, cheaper than in a store. Sell in the neighborhood on advertise.pl
                        </span>
                    </div>
                    <div class="social-icons my-5 col-12 text-center">
                        <h4>Join us</h4>
                        <a href="https://facebook.com" target="_blank" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://twitter.com" target="_blank" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="https://instagram.com" target="_blank" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="https://linkedin.com" target="_blank" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kategorie i popularne wyszukiwania -->
    <div class="bg-light py-5" style="min-height: 200px;" class="popular">
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
</body>

</html>