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

//pobieramy parametry z get
$search = isset($_GET['search']) ? trim($_GET['search']) : null;
$location = isset($_GET['location']) ? trim($_GET['location']) : null;
$category = isset($_GET['category']) ? trim($_GET['category']) : null;
$condition = isset($_GET['condition']) ? trim($_GET['condition']) : null;
$priceFrom = isset($_GET['priceFrom']) ? trim($_GET['priceFrom']) : null;
$priceTo = isset($_GET['priceTo']) ? trim($_GET['priceTo']) : null;

//przygotowujemy zapytanie sql
$sql = "SELECT * FROM ads WHERE active = 1";
$params = [];

//warunki na podstawie filtrow
if (!empty($search)) {
    $sql .= " AND (title LIKE :search OR description LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

if (!empty($location)) {
    $sql .= " AND localization LIKE :location";
    $params[':location'] = '%' . $location . '%';
}

if (!empty($category)) {
    $sql .= " AND category_id = :category";
    $params[':category'] = $category;
}

if ($condition !== null) {
    $sql .= " AND condit = :condition";
    $params[':condition'] = $condition;
}

if ($priceFrom !== null && $priceFrom !== '') {
    $sql .= " AND price >= :priceFrom";
    $params[':priceFrom'] = $priceFrom;
}

if ($priceTo !== null && $priceTo !== '') {
    $sql .= " AND price <= :priceTo";
    $params[':priceTo'] = $priceTo;
}

//sortowanie
$sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'price_asc';
switch ($sortOrder) {
    case 'price_desc':
        $sql .= " ORDER BY price DESC";
        break;
    case 'date_desc':
        $sql .= " ORDER BY updated_at DESC";
        break;
    case 'date_asc':
        $sql .= " ORDER BY updated_at ASC";
        break;
    default:
        $sql .= " ORDER BY price ASC";
        break;
}


// Wykonaj zapytanie
try {
    $query = $db->prepare($sql);
    $query->execute($params);
    $adsToDisplay = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching ads: ' . $e->getMessage()]);
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

        .form-label-placeholder {
            height: 1.5rem;
            /* Wysokość podobna do domyślnej wysokości etykiety Bootstrap */
            margin-bottom: 0.5rem;
            /* Odstęp między "etykietą" a przyciskiem */
            display: block;
        }

        .miniKat {
            font-size: 13px;
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
                <!-- wyszukiwanie + filter -->
                <div style="padding: 20px 0;" class="col-md-12 bg-light">
                    <form id="searchForm" action="./oferts.php" method="GET">
                        <div class=" justify-content-center align-items-center mx-3 my-2">
                            <div class="row">
                                <div class="col-lg-8 col-md-8 col-sm-8 p-0">
                                    <input type="text" class="form-control search-slt" id="searchQuery" value="<?= htmlspecialchars($search) ?>" name="search" placeholder="Find something for you!">
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 p-0">
                                    <input type="text" class="form-control search-slt" value="<?= htmlspecialchars($location) ?>" id="locationQuery" name="location" placeholder="Location">
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 p-0">
                                    <button type="submit" class="btn btn-light btn-search" id="searchButton">Search</button>
                                </div>
                            </div>

                        </div>
                        <!-- filtry -->
                        <div class="my-4">
                            <div class="filter-section">
                                <h5>Filters</h5>
                                <div class="row g-3 align-items-center">
                                    <!-- Kategoria -->
                                    <div class="col-md-3">
                                        <label for="category" class="form-label miniKat">Category</label>
                                        <select id="category" name="category" class="form-control">
                                            <option value="">Choose category</option>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?= htmlspecialchars($cat['id']) ?>" <?= $cat['id'] == $category ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($cat['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <!-- Stan -->
                                    <div class="col-md-2">
                                        <label for="condition" class="form-label miniKat">Condition</label>
                                        <select class="form-select" id="condition" name="condition">
                                            <option value="" <?= $condition === null ? 'selected' : '' ?>>All</option>
                                            <option value="1" <?= $condition == '1' ? 'selected' : '' ?>>New</option>
                                            <option value="2" <?= $condition == '2' ? 'selected' : '' ?>>Used</option>
                                        </select>
                                    </div>
                                    <!-- Cena -->
                                    <div class="col-md-2">
                                        <label class="form-label miniKat">Price</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" value="<?= htmlspecialchars($priceFrom) ?>" placeholder="From" id="priceFrom" name="priceFrom">
                                            <input type="number" class="form-control" value="<?= htmlspecialchars($priceTo) ?>" placeholder="To" id="priceTo" name="priceTo">
                                        </div>
                                    </div>
                                    <!-- sortowanie -->
                                    <div class="col-md-3">
                                        <label for="sortOrder" class="form-label miniKat">Sort</label>
                                        <select name="sortOrder" class="form-select">
                                            <option value="price_asc" <?= $sortOrder == 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                                            <option value="price_desc" <?= $sortOrder == 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                                            <option value="date_desc" <?= $sortOrder == 'date_desc' ? 'selected' : '' ?>>Newest</option>
                                            <option value="date_asc" <?= $sortOrder == 'date_asc' ? 'selected' : '' ?>>Oldest</option>
                                        </select>
                                    </div>
                                    <!-- Wyczyść filtry -->
                                    <div class="col-md-2 filter-buttons">
                                        <!-- Puste miejsce imitujące label -->
                                        <div class="form-label-placeholder miniKat"></div>
                                        <button type="button" class="btn btn-outline-secondary" id="clearFilters">Clear Filters</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Zakładki -->
                            <ul class="nav nav-tabs my-4">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#">All</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Bussines</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Private</a>
                                </li>
                            </ul>
                        </div>


                    </form>
                </div>




                <div style="width: 100%;" class="container my-4">
                    <div class="row">
                        <?php if (!empty($adsToDisplay)) : ?>
                            <?php foreach ($adsToDisplay as $ad): ?>
                                <div class="mb-3">
                                    <a style="text-decoration: none;" href="./ofert.php?adId=<?= $ad['id'] ?>" class="card featured p-3">
                                        <div class="row">
                                            <div class="col-md-2 text-center">
                                                <?php
                                                $imagePaths = !empty($ad['image_path']) ? explode(',', $ad['image_path']) : ['noImage.jpg'];
                                                $firstImage = $imagePaths[0];
                                                ?>
                                                <img src="./uploads/thumb_150x113/<?= htmlspecialchars($firstImage) ?>" alt="Image" class="img-fluid rounded">
                                            </div>
                                            <div class="col-md-8">
                                                <h5 class="card-title"><?= htmlspecialchars($ad['title']) ?></h5>
                                                <p style="font-size:12px" class="text-muted mx-1"><?= $ad['condit'] == 0 ? 'New' : 'Used' ?></p>
                                                <p style="margin-top: 7%; font-size:11px" class="text-muted"><?= htmlspecialchars($ad['localization']) ?> - <?= date('d F Y', strtotime($ad['updated_at'])) ?></p>
                                            </div>
                                            <div style="margin-top: 1%;padding-right:4%" class="col-md-2 text-end">
                                                <span class="price"><?= number_format($ad['price'], 2) ?> zł</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-primary text-center" role="alert">
                                <h4 class="alert-heading">No results!</h4>
                                <p>We couldn't find any ads matching your criteria.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>


            </main>
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
        document.getElementById('clearFilters').addEventListener('click', function() {
            document.getElementById('searchForm').reset();
        });
    </script>
</body>

</html>