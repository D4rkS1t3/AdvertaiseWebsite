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
        <button type="button" class="btn btn-light" id="btn-add" onclick="window.location.href = 'addAdvertise.php';">
            <span class="ms-2">Add an advertisement</span></button>
    </nav>



    <!-- Main Layout -->
    <div style="min-height: 630px;" id="kontener" class="container-fluid bg-light mt-3">
        <div class="row">
            <!-- Left Sidebar (Empty) -->
            <div class="col-md-2 bg-light" id="sideBar1"></div>

            <!-- Main Content -->
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

            <!-- Right Sidebar (Empty) -->
            <div class="col-md-2 bg-light" id="sideBar2"></div>
        </div>
    </div>

    
    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-5 mt-auto">
        &copy; 2024 Advertise Website
    </footer>

    <div id="editProductModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form enctype="multipart/form-data">
                <div class="modal-header">						
                    <h4 class="modal-title">Edit advertise</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">					
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" class="form-control" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Lokalizacja</label>
                        <input type="text" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select class="form-control" required>
                            <option value="">Select Category</option>
                            <option value="1">Electronics</option>
                            <option value="2">Clothing</option>
                            <option value="3">Furniture</option>
                            <option value="4">Toys</option>
                            <option value="5">Books</option>
                            <option value="6">Sports</option>
                            <option value="7">Home Appliances</option>
                            <option value="8">Health & Beauty</option>
                            <option value="9">Food & Beverages</option>
                            <option value="10">Jewelry</option>
                        </select>
                    </div>					
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                    <input type="submit" class="btn btn-info" value="Save">
                </div>
            </form>
        </div>
    </div>
</div>
<!-- addd modal -->
<div id="addProductModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form enctype="multipart/form-data">
                <div class="modal-header">						
                    <h4 class="modal-title">Add advertise</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">					
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" class="form-control" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Lokalizacja</label>
                        <input type="text" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select class="form-control" required>
                            <option value="">Select Category</option>
                            <option value="1">Electronics</option>
                            <option value="2">Clothing</option>
                            <option value="3">Furniture</option>
                            <option value="4">Toys</option>
                            <option value="5">Books</option>
                            <option value="6">Sports</option>
                            <option value="7">Home Appliances</option>
                            <option value="8">Health & Beauty</option>
                            <option value="9">Food & Beverages</option>
                            <option value="10">Jewelry</option>
                        </select>
                    </div>					
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                    <input type="submit" class="btn btn-info" value="Save">
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal HTML -->
<div id="deleteEmployeeModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form>
				<div class="modal-header">						
					<h4 class="modal-title">Delete Advertise</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">					
					<p>Are you sure you want to delete these Records?</p>
					<p class="text-warning"><small>This action cannot be undone.</small></p>
				</div>
				<div class="modal-footer">
					<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
					<input type="submit" class="btn btn-danger" value="Delete">
				</div>
			</form>
		</div>
	</div>
</div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</body>

</html>

</div>
</div>
</div>
</div>

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