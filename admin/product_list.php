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

// Check if a search query is provided
$search_query = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
  $search_query = "%" . $_GET['search'] . "%"; 
  $sql = "SELECT product_id, category_name, product_name, quantity, p_image, p_description, price 
          FROM product 
          WHERE product_name LIKE ? OR category_name LIKE ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $search_query, $search_query);
  $stmt->execute();
  $result = $stmt->get_result();
} else {

  $sql = "SELECT product_id, category_name, product_name, quantity, p_image, p_description, price FROM product";
  $result = $conn->query($sql);
}


if (isset($_GET['id'])) {
  $product_id = $_GET['id'];

  $sql = "DELETE FROM product WHERE product_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $product_id);
  if ($stmt->execute()) {
    header("Location: product_list.php?status=deleted");
    exit;
  } else {
    echo "Error deleting Product: " . $stmt->error;
  }

  $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product List</title>
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
        <h4>Product List</h4>
        <form>
          <form class="d-flex" method="GET" action="product_list.php">
            <input class="form-control me-2" type="search" name="search" placeholder="Search by Product or Category"
              value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
              aria-label="Search">
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
                  Product deleted successfully.
                </div>';
      }
      ?>

      <div class="container mt-4">
        <div class="table-responsive">
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Product ID</th>
                <th>Product Image</th>
                <th>Product Name</th>
                <th>Product Quantity</th>
                <th>Category Name</th>
                <th>Product Description</th>
                <th>Price</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  echo "<tr>";
                  echo "<td>" . htmlspecialchars($row['product_id']) . "</td>";

                  $image_url = !empty($row['p_image']) ? '../admin/' . htmlspecialchars($row['p_image']) : '../default.png';

                  echo "<td><img src='$image_url' alt='" . htmlspecialchars($row['product_name']) . "' class='img-fluid' style='max-width: 100px; height: auto;'></td>";
                  echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['p_description']) . "</td>";
                  echo "<td> Rs." . htmlspecialchars($row['price']) . "</td>";

                  echo "<td>";
                  echo "<div class='d-inline-flex'>";
                  echo "<a href='view_product.php?id=" . htmlspecialchars($row['product_id']) . "' class='btn btn-info btn-sm me-2'>View</a>";

                  echo "<a href='edit_product.php?id=" . htmlspecialchars($row['product_id']) . "' class='btn btn-warning btn-sm me-2'>Edit</a>";
                  echo "<a href='product_list.php?id=" . htmlspecialchars($row['product_id']) . "' class='btn btn-danger btn-sm'>Delete</a>";
                  echo "</div>";
                  echo "</td>";

                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='7' class='text-center'>No Product found</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <footer>
    © 2025 Gifty House. All Rights Reserved.
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>