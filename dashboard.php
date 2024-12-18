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
	$query = $db->prepare("SELECT id from ads WHERE active = 1");
	$query->execute();
	$allIds = $query->fetchAll(PDO::FETCH_COLUMN);
	//Losowanie 8 id
	$randomIds = array_rand(array_flip($allIds), 8);
	$ids = implode(',', $randomIds);
	//pobranie rekordow z tymi id
	$query = $db->prepare("select * from ads where id in ($ids)");
	$query->execute();
	$randomAds = $query->fetchAll(PDO::FETCH_ASSOC);
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
				<br>
				<h3 class="mt-4">Your Advertisement</h3>
				<br>
				<div style="background-color: rgb(206, 221, 255);font-size:10px" class="panel panel-default">
					<strong style="padding: 10px;">Attention</strong>
					<div class="panel-body">The process of buying and selling through Shipments Advertisement takes place in your Advertisment account. Ignore links sent outside the portal, e.g. via Whatsapp. Read more on our Blog.</div>
				</div>
				<div class="btn-group btn-group-justified">
					<a href="#" class="btn btn-light">Active</a>
					<a href="#" class="btn btn-light">Unactive</a>
				</div>

				<!-- Content Cards -->


				<div style="width: 100%;" class="container my-4">
					<div class="row">


						<!-- Card 1 -->
						<div class="mb-3">
							<div class="card featured p-3">
								<div class="row">
									<div class="col-md-2 text-center">
										<img src="https://via.placeholder.com/100x100" alt="Image" class="img-fluid rounded">
									</div>
									<div class="col-md-8">
										<h5 class="card-title">Tribute to Dirty Dancing 2x Bilety Częstochowa</h5>
										<p style="margin-top: 7%; font-size:9px" class="text-muted">Dąbrowa Górnicza - 05 grudnia 2024</p>
									</div>
									<div style="margin-top: 1%;padding-right:4%" class="col-md-2 text-end">
										<span class="price">60 zł</span>
										<div style="margin-top:10px;" class="actions">
											<button class="btn btn-sm btn-primary" (click)="onEdit(task)">
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
													<path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
													<path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
												</svg>
											</button>
											<button class="btn btn-sm btn-danger" (click)="onDelete(task)">
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
													<path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5" />
												</svg>
											</button>
										</div>
										<i class="bi bi-heart heart-icon"></i>
									</div>
								</div>
							</div>
						</div>


						<!-- Card 1 -->
						<div class="mb-3">
							<div class="card featured p-3">
								<div class="row">
									<div class="col-md-2 text-center">
										<img src="https://via.placeholder.com/100x100" alt="Image" class="img-fluid rounded">
									</div>
									<div class="col-md-8">
										<h5 class="card-title">Tribute to Dirty Dancing 2x Bilety Częstochowa</h5>
										<p style="margin-top: 7%; font-size:9px" class="text-muted">Dąbrowa Górnicza - 05 grudnia 2024</p>
									</div>
									<div style="margin-top: 1%;padding-right:4%" class="col-md-2 text-end">
										<span class="price">60 zł</span>
										<div style="margin-top:10px;" class="actions">
											<button class="btn btn-sm btn-primary" (click)="onEdit(task)">
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
													<path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
													<path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
												</svg>
											</button>
											<button class="btn btn-sm btn-danger" (click)="onDelete(task)">
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
													<path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5" />
												</svg>
											</button>
										</div>
										<i class="bi bi-heart heart-icon"></i>
									</div>
								</div>
							</div>
						</div>



						<!-- Add more cards as needed -->
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



	<!-- Poprawiony skrypt Bootstrap 5 (bez jQuery) -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>