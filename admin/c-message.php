<?php
session_start();

include '../connection/connection.php';

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


$query = "SELECT * FROM message ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $message_id = $_POST['message_id'];
    $reply = $_POST['reply'];

   
    $updateQuery = "UPDATE message SET reply = ? WHERE message_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $reply, $message_id);
    $stmt->execute();

    header("Location: " . $_SERVER['PHP_SELF']); 
    exit;
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Messages</title>
    <link rel="icon" href="../logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">

    <style>
        .send-btn {
            background-color: #D4AF37;
            color: #2a3d66;
            border: none;
            border-radius: 5px;
            padding: 6px 12px;
            font-size: 0.9rem;
            width: auto;
            transition: background-color 0.3s, transform 0.2s;
        }

        .send-btn:hover {
            background-color: #F26D6D;
            color: white;
            transform: scale(1.05);
        }

        .messages-section h4 {
            font-weight: bold;
            color: #2a3d66;
            margin-bottom: 30px;
        }

        .card-subtitle {
            color: #666;
        }

        .text-card-text {
            color: black !important;
            margin-top: 15px;
        }

        .customer-card-title {
            color: #666;
            margin-bottom: 30px;
        }

        .message {
            font-size: 7rem;
            color: black !important;
            padding: 10px;
            border-left: 4px solid #D4AF37;
            border-radius: 5px;
            margin-top: 30px;
            word-wrap: break-word;
        }
    </style>
</head>

<body>

    <?php include 'navbar.html'; ?>

    <main class="main-content">
        <div class="container-fluid">
            <div class="top-bar d-flex justify-content-between align-items-center mb-4">
                <h4>Customer Messages</h4>
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

            <div class="messages-section">
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="customer-card-title"> <?php echo htmlspecialchars($row['c_name']); ?> </h5>
                            <h6 class="card-subtitle text-muted">
                                <i class="bi bi-envelope"></i> <?php echo htmlspecialchars($row['c_email']); ?>
                            </h6>
                            <p class="message"> <?php echo htmlspecialchars($row['message']); ?> </p>

                            <?php if (!empty($row['reply'])) : ?>
                                <div class="alert alert-success mt-2">
                                    <strong>Reply:</strong> <?php echo htmlspecialchars($row['reply']); ?>
                                </div>
                            <?php else : ?>
                                <form action="c-message.php" method="POST">
                                    <input type="hidden" name="message_id" value="<?php echo htmlspecialchars($row['message_id']); ?>">
                                    <textarea class="form-control mb-2" rows="2" name="reply" placeholder="Write your reply here..." required></textarea>
                                    <button type="submit" class="send-btn btn-primary">Send Reply</button>
                                </form>

                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </main>

    <footer class="mt-4 text-center">
        © 2025 Gifty House. All Rights Reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>