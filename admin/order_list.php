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


$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$query = "
    SELECT o.order_id, u.full_name, o.total_cost, o.order_status, o.order_date
    FROM orders o
    JOIN users u ON o.user_id = u.id";
if (!empty($search)) {
    $query .= " WHERE (o.order_id LIKE '%$search%' OR u.full_name LIKE '%$search%' OR o.order_status LIKE '%$search%')";
}

$result = $conn->query($query);

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
  $order_id = $_POST['order_id'];
  $update_query = "UPDATE orders SET order_status = 'Shipping' WHERE order_id = ?";
  $stmt = $conn->prepare($update_query);
  $stmt->bind_param('i', $order_id);
  $stmt->execute();
  header("Location: " . $_SERVER['PHP_SELF']);
  exit();
}

//  order deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order_id'])) {
  $delete_order_id = $_POST['delete_order_id'];
  $delete_query = "DELETE FROM orders WHERE order_id = ?";
  $stmt = $conn->prepare($delete_query);
  $stmt->bind_param('i', $delete_order_id);
  $stmt->execute();
  header("Location: " . $_SERVER['PHP_SELF']);
  exit();
}

$orders = [];
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
  }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order List</title>
  <link rel="icon" href="../logo.png" type="image/png">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css">

  <style>
    .order-list .table th {
      background-color: rgb(159, 146, 110);
      color: white;
      text-align: center;
      font-size: 1.1rem;
    }

    .order-list .table td {
      text-align: center;
      vertical-align: middle;
    }

    .order-list .table td .btn {
      margin: 0 5px;
      font-size: 0.9rem;
    }


    .table .btn-shipping {
      color: #ff8c00 !important;
      border-color: #ff8c00 !important;
    }

    .table .btn-shipping:hover {
      color: #f8b400 !important;
      border-color: #e07b00 !important;
    }

    .table-responsive {
      margin-top: 30px;
    }

    .table {
      border-radius: 8px;
      overflow: hidden;
    }
  </style>
</head>

<body>

  <?php include 'navbar.html'; ?>

  <main class="main-content">
    <div class="container-fluid">
      <div class="top-bar d-flex justify-content-between align-items-center mb-4">
        <h4>Order List</h4>
        <form class="d-flex align-items-center" method="GET">
          <input class="form-control" type="search" name="search" placeholder="Search by Customer Name or Order ID or Order Status" aria-label="Search" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
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


      <div class="order-list">
        <div class="table-responsive">
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th scope="col">Order ID</th>
                <th scope="col">Customer Name</th>
                <th scope="col">Price</th>
                <th scope="col">Order Date</th>
                <th scope="col">Order Status</th>
                <th scope="col">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                  <tr>
                    <td><?php echo $order['order_id']; ?></td>
                    <td><?php echo $order['full_name']; ?></td>
                    <td>Rs. <?php echo $order['total_cost']; ?></td>
                    <td><?php echo date("F j, Y", strtotime($order['order_date'])); ?></td>
                    <td><?php echo $order['order_status']; ?></td>
                    <td>
                      <?php if ($order['order_status'] != 'Shipping'): ?>
                        <form method="POST" style="display:inline;">
                          <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                          <button type="submit" class="btn btn-shipping btn-sm">Mark as Shipping</button>
                        </form>
                      <?php else: ?>
                        <button class="btn btn-secondary btn-sm" disabled>Shipping</button>
                      <?php endif; ?>
                      <a href="view_order.php?id=<?php echo $order['order_id']; ?>" class="btn btn-info btn-sm">View</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center text-danger fw-bold">No orders found</td>
                </tr>
              <?php endif; ?>
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