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


if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

   
    $delete_query = "DELETE FROM new_arrivals WHERE new_arrival_id = '$delete_id'";

    if (mysqli_query($conn, $delete_query)) {
 
        header('Location: new_arrival.php?status=deleted');
        exit;
    } else {

        echo "Error deleting record: " . mysqli_error($conn);
    }
}

$query = "SELECT na.new_arrival_id, p.product_name, p.p_image, na.arrival_date
          FROM new_arrivals na
          JOIN product p ON na.product_id = p.product_id";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Arrivals List</title>
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
                <h4>New Arrivals List</h4>
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

            <?php
         
            if (isset($_GET['status']) && $_GET['status'] == 'deleted') {
                echo '<div class="alert alert-success" role="alert">
                  New Arrival deleted successfully.
                </div>';
            }
            ?>

            <div class="order-list">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">New Arrivals ID</th>
                            <th scope="col">Product Name</th>
                            <th scope="col">Image</th>
                            <th scope="col">Arrivals Date</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <tr>
                                <td><?php echo $row['new_arrival_id']; ?></td>
                                <td><?php echo $row['product_name']; ?></td>
                                <?php
                                $image_url = !empty($row['p_image']) ? '../admin/' . $row['p_image'] : '../default.png';
                                echo "<td><img src='$image_url' alt='" . $row['product_name'] . "' class='img-fluid' style='max-width: 100px; height: auto;'></td>";
                                ?>
                                <td><?php echo $row['arrival_date']; ?></td>
                                <td>
                                <a href="view_new_arrival.php?id=<?php echo $row['new_arrival_id']; ?>" class="btn btn-info btn-sm">View</a>
                                <a href="new_arrival.php?delete_id=<?php echo $row['new_arrival_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this new arrival?');">Delete</a>
                                </td>

                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
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

<?php

mysqli_close($conn);
?>