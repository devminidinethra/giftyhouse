<?php
include 'connection/connection.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = []; 
}
// Initialize default variables
$user_name = "Guest";
$profile_picture = 'img/profile.jpg';


if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];


    $query = "SELECT full_name, profile_picture FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($db_user_name, $db_profile_picture);

    if ($stmt->fetch()) {
        $user_name = $db_user_name;
        $profile_picture = $db_profile_picture ? $db_profile_picture : 'img/profile.jpg';
    }
    $stmt->close();
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gifty House</title>
    <link rel="icon" href="logo.png" type="image/png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Navbar Styles */
        .navbar {
            background: linear-gradient(90deg, #D3D3D3, #CFCFCF);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            padding: 15px 20px;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .navbar-brand img {
            height: 60px;
            width: auto;
        }

        .navbar-brand span {
            font-weight: 700;
            font-size: 1.8rem;
            color: #2A3D66;
        }

        .navbar-nav {
            margin: 0 auto;
            display: flex;
            gap: 25px;
        }

        .nav-link {
            color: #2A3D66;
            font-size: 1.2rem;
            font-weight: 600;
            padding: 8px 12px;
            border-radius: 5px;
            transition: color 0.3s ease, transform 0.2s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #D4AF37;
            transform: scale(1.05);
        }

        .cart-icon {
    font-size: 1.8rem;
    color: #2A3D66;
    margin-left: 30px;
    transition: color 0.3s ease;
    padding-right: 20px !important;
    position: relative;
}

.cart-icon span {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: #D4AF37;
    color: white;
    font-size: 0.9rem;
    padding: 5px 10px;
    border-radius: 50%;
    font-weight: bold;
}


        .cart-icon:hover {
            color: #D4AF37;
        }

        
        /* Dropdown Styling */
        .dropdown-menu {
            border-radius: 8px;
            border: 1px solid #D4AF37;
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            min-width: 240px;
        }

        .dropdown-item {
            font-size: 1.1rem;
            color: #333;
            padding: 12px;
            transition: background-color 0.3s ease, color 0.3s ease;
            border-radius: 6px;
        }

        .dropdown-item:hover,
        .dropdown-item:focus {
            background-color: #D4AF37;
            color: #ffffff;
        }

        .dropdown-item i {
            font-size: 1.3rem;
            margin-right: 12px;
        }

        .dropdown-toggle {
            display: flex;
            align-items: center;
            color: #2A3D66;
            font-weight: 600;
            font-size: 1.1rem;
            gap: 10px;
        }

        .dropdown-toggle:hover {
            color: #D4AF37;
        }

        .profile-pic {
            width: 55px;
            height: 55px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #D4AF37;
            transition: transform 0.3s ease;
        }

        .profile-pic:hover {
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .navbar-nav {
                flex-direction: column;
                gap: 12px;
            }

            .search-input {
                margin-top: 15px;
                width: 100%;
            }

            .cart-icon {
                margin-left: 0;
                margin-top: 15px;
            }
        }

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
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="logo.png" alt="Gifty House Logo">
                <span>Gifty House</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="shop.php">Shop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                </ul>

                <a href="cart.php" class="cart-icon" aria-label="View Cart">
    <i class="bi bi-cart">
        <?php if(isset($_SESSION['quantity']) && $_SESSION['quantity'] > 0) { ?>
            <span><?php echo $_SESSION['quantity']; ?></span>
        <?php } ?>
    </i>
</a>


                <!-- Dropdown for Customer's Name and Picture -->
                <div class="dropdown ms-3">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="profile-pic">
                        <span class="ms-2"><?php echo htmlspecialchars($user_name); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li>
                                <a class="dropdown-item" href="profile.php">
                                    <i class="bi bi-person-circle"></i> My Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="order.php">
                                    <i class="bi bi-bag-check"></i> My Orders
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" id="logout-btn">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                            </li>
                        <?php else: ?>
                            <li>
                                <a class="dropdown-item" href="forms/login.php">
                                    <i class="bi bi-box-arrow-in-right"></i> Login
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="forms/register.php">
                                    <i class="bi bi-person-plus"></i> Register
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
    </script>
</body>

</html>