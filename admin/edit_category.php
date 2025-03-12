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
    $category_id = $_GET['id'];

   
    $sql = "SELECT category_id, category_name, c_description, c_picture FROM category WHERE category_id = $category_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Category not found.";
        exit;
    }
} else {
    echo "Category ID is missing.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  
    $category_name = $_POST['category_name'];
    $c_description = $_POST['c_description'];
    $c_picture = $_FILES['c_picture']['name'];

    if ($c_picture) {
        move_uploaded_file($_FILES['c_picture']['tmp_name'], '../admin/uploads/' . $c_picture);
        $update_sql = "UPDATE category SET category_name='$category_name', c_description='$c_description', c_picture='$c_picture' WHERE category_id=$category_id";
    } else {
        $update_sql = "UPDATE category SET category_name='$category_name', c_description='$c_description' WHERE category_id=$category_id";
    }

    if ($conn->query($update_sql)) {
     
        header("Location: edit_category.php?id=$category_id&success=true");
        exit;
    } else {
        echo "Error updating category: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <link rel="icon" href="../logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">

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

        .category-btn {
            background-color: #D4AF37;
            color: #2a3d66;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .category-btn:hover {
            background-color: #F26D6D;
            color: white;
        }

        .edit-category {
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
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> Category updated successfully.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
                }
                ?>

                <div class="card shadow-lg p-4">
             
                    <a href="category_list.php" class="back-button" title="Go Back">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h3 class="edit-category">Edit Category</h3>
                    <form action="edit_category.php?id=<?php echo $category_id; ?>" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="category_name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="category_name" name="category_name" value="<?php echo $row['category_name']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="c_description" class="form-label">Description</label>
                            <textarea class="form-control" id="c_description" name="c_description" rows="3" required><?php echo $row['c_description']; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="c_picture" class="form-label">Category Image</label>
                            <input type="file" class="form-control" id="c_picture" name="c_picture">
                            <small class="form-text text-muted">Leave blank if you do not wish to change the image.</small>
                        </div>
                        <button type="submit" class="category-btn ">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>