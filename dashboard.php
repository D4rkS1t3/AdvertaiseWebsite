<?php
session_start();
require 'db.php';

if (!isset($_SESSION['session_id'])) {
	header("Location: signin.php");
	exit();
}

$sessionID = $_SESSION['session_id'];
try {
	//pobranie id uzytkownika zeby wybrac odpowiednie ogloszenia

	$query = $db->prepare("SELECT id FROM users WHERE session_id = :session_id");
	$query->bindParam(':session_id', $sessionID);
	$query->execute();
	$data = $query->fetch(PDO::FETCH_ASSOC);
	$userId = $data['id'];

	//pobranie z bazy ogloszen uzytkownika aktywnych ogloszen
	$queryActiveAds = $db->prepare("SELECT * FROM ads WHERE user_id = :user_id && active = 1");
	$queryActiveAds->bindParam(':user_id', $userId);
	$queryActiveAds->execute();
	$activeAds = $queryActiveAds->fetchAll(PDO::FETCH_ASSOC);

	//pobranie z bazy ogloszen uzytkownika nieaktywnych ogloszen
	$queryUnActiveAds = $db->prepare("SELECT * FROM ads WHERE user_id = :user_id && active = 0");
	$queryUnActiveAds->bindParam(':user_id', $userId);
	$queryUnActiveAds->execute();
	$unActiveAds = $queryUnActiveAds->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
	echo json_encode(['success' => false, 'message' => 'Error fetching your data!']);
	exit();
}
// //losowe ogloszenia do promoted ads

$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'active';

$adsToDisplay = $activeTab === 'active' ? $activeAds : $unActiveAds;


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

		.btn.active {
			background-color: #007bff;
			color: white;
			border-color: #0056b3;
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
				<h3 class="my-1">Your Advertisement</h3>
				<br>
				<div class="alert alert-primary" role="alert">
					<strong>Attention</strong>
					<div class="panel-body">The process of buying and selling through Shipments Advertisement takes place in your Advertisment account. Ignore links sent outside the portal, e.g. via Whatsapp. Read more on our Blog.</div>
				</div>
				<div class="btn-group d-flex w-100 my-3">
					<a href="?tab=active" class="btn btn-light flex-fill <?php echo $activeTab === 'active' ? 'active' : ''; ?>">Active</a>
					<a href="?tab=unactive" class="btn btn-light flex-fill <?php echo $activeTab === 'unactive' ? 'active' : ''; ?>">Unactive</a>
				</div>

				<!-- Content Cards -->


				<div style="width: 100%;" class="container my-4">
					<div class="row">

						<?php if (!empty($adsToDisplay)) : ?>
							<?php foreach ($adsToDisplay as $ad): ?>
								<!-- Card 1 -->
								<div class="mb-3">
									<a href="./ofert.php?adId=<?= $ad['id'] ?>" style="text-decoration: none;" data-id="<?= $ad['id'] ?>" class="card featured p-3">
										<div class="row">
											<div class="col-md-2 text-center">
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
												<img src="./uploads/thumb_150x113/<?= $firstImage ?>" alt="Image" class="img-fluid rounded">
											</div>
											<div class="col-md-8 ">
												<h5 class="card-title"><?= $ad['title'] ?></h5>
												<p style="margin-top: 7%; font-size:11px" class="text-muted"><?= $ad['localization'] ?> - <?= date('d F Y', strtotime($ad['updated_at']))  ?></p>
											</div>
											<div style="margin-top: 1%;padding-right:4%" class="col-md-2 text-end">
												<span class="price"><?= $ad['price'] ?> zł</span>
												<?php if ($activeTab === 'active'): ?>
													<div style="margin-top:10px;" class="actions">
														<button class="btn btn-sm btn-primary btn-edit-ads" data-id="<?= $ad['id'] ?>" >
															<i class="fa-solid fa-pen-to-square"></i>
														</button>
														<button class="btn btn-sm btn-danger btn-move-to-inactive" data-id="<?= $ad['id'] ?>" >
															<i class="fa-solid fa-arrow-right"></i>
														</button>
													</div>
												<?php endif; ?>

												<i class="bi bi-heart heart-icon"></i>
											</div>
										</div>
									</a>
								</div>
							<?php endforeach; ?>
						<?php else: ?>
							<?php if ($activeTab === 'active'): ?>
								<!-- Div z informacją o braku ogłoszeń dla aktywnych -->
								<div class="alert alert-primary text-center" role="alert">
									<h4 class="alert-heading">No ads!</h4>
									<p>You don't have any ads in this section. Try adding a new ad!</p>
									<a href="./addAnnounView.php" class="btn btn-primary mt-2">Add ads!</a>
								</div>
							<?php else: ?>
								<!-- Div z informacją o braku ogłoszeń dla nieaktywnych -->
								<div class="alert alert-primary text-center" role="alert">
									<h4 class="alert-heading">No ads!</h4>
									<p>You don't have any ads in this section. When the active ad ends it will be here.</p>
									<a href="?tab=active" class="btn btn-primary mt-2">Active ads</a>
								</div>
							<?php endif; ?>

						<?php endif; ?>


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

	<script>
document.addEventListener('DOMContentLoaded', function () {
    // Obsługa przycisków 'btn-move-to-inactive'
    const moveToInactiveButtons = document.querySelectorAll('.btn-move-to-inactive');

    moveToInactiveButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault(); // Zapobiegaj domyślnemu zachowaniu przycisku
            event.stopImmediatePropagation(); // Zapobiegaj dalszemu wykonywaniu innych handlerów
            const adId = this.getAttribute('data-id');

            if (confirm("Are you sure you want to move the ad to inactive?")) {
                fetch('./moveToInactive.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: adId })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("The ad has been moved to inactive.");
                            location.reload(); // Odświeżenie strony
                        } else {
                            alert("Error occurred: " + data.message);
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("An error occurred while transferring the ad.");
                    });
            }
        });
    });

    // Obsługa przycisków 'btn-edit-ads'
    const editButtons = document.querySelectorAll('.btn-edit-ads');

    editButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault(); // Zapobiegaj domyślnemu zachowaniu przycisku
            event.stopImmediatePropagation(); // Zapobiegaj dalszemu wykonywaniu innych handlerów
            const adId = this.getAttribute('data-id');

            if (adId) {
                // Przekierowanie do strony edycji ogłoszenia
                window.location.href = `editAnnounView.php?id=${adId}`;
            } else {
                alert("This ad does not have a valid ID.");
            }
        });
    });

    // Obsługa linków 'ad-link' (gdy kliknięto w link, a nie w przycisk)
    const adLinks = document.querySelectorAll('.ad-link');

    adLinks.forEach(link => {
        link.addEventListener('click', function (event) {
            const adId = this.getAttribute('data-id');

            if (adId) {
                // Przejście do strony ogłoszenia
                window.location.href = `./ofert.php?adId=${adId}`;
            } else {
                alert("Ad ID is missing.");
            }
        });
    });
});
	</script>

</body>

</html>