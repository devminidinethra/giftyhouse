<?php
session_start();

include 'connection/connection.php';
include 'functions/common_function.php';

$user_id = $_SESSION['user_id'] ?? null;



if (isset($_GET['add-to-cart'])) {
    if (!$user_id) {
        header("Location: forms/login.php");
        exit();
    }

    $product_id = $_GET['add-to-cart'];
    $ip = getIPAddress();


    $user_query = $conn->prepare("SELECT full_name FROM users WHERE id = ?");
    $user_query->bind_param("i", $user_id);
    $user_query->execute();
    $user_result = $user_query->get_result();
    $user_data = $user_result->fetch_assoc();
    $full_name = $user_data['full_name'] ?? 'Guest';

   
    $check_product = $conn->prepare("SELECT * FROM product WHERE product_id = ?");
    $check_product->bind_param("s", $product_id);
    $check_product->execute();
    $product_result = $check_product->get_result();
}

// Handle logout request
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_unset();
    session_destroy();
    header('Location: forms/login.php');
    exit;
}

// Get category from GET request
$category = $_GET['category'] ?? null;

// Get category & price from POST request (if form is submitted)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'] ?? $category;
    $price = $_POST['price'] ?? null;
}

// Modify the query to fetch the stock quantity as well
$query = "SELECT product_id, product_name, price, p_image, quantity FROM product WHERE 1";
$params = [];
$types = "";

// Filter products based on category
if (!empty($category)) {
    $query .= " AND category_name = ?";
    $params[] = $category;
    $types .= "s";
}

// Add price filter
if (!empty($price)) {
    $query .= " AND price <= ?";
    $params[] = $price;
    $types .= "d";
}

// Pagination logic
if (isset($_GET['page_no']) && $_GET['page_no'] != "") {
    $page_no = $_GET["page_no"];
} else {
    // Default to page 1
    $page_no = 1;
}

// Fetch total records for pagination
$stmt1 = $conn->prepare("SELECT COUNT(*) as total_records FROM product");
$stmt1->execute();
$stmt1->store_result();
$stmt1->bind_result($total_records);
$stmt1->fetch();

// Products per page
$total_records_per_page = 9;
$offset = ($page_no - 1) * $total_records_per_page;
$total_no_of_pages = ceil($total_records / $total_records_per_page);

