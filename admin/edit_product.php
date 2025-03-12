<?php
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


if (isset($_GET['id'])) {
    $product_id = $_GET['id'];


    $sql = "SELECT product_id, product_name, p_description, p_image, price, category_name FROM product WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "Product not found.";
        exit;
    }
    $stmt->close();
} else {
    echo "Product ID is missing.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $product_name = $_POST['product_name'];
    $p_description = $_POST['p_description'];
    $price = $_POST['price'];
    $p_image = $_FILES['p_image']['name'];
    $category_name = $_POST['category_name'];

    if ($p_image) {
        move_uploaded_file($_FILES['p_image']['tmp_name'], '../admin/uploads/' . $p_image);
        $update_sql = "UPDATE product SET product_name = ?, p_description = ?, price = ?, p_image = ?, category_name = ? WHERE product_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssdsss", $product_name, $p_description, $price, $p_image, $category_name, $product_id);
    } else {
        $update_sql = "UPDATE product SET product_name = ?, p_description = ?, price = ?, category_name = ? WHERE product_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssdss", $product_name, $p_description, $price, $category_name, $product_id);
    }


    if ($stmt->execute()) {
    
        header("Location: edit_product.php?id=$product_id&success=true");
        exit;
    } else {
        echo "Error updating product: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="icon" href="../logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 1.5rem;
            color: #333333;
            text-decoration: none;
            background-color: transparent;
            padding: 10px 15px;
            border-radius: 50%;
            border: 2px solid #D4AF37;
            transition: background-color 0.3s ease, color 0.3s ease, transform 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 50px;
        }

        .back-button:hover {
            background-color: #D4AF37;
            color: #ffffff;
            transform: scale(1.1);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        .back-button i {
            font-size: 1.5rem;
        }

        .product-btn {
            background-color: #D4AF37;
            color: #2a3d66;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .product-btn:hover {
            background-color: #F26D6D;
            color: white;
        }

        .edit-product {
            color: #2a3d66;
            text-align: center;
            margin-bottom: 20px;
        }

        .main-content {
            padding: 20px;
        }

        .form-label {
            font-weight: bold;
        }

        .form-control {
            margin-bottom: 10px;
        }

        .alert-success {
            margin-top: 20px;
            font-size: 1.1em;
        }

        @media (max-width: 576px) {
            .category-btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <?php include 'navbar.html'; ?>


    <main class="main-content">
        <div class="container-fluid">
            <div class="top-bar d-flex justify-content-between align-items-center mb-4">
                <h4>Category List</h4>
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

            <div class="container mt-5">

                <?php
                if (isset($_GET['success']) && $_GET['success'] == 'true') {
                    echo '<div class="alert alert-success" role="alert">Product updated successfully!</div>';
                }
                ?>
                <div class="card shadow-lg p-4">
                
                    <a href="product_list.php" class="back-button" title="Go Back">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h3 class="edit-product">Edit Product</h3>
                    <form action="edit_product.php?id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="product_name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="product_name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="category_name" name="category_name" value="<?php echo htmlspecialchars($product['category_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="p_description" class="form-label">Product Description</label>
                            <textarea class="form-control" id="p_description" name="p_description" rows="3" required><?php echo htmlspecialchars($product['p_description']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="p_image" class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="p_image" name="p_image">
                            <small class="form-text text-muted">Leave blank if you do not wish to change the image.</small>
                        </div>
                        <button type="submit" class="product-btn">Save Changes</button>
                    </form>
                </div>

                <div>
                </div>
    </main>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>