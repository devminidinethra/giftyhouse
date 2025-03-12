<?php
session_start();
require '../connection/connection.php';


if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
  header("Location: ../forms/login.php");
  exit();
}


$sql_admin = "SELECT full_name, profile_picture FROM users WHERE role = 'admin' LIMIT 1";
$result_admin = $conn->query($sql_admin);
$admin_name = 'Admin';
$admin_profile_pic = '../img/default-profile.jpg';
if ($result_admin->num_rows > 0) {
  $admin = $result_admin->fetch_assoc();
  $admin_name = htmlspecialchars($admin['full_name']);
  $admin_profile_pic = htmlspecialchars($admin['profile_picture']);
}

// Fetch total sales, orders, customers, and revenue
$sql_total_sales = "SELECT SUM(total_cost) AS total_sales FROM orders";
$result_total_sales = $conn->query($sql_total_sales);
$total_sales = $result_total_sales->num_rows > 0 ? $result_total_sales->fetch_assoc()['total_sales'] : 0;

$sql_total_orders = "SELECT COUNT(order_id) AS total_orders FROM orders";
$result_total_orders = $conn->query($sql_total_orders);
$total_orders = $result_total_orders->num_rows > 0 ? $result_total_orders->fetch_assoc()['total_orders'] : 0;

$sql_total_customers = "SELECT COUNT(DISTINCT id) AS total_customers FROM users WHERE role = 'user'";
$result_total_customers = $conn->query($sql_total_customers);
$total_customers = $result_total_customers->num_rows > 0 ? $result_total_customers->fetch_assoc()['total_customers'] : 0;

$sql_total_revenue = "SELECT SUM(product_price * product_quantity) AS total_revenue FROM order_item";
$result_total_revenue = $conn->query($sql_total_revenue);
$total_revenue = $result_total_revenue->num_rows > 0 ? $result_total_revenue->fetch_assoc()['total_revenue'] : 0;


$sql_messages = "SELECT user_id, c_email, created_at FROM message WHERE reply IS NULL OR reply = '' ORDER BY created_at DESC";
$result_messages = $conn->query($sql_messages);
$messages = [];
if ($result_messages->num_rows > 0) {
  while ($row = $result_messages->fetch_assoc()) {
    $messages[] = $row;
  }
}

$sql_products = "SELECT product_name, quantity FROM product WHERE quantity <= 5";
$result_products = $conn->query($sql_products);
$low_stock_products = [];
if ($result_products->num_rows > 0) {
  while ($row = $result_products->fetch_assoc()) {
    $low_stock_products[] = $row;
  }
}


