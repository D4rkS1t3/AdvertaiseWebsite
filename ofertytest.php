<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filtry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .filter-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
        }
        .filter-section h5 {
            font-weight: bold;
            margin-bottom: 15px;
        }
        .filter-buttons {
            text-align: right;
        }
        .filter-buttons button {
            margin-left: 10px;
        }
        .nav-tabs {
            border-bottom: 2px solid #007bff;
        }
        .nav-tabs .nav-link.active {
            border-color: #007bff #007bff #f8f9fa;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="filter-section">
            <h5>Filtry</h5>
            <form id="filters-form">
                <div class="row g-3 align-items-center">
                    <!-- Kategoria -->
                    <div class="col-md-3">
                        <label for="category" class="form-label">Kategoria</label>
                        <select class="form-select" id="category">
                            <option selected>Akcesoria dla zwierząt</option>
                            <option value="1">Elektronika</option>
                            <option value="2">Moda</option>
                            <option value="3">Sport</option>
                        </select>
                    </div>
                    <!-- Podkategoria -->
                    <div class="col-md-3">
                        <label for="subcategory" class="form-label">Podkategoria</label>
                        <select class="form-select" id="subcategory">
                            <option selected>Wszystkie</option>
                            <option value="1">Smycze</option>
                            <option value="2">Zabawki</option>
                            <option value="3">Legowiska</option>
                        </select>
                    </div>
                    <!-- Stan -->
                    <div class="col-md-2">
                        <label for="condition" class="form-label">Stan</label>
                        <select class="form-select" id="condition">
                            <option selected>Wszystkie</option>
                            <option value="1">Nowe</option>
                            <option value="2">Używane</option>
                        </select>
                    </div>
                    <!-- Cena -->
                    <div class="col-md-2">
                        <label class="form-label">Cena</label>
                        <div class="input-group">
                            <input type="number" class="form-control" placeholder="Od" id="priceFrom">
                            <input type="number" class="form-control" placeholder="Do" id="priceTo">
                        </div>
                    </div>
                    <!-- Wyczyść filtry -->
                    <div class="col-md-2 filter-buttons">
                        <button type="button" class="btn btn-outline-secondary" id="clearFilters">Wyczyść filtry</button>
                        <button type="submit" class="btn btn-primary">Filtruj</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Zakładki -->
        <ul class="nav nav-tabs my-4">
            <li class="nav-item">
                <a class="nav-link active" href="#">Wszystkie</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Firmowe</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Prywatne</a>
            </li>
        </ul>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('clearFilters').addEventListener('click', function () {
            document.getElementById('filters-form').reset();
        });

        document.getElementById('filters-form').addEventListener('submit', function (e) {
            e.preventDefault();
            // Przechwyć dane z filtrów
            const category = document.getElementById('category').value;
            const subcategory = document.getElementById('subcategory').value;
            const condition = document.getElementById('condition').value;
            const priceFrom = document.getElementById('priceFrom').value;
            const priceTo = document.getElementById('priceTo').value;

            console.log({ category, subcategory, condition, priceFrom, priceTo });
            // Dodaj logikę przetwarzania filtrów
        });
    </script>
</body>
</html>
