<?php
session_start();
include 'connection/connection.php';

if (!isset($_SESSION['user_id'])) {
  die("User not logged in");
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT full_name, email, contact_number, address, country, profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
  $user = $result->fetch_assoc();
} else {
  die("User data not found.");
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $full_name = trim($_POST["name"]);
  $email = trim($_POST["email"]);
  $contact_number = trim($_POST["phone"]);
  $address = trim($_POST["address"]);
  $country = trim($_POST["country"]);
  $profile_picture = $user['profile_picture'];

  // validation
  if (empty($full_name) || !preg_match("/^[a-zA-Z ]+$/", $full_name)) {
    $errors[] = "Invalid name format. Only letters and spaces are allowed.";
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
  }
  if (!preg_match("/^[0-9]{10,15}$/", $contact_number)) {
    $errors[] = "Invalid phone number. Only 10-15 digits allowed.";
  }
  if (empty($address)) {
    $errors[] = "Address cannot be empty.";
  }
  if (empty($country) || !preg_match("/^[a-zA-Z ]+$/", $country)) {
    $errors[] = "Invalid country name.";
  }

  // Handle profile picture upload
  if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['profile_picture']['type'], $allowed_types)) {
      $errors[] = "Invalid image format. Only JPG, PNG, and GIF are allowed.";
    } else {
      $target_dir = "profile_pictures/";
      if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
      }

      $image_file = basename($_FILES["profile_picture"]["name"]);
      $image_path = $target_dir . "profile_" . time() . "_" . $image_file;

      if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $image_path)) {
        $profile_picture = $image_path;
      } else {
        $errors[] = "Failed to upload profile picture.";
      }
    }
  }

  if (empty($errors)) {
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, contact_number = ?, address = ?, country = ?, profile_picture = ? WHERE id = ?");

    if (!$stmt) {
      die("SQL Prepare Error: " . $conn->error);
    }

    $stmt->bind_param("ssssssi", $full_name, $email, $contact_number, $address, $country, $profile_picture, $user_id);

    if ($stmt->execute()) {
      echo "Profile updated successfully!";
      header("Location: profile.php");
      exit;
    } else {
      die("Error updating profile: " . $stmt->error);
    }

    $stmt->close();
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Profile</title>
  <link rel="icon" href="logo.png" type="image/png">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

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

    .edit-profile-card {
      background-color: #ffffff;
      border-radius: 15px;
      padding: 30px;
      box-shadow: rgba(0, 0, 0, 0.1) 0px 10px 30px;
      margin-top: 50px;
    }

    .edit-profile-card h2 {
      font-size: 2rem;
      margin-bottom: 20px;
      color: #333333;
      text-align: center;
    }

    .form-label {
      font-weight: 600;
      color: #333333;
    }

    .btn-save {
      background: #D4AF37 !important;
      color: #ffffff !important;
      border-radius: 8px;
      padding: 12px 30px;
      font-size: 1.1rem;
      font-weight: 600;
      text-decoration: none;
      display: block;
      width: 100%;
      text-align: center;
      transition: all 0.3s ease;
      border: none;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-save:hover {
      background: #F26D6D !important;
      color: #ffffff !important;
      transform: translateY(-2px);
      box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
    }

    .profile-img-preview {
      width: 140px;
      height: 140px;
      border-radius: 50%;
      object-fit: cover;
      display: block;
      margin: 0 auto 20px auto;
      border: 5px solid #198754;
      transition: transform 0.3s ease;
    }

    .profile-img-preview:hover {
      transform: scale(1.1);
    }

    .form-control {
      border-radius: 8px;
      padding: 12px;
      border: 1px solid #ced4da;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-control:focus {
      border-color: #198754;
      box-shadow: 0 0 8px rgba(25, 135, 84, 0.25);
    }
  </style>
</head>

<body>

  <!-- Back Button -->
  <a href="profile.php" class="back-button" title="Go Back">
    <i class="bi bi-arrow-left"></i>
  </a>

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="edit-profile-card">
          <h2>Edit Profile</h2>


          <form method="POST" action="#" enctype="multipart/form-data">
            <?php if (!empty($errors)): ?>
              <div class="alert alert-danger">
                <ul>
                  <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>
            <img src="<?php echo !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : 'img/profile.jpg'; ?>"
              alt="Profile Picture"
              class="profile-img-preview">

            <div class="mb-3">
              <label for="profile_picture" class="form-label">Update Profile Picture</label>
              <input type="file" class="form-control" id="profile_picture" name="profile_picture">
            </div>
            <div class="mb-3">
              <label for="name" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['full_name']); ?>" placeholder="Enter Your Name" required>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="Enter Your Email" required>
            </div>

            <div class="mb-3">
              <label for="phone" class="form-label">Phone Number</label>
              <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['contact_number']); ?>" placeholder="Enter Your Contact Number" required>
            </div>

            <div class="mb-3">
              <label for="address" class="form-label">Address</label>
              <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" placeholder="Enter Your Shipping Address" required>
            </div>

            <div class="mb-3">
              <label for="country" class="form-label">Country</label>
              <input type="text" class="form-control" id="country" name="country" value="<?php echo htmlspecialchars($user['country']); ?>" placeholder="Enter Your Country" required>
            </div>

            <button type="submit" class="btn btn-save">Save Changes</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>