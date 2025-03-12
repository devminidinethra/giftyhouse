<?php
session_start();
include('../connection/connection.php');


$query = "SELECT * FROM users WHERE role = 'admin' LIMIT 1";
$result = mysqli_query($conn, $query);
$admin = mysqli_fetch_assoc($result);

if (!$admin) {
    die("No admin details found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // Validation
    if (empty($name) || empty($email) || empty($phone)) {
        $_SESSION['error'] = "All fields are required!";
        header("Location: a_profile.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format!";
        header("Location: a_profile.php");
        exit();
    }

    if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        $_SESSION['error'] = "Phone number must be 10-15 digits!";
        header("Location: a_profile.php");
        exit();
    }

    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);
    $phone = mysqli_real_escape_string($conn, $phone);

   
    if (!empty($_FILES['profilePic']['name'])) {
        $targetDir = "uploads/";
    
        // Ensure the uploads directory exists
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
    
        $fileName = time() . '_' . basename($_FILES['profilePic']['name']);
        $targetFile = $targetDir . $fileName;

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['profilePic']['tmp_name']);
    
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['profilePic']['tmp_name'], $targetFile)) {
                $profilePic = $fileName;
            } else {
                $_SESSION['error'] = "Failed to upload image!";
                header("Location: a_profile.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Invalid image format! Only JPG, PNG, and GIF are allowed.";
            header("Location: a_profile.php");
            exit();
        }
    } else {
        $profilePic = $admin['profile_picture']; 
    }
    

  
    $updateQuery = "UPDATE users SET full_name='$name', email='$email', contact_number='$phone', profile_picture='$profilePic' WHERE role='admin'";

    if (mysqli_query($conn, $updateQuery)) {
        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: a_profile.php");
        exit();
    } else {
        $_SESSION['error'] = "Error updating profile: " . mysqli_error($conn);
        header("Location: a_profile.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" href="../logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }
      
        .profile-container {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
            margin: 40px auto;
            max-width: 600px;
        }
        .profile-container img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            margin-bottom: 15px;
        }
        .profile-container h2 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .profile-container p {
            font-size: 18px;
            color: #555;
        }
        .profile-container .e-btn {
            background-color: #f5d547;
            color: #2a3d66;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .profile-container .e-btn:hover {
            background-color: #F26D6D;
            color: #fff;
        }
        .form-container {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin: 40px auto;
            max-width: 600px;
        }
        .form-container h4 {
            font-size: 22px;
            margin-bottom: 20px;
        }
        .form-container .form-label {
            font-weight: bold;
            font-size: 16px;
        }
        .form-container input {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 12px;
            font-size: 16px;
        }
        .form-container .btn-success {
            background-color: #F26D6D;
            border: none;
            padding: 12px 25px;
            font-size: 16px;
            border-radius: 5px;
        }
        .form-container .btn-secondary {
            background-color: #6c757d;
            border: none;
            padding: 12px 25px;
            font-size: 16px;
            border-radius: 5px;
        }
        .form-container .btn:hover {
            opacity: 0.9;
        }
        .alert {
            max-width: 600px;
            margin: 20px auto;
        }
    </style>
</head>
<body>
<?php include('navbar.html'); ?>

<main class="container mt-5">
   
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php elseif (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="profile-container">
        <img src="<?php echo !empty($admin['profile_picture']) ? 'uploads/' . $admin['profile_picture'] : '../img/about_1.jpg'; ?>" alt="Profile Picture">
        <h2><?php echo htmlspecialchars($admin['full_name']); ?></h2>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($admin['email']); ?></p>
        <button class="btn btn-warning" onclick="document.getElementById('editProfile').style.display='block'">Edit Profile</button>
    </div>

    <div id="editProfile" class="form-container" style="display: none;">
        <h4>Edit Profile</h4>
        <form action="a_profile.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="profilePic" class="form-label">Profile Picture</label>
                <input type="file" class="form-control" id="profilePic" name="profilePic">
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($admin['full_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($admin['contact_number']); ?>" required pattern="[0-9]{10,15}">
            </div>
            <button type="submit" class="btn btn-success">Save Changes</button>
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('editProfile').style.display='none'">Cancel</button>
        </form>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
