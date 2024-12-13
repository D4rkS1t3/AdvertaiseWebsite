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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="css/mojestyle.css" rel="stylesheet" />
</head>
<style>
    body {
        padding-top: 47px;
        /* Przesuwa zawartość w dół o wysokość navbar */
        margin: 0;
    }

    /*search box css start here*/
    .search-sec {
        padding: 2rem;
    }

    .search-slt {
        display: block;
        width: 100%;
        height: 72px;
        font-size: 1.25rem;
        line-height: 1.5;
        color: #55595c;
        background-color: #fff;
        background-image: none;
        border: 1px solid #ccc;
        height: calc(5rem + 5px) !important;
        border-radius: 0;
    }

    .btn-search {
        width: 100%;
        font-size: 16px;
        font-weight: 400;
        text-transform: capitalize;
        height: calc(5rem + 5px) !important;
        border-radius: 2%;
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
</style>


<body class="sb-nav-fixed">
    <!-- Nav -->

    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" id="navFirst" href="index.php">Advertise Website</a>
        <!-- Sidebar Toggle-->


        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">

            <li class="nav-item dropdown">
                <a style="text-decoration: none;" class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="ms-2">Your account</span><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="./accountSett.php">Settings</a></li>
                    <li><a class="dropdown-item" href="./dashboard.php">My advertise</a></li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li><a class="dropdown-item" href="./logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
        <button type="button" class="btn btn-light" id="btn-add" onclick="window.location.href = 'addAnnounView.php';">
            <span class="ms-2">Add an advertisement</span></button>
    </nav>



    <!-- Main Layout -->
    <div style="min-height: 630px;" id="kontener" class="container-fluid bg-light mt-3">
        <div class="row">
            <!-- Left Sidebar (Empty) -->
            <div class="col-md-2 bg-light" id="sideBar1"></div>

            <!-- Main Content -->
            <main class="col-md-8 bg-light">
                <!-- wyszukiwanie -->
                <div style="padding: 30px 0;" class="col-md-12 bg-light>
                    <form action=" #" method="post" novalidate="novalidate">
                    <div class=" justify-content-center align-items-center">
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-8 p-0">
                                <input type="text" class="form-control search-slt" placeholder="Find something for you!">
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 p-0">
                                <input type="text" class="form-control search-slt" placeholder="Location">
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 p-0">
                                <button type="button" class="btn btn-light btn-search">Search</button>
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
                            <div class="col-4 col-sm-4 col-lg-2 mb-5"><a href=""><img style="width: 70px;" src="./img/kategorie/elektronika.png" alt="">
                                    <p>Elektronika</p>
                                </a></div>
                            <div class="col-4 col-sm-4 col-lg-2 mb-5"><a href=""><img style="width: 70px;" src="./img/kategorie/elektronika.png" alt="">
                                    <p>Elektronika</p>
                                </a></div>
                            <div class="col-4 col-sm-4 col-lg-2 mb-5"><a href=""><img style="width: 70px;" src="./img/kategorie/elektronika.png" alt="">
                                    <p>Elektronika</p>
                                </a></div>
                            <div class="col-4 col-sm-4 col-lg-2 mb-5"><a href=""><img style="width: 70px;" src="./img/kategorie/elektronika.png" alt="">
                                    <p>Elektronika</p>
                                </a></div>
                            <div class="col-4 col-sm-4 col-lg-2 mb-5"><a href=""><img style="width: 70px;" src="./img/kategorie/elektronika.png" alt="">
                                    <p>Elektronika</p>
                                </a></div>
                            <div class="col-4 col-sm-4 col-lg-2 mb-5"><a href=""><img style="width: 70px;" src="./img/kategorie/elektronika.png" alt="">
                                    <p>Elektronika</p>
                                </a></div>
                        </div>
                    </div>
                </div>

                <!-- promowane -->

                <div class="col-md-12 bg-light">

                    <h2 style="margin:5% 0%;text-align: center;">Promoted Ads</h2>
                    <div class="mt-5">
                        <div class="row">
                            <!-- pierwszy ele -->

                            <div class="col-6 col-sm-6 col-md-4 col-lg-3 mb-5">
                                <a style="text-decoration: none;" href="">
                                    <div class="d-flex justify-content-center"><img src="./uploads/675c492b2ea68.jpg" style="height: 200px;width:200px;" alt=""></div>
                                    <div>
                                        <div class="m-4">
                                            <p style="font-size:13px">rusztowania bardzo dobre itd rusztowanie cos tam</p>
                                            <p><b>255,15zl</b></p>
                                        </div>
                                        <div class="m-4">
                                            <small style="font-size: 10px;">Grójec</small><br>
                                            <small style="font-size: 10px;">odświeżono dnia 12 grudnia 2024</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <!-- pierwszy ele -->

                            <div class="col-6 col-sm-6 col-md-4 col-lg-3 mb-5">
                                <a style="text-decoration: none;" href="">
                                    <div class="d-flex justify-content-center"><img src="./uploads/675c492b2ea68.jpg" style="height: 200px;width:200px;" alt=""></div>
                                    <div>
                                        <div class="m-4">
                                            <p style="font-size:13px">rusztowania bardzo dobre itd rusztowanie cos tam</p>
                                            <p><b>255,15zl</b></p>
                                        </div>
                                        <div class="m-4">
                                            <small style="font-size: 10px;">Grójec</small><br>
                                            <small style="font-size: 10px;">odświeżono dnia 12 grudnia 2024</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <!-- pierwszy ele -->

                            <div class="col-6 col-sm-6 col-md-4 col-lg-3 mb-5">
                                <a style="text-decoration: none;" href="">
                                    <div class="d-flex justify-content-center"><img src="./uploads/675c492b2ea68.jpg" style="height: 200px;width:200px;" alt=""></div>
                                    <div>
                                        <div class="m-4">
                                            <p style="font-size:13px">rusztowania bardzo dobre itd rusztowanie cos tam</p>
                                            <p><b>255,15zl</b></p>
                                        </div>
                                        <div class="m-4">
                                            <small style="font-size: 10px;">Grójec</small><br>
                                            <small style="font-size: 10px;">odświeżono dnia 12 grudnia 2024</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <!-- pierwszy ele -->

                            <div class="col-6 col-lg-3 mb-5">
                                <a style="text-decoration: none;" href="">
                                    <div class="d-flex justify-content-center"><img src="./uploads/675c492b2ea68.jpg" style="height: 200px;width:200px;" alt=""></div>
                                    <div>
                                        <div class="m-4">
                                            <p style="font-size:13px">rusztowania bardzo dobre itd rusztowanie cos tam</p>
                                            <p><b>255,15zl</b></p>
                                        </div>
                                        <div class="m-4">
                                            <small style="font-size: 10px;">Grójec</small><br>
                                            <small style="font-size: 10px;">odświeżono dnia 12 grudnia 2024</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <!-- pierwszy ele -->

                            <div class="col-6  col-lg-3 mb-5">
                                <a style="text-decoration: none;" href="">
                                    <div class="d-flex justify-content-center"><img src="./uploads/675c492b2ea68.jpg" style="height: 200px;width:200px;" alt=""></div>
                                    <div>
                                        <div class="m-4">
                                            <p style="font-size:13px">rusztowania bardzo dobre itd rusztowanie cos tam</p>
                                            <p><b>255,15zl</b></p>
                                        </div>
                                        <div class="m-4">
                                            <small style="font-size: 10px;">Grójec</small><br>
                                            <small style="font-size: 10px;">odświeżono dnia 12 grudnia 2024</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <!-- pierwszy ele -->

                            <div class="col-6 col-lg-3 mb-5">
                                <a style="text-decoration: none;" href="">
                                    <div class="d-flex justify-content-center"><img src="./uploads/675c492b2ea68.jpg" style="height: 200px;width:200px;" alt=""></div>
                                    <div>
                                        <div class="m-4">
                                            <p style="font-size:13px">rusztowania bardzo dobre itd rusztowanie cos tam</p>
                                            <p><b>255,15zl</b></p>
                                        </div>
                                        <div class="m-4">
                                            <small style="font-size: 10px;">Grójec</small><br>
                                            <small style="font-size: 10px;">odświeżono dnia 12 grudnia 2024</small>
                                        </div>
                                    </div>
                                </a>
                            </div>


                        </div>
                    </div>
                </div>

            </main>

            <!-- Right Sidebar (Empty) -->
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
    <div class="bg-light" style="min-height: 200px;" class="popular">
        <div class="container">
            <div class="row my-5 text-center">
                <!-- Pierwsza kolumna -->
                <div class="col-6">
                    <div class="mb-3" style="align-items: center;"><img style="height:100px; width:100px" src="./img/1.svg" alt=""></div>
                    <div class="m-4" style="font-size:12px;">
                        <span>
                        Main Categories: Antiques & CollectiblesBusiness & IndustryAutomotiveReal EstateWorkHome & GardenElectronicsFashionFarmingAnimalsFor KidsSports & HobbiesMusic & EducationHealth & BeautyServicesAccommodationRentalI'll Give Away for Free                        </span>
                    </div>
                </div>

                <!-- Druga kolumna -->
                <div class="col-6">
                    <div class="mb-3" style="align-items: center;"><img style="height:100px; width:100px" src="./img/2.svg" alt=""></div>
                    <div class="m-4" style="font-size:12px;"><span>
                    Popular searches: passenger carsrenault capturused carspassenger carskia ceedhyundai i20hyundai i30hyundai ix35bmw x1ford kugavw tiguankia sportagepeugeot 3008hyundai tucsonnissan qashqaitoyota corollatoyota aurisnissan jukeaudi q5volvo xc60                        </span>
                    </div>
                </div>
            </div>
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