if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
  session_unset();
  session_destroy();
  header('Location: ../forms/login.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Gifty House</title>
  <link rel="icon" href="../logo.png" type="image/png">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">


  <link rel="stylesheet" href="css/styles.css">

  <style>
    /* Modal Styles */
    #logoutModal {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      z-index: 1001;
      width: 400px;
      border-radius: 8px;
      padding: 30px;
      transform: translate(-50%, -50%);
    }

    .modal-content {
      background-color: #ffffff;
      border-radius: 8px;
      padding: 20px;
      text-align: center;
    }

    .modal-buttons {
      margin-top: 20px;
    }

    .btn-yes,
    .btn-no {
      padding: 10px 25px;
      margin: 6px;
      border: none;
      cursor: pointer;
      border-radius: 8px;
      font-size: 1.1rem;
    }

    .btn-yes {
      background-color: #D4AF37;
      color: #ffffff;
    }

    .btn-yes:hover {
      background-color: #B68C2A;
    }

    .btn-no {
      background-color: #F26D6D;
      color: #ffffff;
    }

    .btn-no:hover {
      background-color: #D14A4A;
    }

    .close {
      position: absolute;
      top: 10px;
      right: 10px;
      font-size: 1.7rem;
      color: #333;
      cursor: pointer;
    }

    .close:hover {
      color: #D4AF37;
    }

    .message_reply {
      color: #D14A4A;
      font-weight: bold;
      text-decoration: none;
      transition: color 0.3s ease, background-color 0.3s ease;
      padding: 5px 10px;
      border-radius: 4px;
    }

    .message_reply:hover {
      color: #ffffff;
      background-color: #D14A4A;
      text-decoration: none;
    }

    /* Add padding for the reply section inside the alert */
    .alert .message_reply {
      margin-left: auto;
      padding: 0.2rem 0.8rem;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .alert .message_reply:focus {
      outline: none;
      box-shadow: 0 0 5px rgba(210, 74, 74, 0.7);
    }
  </style>
</head>

<body>
  <?php include('navbar.html'); ?>

  <main class="main-content">
    <div class="container-fluid">
      <div class="top-bar d-flex justify-content-between align-items-center mb-4">
        <h4>Dashboard - <span class="fw-bold">Welcome, <?php echo $admin_name; ?></span></h4>


        <form>
          <div class="dropdown ms-3">
            <button class="btn btn-link p-0" type="button" id="adminProfile" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="uploads/<?php echo $admin_profile_pic; ?>" alt="Admin Profile" class="rounded-circle" width="55" height="55">
            </button>
            <ul class="dropdown-menu" aria-labelledby="adminProfile">
              <li class="dropdown-header"><strong><?php echo $admin_name; ?></strong></li>
              <li><a class="dropdown-item" href="a_profile.php"><i class="bi bi-person-fill"></i> Profile</a></li>
              <li><a class="dropdown-item" href="#" id="logout-btn"><i class="bi bi-box-arrow-right"></i> Logout</a>
              </li>
            </ul>
          </div>
        </form>
      </div>

      <div class="row">
        <div class="col-md-3 mb-3">
          <div class="card bg-primary text-white">
            <div class="card-body">
              <h5 class="card-title">Total Sales</h5>
              <h2>Rs. <?php echo number_format($total_sales, 2); ?></h2>
              <p>All time</p>
            </div>
          </div>
        </div>
        <div class="col-md-3 mb-3">
          <div class="card bg-warning text-white">
            <div class="card-body">
              <h5 class="card-title">Total Orders</h5>
              <h2><?php echo $total_orders; ?></h2>
              <p>All time</p>
            </div>
          </div>
        </div>
        <div class="col-md-3 mb-3">
          <div class="card bg-success text-white">
            <div class="card-body">
              <h5 class="card-title">Total Customers</h5>
              <h2><?php echo $total_customers; ?></h2>
              <p>Registered Users</p>
            </div>
          </div>
        </div>
        <div class="col-md-3 mb-3">
          <div class="card bg-danger text-white">
            <div class="card-body">
              <h5 class="card-title">Total Revenue</h5>
              <h2>Rs. <?php echo number_format($total_revenue, 2); ?></h2>
              <p>All time</p>
            </div>
          </div>
        </div>
      </div>


      <div class="row">
        <!-- To-Do List -->
        <div class="col-md-6">
          <div class="card">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0">Sales & Revenue Overview</h5>
            </div>
            <div class="card-body">
              <canvas id="salesChart"></canvas>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card notification-card">
            <div class="card-header bg-info text-white">
              <h5 class="mb-0">Notifications</h5>
            </div>
            <div class="card-body">
              <?php if (!empty($messages)) : ?>
                <?php foreach ($messages as $message) : ?>
                  <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="bi bi-envelope-fill me-2"></i>
                    <?php echo htmlspecialchars($message['c_email']); ?>
                    <a class="message_reply" href="c-message.php?id=<?php echo $message['user_id']; ?>" class="btn btn-link ms-auto">Reply</a>
                  </div>
                <?php endforeach; ?>
              <?php else : ?>
                <div class="alert alert-secondary" role="alert">
                  No new messages.
                </div>
              <?php endif; ?>
              <?php if (!empty($low_stock_products)) : ?>
                <?php foreach ($low_stock_products as $product) : ?>
                  <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-3" style="font-size: 1.5rem;"></i>
                    <div>
                      <strong><?php echo htmlspecialchars($product['product_name']); ?></strong>
                      <span class="badge bg-danger ms-2"><?php echo $product['quantity']; ?> left</span>
                      <div class="mt-1" style="font-size: 0.9rem; color: #6c757d;">
                        <i class="bi bi-info-circle-fill"></i> Stock is running low!
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else : ?>
                <div class="alert alert-secondary" role="alert">
                  No low stock products.
                </div>
              <?php endif; ?>

            </div>
          </div>
        </div>

  </main>

  <!-- Modal structure -->
  <div id="logoutModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <p>Do you want to log out?</p>
      <div class="modal-buttons">
        <button class="btn-yes" id="confirm-logout">Yes</button>
        <button class="btn-no" id="cancel-logout">No</button>
      </div>
    </div>
  </div>


  <footer>
    © 2025 Gifty House. All Rights Reserved.
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


  <script>
    var modal = document.getElementById("logoutModal");
    var logoutBtn = document.getElementById("logout-btn");
    var closeBtn = document.getElementsByClassName("close")[0];
    var cancelBtn = document.getElementById("cancel-logout");
    var confirmLogoutBtn = document.getElementById("confirm-logout");

    logoutBtn.onclick = function(event) {
      event.preventDefault();
      modal.style.display = "block";
    }

    closeBtn.onclick = function() {
      modal.style.display = "none";
    }

    cancelBtn.onclick = function() {
      modal.style.display = "none";
    }

    confirmLogoutBtn.onclick = function() {
      window.location.href = "?logout=true"; 
    }

    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }


    var ctx = document.getElementById('salesChart').getContext('2d');
    var salesChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Total Sales', 'Total Orders', 'Total Customers', 'Total Revenue'],
        datasets: [{
          label: 'Statistics',
          data: [<?php echo $total_sales; ?>, <?php echo $total_orders; ?>, <?php echo $total_customers; ?>, <?php echo $total_revenue; ?>],
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          borderColor: 'rgba(75, 192, 192, 1)',
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: true,
            position: 'top'
          }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  </script>
</body>

</html>