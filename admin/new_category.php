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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = trim($_POST['category_name']);
    $category_description = trim($_POST['category_description']);
    $category_image = $_FILES['category_image'];


    if (empty($category_name) || empty($category_description)) {
        $error_message = "All fields are required.";
    } else {
        $target_dir = dirname(__FILE__) . "/category/";

     
        $image_url = '';
        if ($category_image && $category_image['error'] == UPLOAD_ERR_OK) {
            $image_filename = $category_name . "_" . basename($category_image["name"]);
            $target_file = $target_dir . $image_filename;

            if (move_uploaded_file($category_image["tmp_name"], $target_file)) {
                $image_url = "/category/" . $image_filename;
            } else {
                $error_message = "Failed to upload image.";
            }
        }

   
        if (empty($error_message)) {
            $sql = "INSERT INTO category (category_name, c_description, c_picture) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("sss", $category_name, $category_description, $image_url);

                if ($stmt->execute()) {
                    $success_message = "Category added successfully!";
                } else {
                    $error_message = "Database error: " . $stmt->error;
                }

                $stmt->close();
            } else {
                $error_message = "Failed to prepare database query.";
            }
        }
    }

    $_SESSION['success_message'] = $success_message;
    $_SESSION['error_message'] = $error_message;
    header("Location: new_category.php");
    exit;
}

$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    <link rel="icon" href="../logo.png" type="image/png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">

    <style>
        .category-btn {
            background-color: #D4AF37;
            color: #2a3d66;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            transition: background-color 0.3s, transform 0.2s;
        }

        .category-btn:hover {
            background-color: #F26D6D;
            color: white;
            transform: scale(1.05);
        }

        .category-card-title {
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
                <h4>Manage Categories</h4>
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
                <h5 class="category-card-title mb-3">Add New Category</h5>
                <form action="#" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="categoryName" name="category_name" placeholder="Enter category name" required>
                    </div>

                    <div class="mb-3">
                        <label for="categoryDescription" class="form-label">Category Description</label>
                        <textarea class="form-control" id="categoryDescription" name="category_description" rows="3" placeholder="Enter category description" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="categoryImage" class="form-label">Category Image</label>
                        <input type="file" class="form-control" id="categoryImage" name="category_image" accept="image/*" required>
                    </div>

                    <button type="submit" class="category-btn btn-success">Add Category</button>
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