// Query to fetch products with limit (pagination)
$product_stmt = $conn->prepare($query . " LIMIT ?, ?");
$params[] = $offset;
$params[] = $total_records_per_page;
$types .= "ii";
$product_stmt->bind_param($types, ...$params);
$product_stmt->execute();
$product_result = $product_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gifty House</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">

    <style>
        /* Main container layout */
        .main-container {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        /* Search section - smaller */
        .search-container {
            flex: 1;
            max-width: 280px;
            min-width: 250px;
        }

        /* Product section - larger */
        .product-container {
            flex: 3;

        }

        /* Search section styling */
        #search {
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            border: 1px solid #ddd;
        }

        /* Heading style for search section */
        #search p {
            font-weight: 600;
            color: #2a3d66;
            font-size: 1.2rem;
            margin-bottom: 15px;
        }

        /* Category section */
        .form-check {
            margin-bottom: 10px;
        }

        .form-check-input {
            border-radius: 4px;
        }

        /* Style the range input */
        #priceRange {
            width: 100%;
            height: 8px;
            border-radius: 4px;
            border: none;
            appearance: none;
        }


        /* Price range label */
        .w-50 {
            font-weight: 600;
            color: #2a3d66;
            font-size: 0.9rem;
        }

        /* Submit button styling */
        .search-form .btn {
            background-color: #F5D547;
            color: #2a3d66;
            font-weight: 600;
            text-transform: uppercase;
            padding: 10px 20px;
            border-radius: 30px;
            border: none;
            width: 100%;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        /* Button hover effect */
        .search-form .btn:hover {
            background-color: #F26D6D;
            color: #fff;
        }

        /* Mobile responsiveness for search form */
        @media (max-width: 576px) {
            #search {
                padding: 15px;
            }

            .form-check-input {
                width: 18px;
                height: 18px;
            }

            .btn {
                font-size: 0.9rem;
            }
        }

        /* Product card styling */
        .product-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 35px;
            height: 500px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .product-card img {
            width: 100%;
            height: 310px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card-body h5.product-name {
            color: navy;
            font-weight: bold;
            font-size: 1.3rem;
            text-align: center;
            margin-bottom: 10px;
            min-height: 48px;
        }

        .product-card-body {
            padding: 15px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .product-card-body .btn {
            background-color: #f5d547;
            color: #2a3d66;
            border-radius: 5px;
            padding: 8px 16px;
            text-transform: uppercase;
            font-weight: 500;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease-in-out;
        }

        .product-card-body .btn:hover {
            background-color: #F26D6D;
            color: #fff;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3);
        }

        .product-card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .product-card img:hover {
            transform: scale(1.1);
        }

        /* Responsiveness for mobile devices */
        @media (max-width: 576px) {
            .product-card-body h5.product-name {
                font-size: 1rem;
            }

            .product-card-body p.price {
                font-size: 0.9rem;
            }

            .product-card-body .btn {
                padding: 8px 10px;
                font-size: 0.9rem;
            }

            .search-container {
                max-width: 100%;
                min-width: 100%;
            }

            .product-container {
                flex: 1;

            }
        }

        /* Custom pagination styles */
        ul.pagination {
            list-style-type: none;
            display: flex;
            padding-left: 0;
        }

        ul.pagination li.page-item {
            margin: 0 5px;
        }

        ul.pagination li.page-item a.page-link {
            color: #F26D6D;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 30px;
        }

        ul.pagination li.page-item a.page-link:hover {
            color: #fff;
            background-color: #F5D547 !important;
        }

        /* Hover effect for the entire pagination item */
        ul.pagination li.page-item:hover {
            background-color: #F5D547;
        }

        ul.pagination li.page-item a.page-link {
            color: #F26D6D !important;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <div class="main-container">
            <!-- Search Section (Left Side) -->
            <aside class="col-md-4 mb-4">
                <section id="search">
                    <div class="container">
                        <p>Search Products</p>
                        <hr>
                    </div>
                    <form class="search-form" method="post" action="shop.php">
                        <div>
                            <p>Category</p>
                            <div class="form-check">
                                <input type="radio" value="Jewelry" class="form-check-input" name="category" id="category-one">
                                <label for="category-one" class="form-check-label">Jewelry</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" value="Stationeries" class="form-check-input" name="category" id="category-two">
                                <label for="category-two" class="form-check-label">Stationeries</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" value="Makeup" class="form-check-input" name="category" id="category-three">
                                <label for="category-three" class="form-check-label">Makeup</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" value="Hair Accessories" class="form-check-input" name="category" id="category-four">
                                <label for="category-four" class="form-check-label">Hair Accessories</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" value="Fashion Accessories" class="form-check-input" name="category" id="category-five">
                                <label for="category-five" class="form-check-label">Fashion Accessories</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" value="Personalized Gifts" class="form-check-input" name="category" id="category-six">
                                <label for="category-six" class="form-check-label">Personalized Gifts</label>
                            </div>
                        </div>

                        <div class="my-3">
                            <p>Price Range</p>
                            <input type="range" name="price" value="50" class="form-range w-50" min="100" max="3000" id="priceRange">

                            <div class="w-50 d-flex justify-content-between">
                                <span id="priceValue">Rs.100</span>
                                <span>Rs. 3000</span>
                            </div>
                        </div>

                        <div class="form-group my-3">
                            <input type="submit" name="search" value="Search" class="btn btn-primary w-100">
                        </div>
                    </form>
                </section>
            </aside>

            <!-- Product Section (Right Side) -->
            <div class="product-container">
                <div class="row">
                    <?php if ($product_result->num_rows > 0) { ?>
                        <?php while ($row = $product_result->fetch_assoc()) {
                            $stock_quantity = $row['quantity'];
                        ?>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4 mb-4">
                                <div class="product-card">
                                    <img src="admin<?php echo htmlspecialchars($row['p_image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                                    <div class="product-card-body">
                                        <h5 class="product-name"><?php echo htmlspecialchars($row['product_name']); ?></h5>
                                        <p class="price">Rs. <?php echo htmlspecialchars(number_format($row['price'], 2)); ?></p>

                                        <!-- Check stock quantity and display appropriate message -->
                                        <?php if ($stock_quantity <= 0) { ?>
                                            <a href="#" class="btn btn-danger" disabled>Out of Stock</a>
                                        <?php } elseif ($stock_quantity < 6) { ?>
                                            <a href="single_product.php?product_id=<?php echo $row['product_id']; ?>" class="btn btn-warning">Only <?php echo $stock_quantity; ?> Left</a>
                                        <?php } else { ?>
                                            <a href="single_product.php?product_id=<?php echo $row['product_id']; ?>" class="btn">Shop Now</a>
                                        <?php } ?>

                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="col-12 text-center">
                            <h3>No Products Found</h3>
                            <p>Sorry, there are no products available in this category.</p>
                        </div>
                    <?php } ?>
                </div>

            </div>
        </div>

        <nav aria-label="page navigation example">
            <ul class="pagination justify-content-end mt-4">
                <li class="page-item" <?php if ($page_no <= 1) {
                                            echo 'disabled';
                                        } ?>>
                    <a class="page-link" href="<?php echo ($page_no <= 1) ? '#' : "?page_no=" . ($page_no - 1); ?>">Previous</a>
                </li>

                <?php for ($i = 1; $i <= $total_no_of_pages; $i++) { ?>
                    <li class="page-item"><a class="page-link" href="?page_no=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                <?php } ?>

                <li class="page-item" <?php if ($page_no >= $total_no_of_pages) {
                                            echo 'disabled';
                                        } ?>>
                    <a class="page-link" href="<?php echo ($page_no >= $total_no_of_pages) ? '#' : "?page_no=" . ($page_no + 1); ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>

    <?php include 'footer.html'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const priceRange = document.getElementById('priceRange');
        const priceValue = document.getElementById('priceValue');

        // Function to update the value display
        priceRange.addEventListener('input', function() {
            priceValue.textContent = priceRange.value;
        });
    </script>

</body>

</html>