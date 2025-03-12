<?php
session_start();
require '../connection/connection.php';

$sql_admin = "SELECT full_name, profile_picture FROM users WHERE role = 'admin' LIMIT 1";
$result_admin = $conn->query($sql_admin);

if ($result_admin->num_rows > 0) {
    $admin = $result_admin->fetch_assoc();
    $admin_name = htmlspecialchars($admin['full_name']);
    $admin_profile_pic = htmlspecialchars($admin['profile_picture']);
} else {
    $admin_name = 'Admin';
    $admin_profile_pic = '../img/default-profile.jpg';
}


$success_message = '';
$error_message = '';


$category_query = "SELECT category_id, category_name FROM category";
$category_result = $conn->query($category_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_category = trim($_POST['product_category']);
    $product_name = trim($_POST['product_name']);
    $product_description = trim($_POST['product_description']);
    $product_price = trim($_POST['product_price']);
    $product_quantity = trim($_POST['product_quantity']);

    $is_new_arrival = isset($_POST['is_new_arrival']);
    $product_images = [
        $_FILES['product_image'],
        $_FILES['product_image2'],
        $_FILES['product_image3'],
        $_FILES['product_image4']
    ];

    
    if (empty($product_category) || empty($product_name) || empty($product_description) || empty($product_price) || empty($product_quantity) || $product_images[0]['error'] != UPLOAD_ERR_OK) {
        $error_message = "All fields are required, and at least the first image must be uploaded.";
    } elseif (!is_numeric($product_price) || $product_price <= 0) {
        $error_message = "Please enter a valid price greater than 0.";
    } elseif (!is_numeric($product_quantity) || $product_quantity < 1) {
        $error_message = "Please enter a valid quantity greater than 0.";
    } else {
        
        $target_dir = dirname(__FILE__) . "/product/";

        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                $error_message = "Failed to create target directory.";
            }
        }

        if (empty($error_message)) {
            $image_urls = [];
            $image_filenames = [];

            foreach ($product_images as $index => $image) {
                if ($image['error'] === UPLOAD_ERR_OK) {
                    $image_filename = $product_name . "_" . ($index + 1) . "_" . basename($image["name"]);
                    $target_file = $target_dir . $image_filename;

                    if (move_uploaded_file($image["tmp_name"], $target_file)) {
                        $image_urls[] = "/product/" . $image_filename;
                    } else {
                        $error_message = "Failed to upload image " . ($index + 1) . ". Please check permissions.";
                        break;
                    }
                } else {
                    $image_urls[] = null; 
                }
            }

            if (empty($error_message)) {
             
                $category_name_query = "SELECT category_name FROM category WHERE category_id = ?";
                $category_stmt = $conn->prepare($category_name_query);
                $category_stmt->bind_param("s", $product_category);
                $category_stmt->execute();
                $category_stmt->bind_result($category_name);
                $category_stmt->fetch();
                $category_stmt->close();

            
                $new_product_id = uniqid('p'); // Generates IDs like p63e5c7a1d8d9

      
                $formatted_price = number_format((float)$product_price, 2, '.', '');

            
                $stmt = $conn->prepare("INSERT INTO product (product_id, category_id, category_name, product_name, p_image, p_image2, p_image3, p_image4, p_description, price, quantity) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param(
                    "sssssssssdi",
                    $new_product_id,
                    $product_category,
                    $category_name,
                    $product_name,
                    $image_urls[0],
                    $image_urls[1],
                    $image_urls[2],
                    $image_urls[3],
                    $product_description,
                    $formatted_price,
                    $product_quantity 
                );


                if ($stmt->execute()) {
            
                    if ($is_new_arrival) {
                        $arrival_stmt = $conn->prepare("INSERT INTO new_arrivals (product_id, arrival_date) VALUES (?, NOW())");
                        $arrival_stmt->bind_param("s", $new_product_id);
                        if (!$arrival_stmt->execute()) {
                            $error_message = "Error inserting into new_arrivals: " . $arrival_stmt->error;
                        }
                        $arrival_stmt->close();
                    }

                    $success_message = "Product added successfully!";
                    header("Location: new_product.php");
                    exit;
                } else {
                    $error_message = "Error: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Products</title>
    <link rel="icon" href="../logo.png" type="image/png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">

    <style>
        .product-btn {
            background-color: #D4AF37;
            color: #2a3d66;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            transition: background-color 0.3s, transform 0.2s;
        }

        .product-btn:hover {
            background-color: #F26D6D;
            color: white;
            transform: scale(1.05);
        }

        .product-card-title {
            color: #D4AF37;
            display: block;
            font-size: 1.7rem;
            font-weight: 500;
            text-align: center;
        }
    </style>
</head>

<body>

    <?php include 'navbar.html'; ?>

    <main class="main-content">
        <div class="container-fluid">
            <div class="top-bar d-flex justify-content-between align-items-center mb-4">
                <h4>New Product</h4>
                <form class="d-flex align-items-center">
                    <input class="form-control" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-primary ms-2 search-btn">Search</button>

                    <div class="dropdown ms-3">
                        <button class="btn btn-link p-0" type="button" id="adminProfile" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="uploads/<?php echo $admin_profile_pic; ?>" alt="Admin Profile" class="rounded-circle" width="55" height="55">
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="adminProfile">
                            <li class="dropdown-header"><strong><?php echo $admin_name; ?></strong></li>
                            <li><a class="dropdown-item" href="a_profile.php"><i class="bi bi-person-fill"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="../forms/login.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </div>
                </form>
            </div>

            <?php if ($success_message): ?>
                <div class="alert alert-success" role="alert">
                    <?= $success_message; ?>
                </div>
            <?php elseif ($error_message): ?>
                <div class="alert alert-danger" role="alert">
                    <?= $error_message; ?>
                </div>
            <?php endif; ?>

            <div class="card p-4 mt-4">
                <h5 class="product-card-title mb-3">Add New Product</h5>
                <form action="#" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="productCategory" class="form-label">Category</label>
                        <select class="form-select" id="productCategory" name="product_category" required>
                            <option value="" disabled selected>Select a category</option>
                            <?php
                            if ($category_result->num_rows > 0) {
                                while ($category = $category_result->fetch_assoc()) {
                                    echo '<option value="' . $category['category_id'] . '">' . htmlspecialchars($category['category_name']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="productName" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="productName" name="product_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="productImage" class="form-label">Product Image 1</label>
                        <input type="file" class="form-control" id="productImage" name="product_image" accept="image/*" required>
                    </div>

                    <div class="mb-3">
                        <label for="productImage2" class="form-label">Product Image 2</label>
                        <input type="file" class="form-control" id="productImage2" name="product_image2" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label for="productImage3" class="form-label">Product Image 3</label>
                        <input type="file" class="form-control" id="productImage3" name="product_image3" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label for="productImage4" class="form-label">Product Image 4</label>
                        <input type="file" class="form-control" id="productImage4" name="product_image4" accept="image/*">
                    </div>


                    <div class="mb-3">
                        <label for="productDescription" class="form-label">Product Description</label>
                        <textarea class="form-control" id="productDescription" name="product_description" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="productQuantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="productQuantity" name="product_quantity" min="1" required>
                    </div>


                    <div class="mb-3">
                        <label for="productPrice" class="form-label">Price</label>
                        <input type="number" class="form-control" id="productPrice" name="product_price" step="0.01" min="0" required>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="isNewArrival" name="is_new_arrival">
                        <label class="form-check-label" for="isNewArrival">
                            Mark as New Arrival
                        </label>
                    </div>

                    <button type="submit" class="product-btn btn-success">Add Product</button>
                </form>
            </div>
        </div>
    </main>

    <footer class="mt-4 text-center">
        © 2025 Gifty House. All Rights Reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>