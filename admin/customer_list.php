<?php
session_start();
include '../connection/connection.php';

if (isset($_GET['search']) && !empty($_GET['search'])) {
  $search = "%" . $_GET['search'] . "%";
  $sql = "SELECT * FROM users WHERE role = 'user' AND (full_name LIKE ? OR email LIKE ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $search, $search); 
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  $sql = "SELECT * FROM users WHERE role = 'user'";
  $result = $conn->query($sql);
}


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
  $user_id = $_GET['id'];
  $sql = "DELETE FROM users WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $user_id);

  if ($stmt->execute()) {
    header("Location: customer_list.php");
    exit();
  } else {
    echo "<script>alert('Error deleting user.');</script>";
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer List</title>
  <link rel="icon" href="../logo.png" type="image/png">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <link rel="stylesheet" href="css/styles.css">

</head>

<body>

  <?php include 'navbar.html'; ?>

  <main class="main-content">
    <div class="container-fluid">
      <div class="top-bar d-flex justify-content-between align-items-center mb-4">
        <h4>Customer List</h4>
        <form class="d-flex align-items-center" method="GET" action="customer_list.php">
          <input class="form-control" type="search" name="search" placeholder="Search by name or email" aria-label="Search"
            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
          <button class="btn btn-primary ms-2 search-btn" type="submit">Search</button>

          <!-- Admin Profile Dropdown -->
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

      <div class="table-responsive">

        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Address</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($result->num_rows > 0) {
              $counter = 1;
              while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $counter++ . "</td>";
                echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['contact_number']) . "</td>";
                echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                echo "<td>

                        <a href='c_view.php?id=" . $row['id'] . "' class='btn btn-info btn-sm'>
                          <i class='bi bi-eye'></i> View
                        </a>
                              
                        <a href='customer_list.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm ms-2' onclick='return confirm(\"Are you sure you want to delete this user?\")'>
                          <i class='bi bi-trash'></i> Delete
                        </a>
                     </td>";
                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='6' class='text-center'>No users found</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer>
    © 2025 Gifty House. All Rights Reserved.
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>