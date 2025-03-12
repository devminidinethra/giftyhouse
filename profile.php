<?php
session_start();
include 'connection/connection.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: forms/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle logout request
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_unset();
    session_destroy();
    header('Location: forms/login.php');
    exit;
}

// Check if deletion is confirmed
if (isset($_GET['confirm_delete']) && $_GET['confirm_delete'] == 'true') {
    $sql = "DELETE FROM users WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            session_unset();
            session_destroy();
            header("Location: delete_account.php");
            exit;
        } else {
            echo "Error deleting account: " . $conn->error;
        }
    } else {
        echo "Database error: " . $conn->error;
    }
}

// Fetch user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    header('Location: error.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Profile</title>
  <link rel="icon" href="logo.png" type="image/png">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --dark-navy-blue: #1d2a44;
      --yellow: #D4AF37;
      --accent-green: #198754;
      --error-red: #F26D6D;
      --dark-gray: #343a40;
      --light-gray: #f8f9fa;
      --white: #ffffff;
      --light-blue: #a3d8f4;
      --card-shadow: rgba(0, 0, 0, 0.2) 0px 10px 30px;
      --hover-shadow: rgba(0, 0, 0, 0.3) 0px 20px 40px;
    }

    .sidebar {
      height: 100vh;
      width: 250px;
      background-color: var(--dark-navy-blue);
      color: var(--white);
      padding-top: 20px;
      position: fixed;
      top: 0;
      left: 0;
      box-shadow: 3px 0 6px rgba(0, 0, 0, 0.2);
      margin-top: 80px;
    }

    .sidebar a {
      color: var(--light-blue);
      padding: 15px;
      text-decoration: none;
      display: block;
      font-size: 1.1rem;
      border-bottom: 1px solid var(--dark-gray);
      transition: background-color 0.3s ease;
    }

    .sidebar a:hover {
      background-color: var(--yellow);
      color: var(--white);
    }

    .profile-card {
      margin-left: 270px;
      box-shadow: var(--card-shadow);
      background-color: var(--dark-navy-blue);
      border-radius: 20px;
      padding: 35px;
      text-align: center;
      margin-top: 30px;
      margin-bottom: 30px;
      color: var(--dark-navy-blue);
      transition: transform 0.3s ease-in-out;
    }

    .profile-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--hover-shadow);
    }

    .profile-header h2 {
      font-size: 2.8rem;
      font-weight: 700;
      margin-bottom: 15px;
      color: #ffffff;
    }

    .profile-header p {
      color: var(--light-blue);
      font-size: 1.2rem;
      margin-bottom: 25px;
    }

    .profile-img {
      width: 160px;
      height: 160px;
      border-radius: 50%;
      object-fit: cover;
      border: 6px solid var(--yellow);
      margin-bottom: 20px;
      box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease, border 0.3s ease;
    }

    .profile-img:hover {
      transform: scale(1.1);
      border: 6px solid var(--accent-green);
    }

    .contact-info h5,
    .address-section h5 {
      font-size: 1.4rem;
      font-weight: 600;
      margin-bottom: 10px;
    }

    .contact-info p,
    .address-section p {
      color: var(--dark-gray);
      font-size: 1.1rem;
      margin-bottom: 10px;
    }

    .contact-info,
    .address-section {
      background-color: #f8f9fa;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 20px;
      box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
    }

    .contact-info i,
    .address-section i {
      color: var(--accent-green);
      font-size: 1.4rem;
      margin-right: 10px;
    }

    .error-message {
      color: var(--error-red);
      font-size: 1.2rem;
      margin-top: 15px;
    }

    @media (max-width: 768px) {
      .profile-card {
        padding: 25px 30px;
      }

      .profile-header h2 {
        font-size: 2.5rem;
      }

      .profile-img {
        width: 140px;
        height: 140px;
      }

      .contact-info h5,
      .address-section h5 {
        font-size: 1.3rem;
      }

      .contact-info p,
      .address-section p {
        font-size: 1.1rem;
      }
    }

    @media (max-width: 576px) {
      .profile-header p {
        font-size: 1.1rem;
      }

      .profile-img {
        width: 120px;
        height: 120px;
      }

      .contact-info h5,
      .address-section h5 {
        font-size: 1.2rem;
      }

      .contact-info p,
      .address-section p {
        font-size: 1rem;
      }
    }
  </style>
</head>

<body>
  <?php include('navbar.php'); ?>

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="text-center">
      <img src="<?php echo !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : 'img/profile.jpg'; ?>" alt="Profile Picture" class="profile-img">
      <h4><?php echo htmlspecialchars($user['full_name']); ?></h4>
      <p><?php echo htmlspecialchars($user['email']); ?></p>
    </div>
    <a href="update.php">Edit Profile</a>
    <a href="order.php">My Orders</a>
    <a href="pending_order.php">Pending Orders</a>
    <a href="message.php">Message</a>
    <a href="#"  data-bs-toggle="modal" data-bs-target="#deleteModal">Delete Account</a>

  </div>
  <!-- Profile Section -->
<div class="container mt-5" id="my-profile">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="profile-card">
                <div class="profile-header">
                    <img src="<?php echo !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : 'img/profile.jpg'; ?>" 
                         alt="Profile Picture" class="profile-img">
                    <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
                    <p>Manage your personal information and shipping details</p>
                </div>

                <div class="contact-info mb-4">
                    <h5><i class="bi bi-envelope"></i> Email</h5>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                    <h5><i class="bi bi-phone"></i> Phone</h5>
                    <p><?php echo !empty($user['contact_number']) ? htmlspecialchars($user['contact_number']) : 'Not Updated'; ?></p>
                </div>

                <div class="address-section">
                    <h5><i class="bi bi-house-door"></i> Shipping Address</h5>
                    <p><?php echo !empty($user['address']) ? htmlspecialchars($user['address']) : 'Not Updated'; ?></p>
                    <?php if (!empty($user['country'])): ?>
                        <p><?php echo htmlspecialchars($user['country']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>


  <!-- Modal for Confirming Account Deletion -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete your account? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="?confirm_delete=true" class="btn btn-danger">Confirm Delete</a>
            </div>
        </div>
    </div>
</div>

  </div>

  <!-- Bootstrap JS and dependencies -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
