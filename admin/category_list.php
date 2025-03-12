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
  $admin_profile_pic = '../img/profile.jpg'; 
}


if (isset($_GET['id'])) {
  $category_id = $_GET['id'];


  $sql = "DELETE FROM category WHERE category_id = $category_id";
  if ($conn->query($sql) === TRUE) {
    header("Location: category_list.php?status=deleted");
    exit;
  } else {
    echo "Error deleting category: " . $conn->error;
  }
}


// Handle category search
$search = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
  $search = "%" . $_GET['search'] . "%"; 
  $sql = "SELECT category_id, category_name, c_description, c_picture 
          FROM category 
          WHERE category_name LIKE ? OR c_description LIKE ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $search, $search);
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  $sql = "SELECT category_id, category_name, c_description, c_picture FROM category";
  $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Category List</title>
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
        <h4>Category List</h4>
        <form class="d-flex align-items-center" method="GET" action="category_list.php">
          <input class="form-control" type="search" name="search" placeholder="Search category..." 
                 aria-label="Search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
          <button class="btn btn-primary ms-2 search-btn" type="submit">Search</button>


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

      <?php
   
      if (isset($_GET['status']) && $_GET['status'] == 'deleted') {
        echo '<div class="alert alert-success" role="alert">
                  Category deleted successfully.
                </div>';
      }
      ?>

      <div class="category-section">
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">Category ID</th>
              <th scope="col">Image</th>
              <th scope="col">Category Name</th>
              <th scope="col">Description</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['category_id'] . "</td>";

                $image_url = !empty($row['c_picture']) ? '../admin/' . $row['c_picture'] : '../default.png';

                echo "<td><img src='$image_url' alt='" . $row['category_name'] . "' class='img-fluid' style='max-width: 100px; height: auto;'></td>";
                echo "<td>" . $row['category_name'] . "</td>";
                echo "<td>" . $row['c_description'] . "</td>";

                echo "<td>";
                echo "<div class='d-inline-flex'>";
                echo "<a href='view_category.php?category_id=" . $row['category_id'] . "' class='btn btn-info btn-sm me-2'>View</a>";

                echo "<a href='edit_category.php?id=" . $row['category_id'] . "' class='btn btn-warning btn-sm me-2'>Edit</a>";
                echo "<a href='category_list.php?id=" . $row['category_id'] . "' class='btn btn-danger btn-sm'>Delete</a>";
                echo "</div>";
                echo "</td>";

                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='5' class='text-center'>No categories found</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <footer>
    © 2025 Gifty House. All Rights Reserved.
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>

<?php

$conn->close();
?>