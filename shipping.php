<?php

session_start();

include 'connection/connection.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    header("Location: payment.php");
    exit();
}



if (isset($_SESSION['user_id'])) {

    $user_id = $_SESSION['user_id'];
    $query = "SELECT full_name, email FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

   
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $full_name = $user['full_name'];
        $email = $user['email'];
    } else {
        $full_name = '';
        $email = '';
    }
} else {
    $full_name = '';
    $email = ''; 
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Details - Gifty House</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
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

        .shipping-container {
            max-width: 700px;
            margin: 50px auto;
            padding: 20px;
        }

        .shipping-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .shipping-header h2 {
            font-size: 2.5rem;
            color: #2c3e50;
            font-weight: 700;
        }

        .shipping-details-form {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .shipping-details-form .form-label {
            font-weight: 600;
            color: #2c3e50;
        }

        .checkout-actions .btn {
            font-weight: 600;
            padding: 12px 30px;
            font-size: 1.2rem;
            border-radius: 8px;
        }

        .to-pay-btn {
            background-color: #f5d547;
            color: #2a3d66;
            border: none;
        }

        .to-pay-btn:hover {
            background-color: #F26D6D;
            color: white;
        }

        @media (max-width: 768px) {
            .shipping-container {
                padding: 15px;
            }

            .shipping-header h2 {
                font-size: 2rem;
            }

            .shipping-details-form {
                padding: 20px;
            }

            .checkout-actions .btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .shipping-header h2 {
                font-size: 1.8rem;
            }

            .shipping-details-form {
                padding: 15px;
            }
        }
    </style>
</head>

<body>

    <a href="checkout.php" class="back-button" title="Go Back">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div class="container shipping-container">
        <div class="shipping-header">
            <h2>Shipping Details</h2>
            <p class="text-muted">Fill in your shipping details to proceed to payment.</p>
        </div>

        <div class="shipping-details-form">
            <form method="POST" action="server/place_order.php" name="form-gro">
                <div class="mb-3">
                    <label for="full-name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="full-name" name="full_name" placeholder="Enter Your Full Name" value="<?php echo htmlspecialchars($full_name); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter Your Email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>

                <div class="mb-3">
                    <input type="checkbox" id="deliver-to-other" name="deliver_to_other" onclick="toggleDeliveryFields()">
                    <label for="deliver-to-other" class="form-label">Deliver to another user</label>
                </div>

                <div id="delivery-user-fields" style="display: none;">
                    <div class="mb-3">
                        <label for="other-full-name" class="form-label">Other User's Full Name</label>
                        <input type="text" class="form-control" id="other-full-name" name="other_full_name" placeholder="Enter Other User's Full Name">
                    </div>
                    <div class="mb-3">
                        <label for="other-email" class="form-label">Other User's Email</label>
                        <input type="email" class="form-control" id="other-email" name="other_email" placeholder="Enter Other User's Email">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Contact Number</label>
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter Your Contact Number" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Shipping Address</label>
                    <input type="text" class="form-control" id="address" name="address" placeholder="Enter Your Address" required>
                </div>
                <div class="mb-3">
                    <label for="user_province" class="form-label">Province</label>
                    <select class="form-control" id="user_province" name="user_province" required>
                        <option value="" disabled selected>Select Your Province</option>
                        <option value="Western" <?php echo (isset($_POST['user_province']) && $_POST['user_province'] == 'Western') ? 'selected' : ''; ?>>Western Province</option>
                        <option value="Other" <?php echo (isset($_POST['user_province']) && $_POST['user_province'] == 'Other') ? 'selected' : ''; ?>>Other Province</option>
                    </select>
                </div>



                <div class="checkout-actions mt-4">
                    <input type="submit" class="to-pay-btn btn" name="Proceed_to_Payment" value="Proceed to Payment">
                </div>
            </form>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleDeliveryFields() {
            var checkbox = document.getElementById('deliver-to-other');
            var deliveryFields = document.getElementById('delivery-user-fields');
            if (checkbox.checked) {
                // Show additional fields for the other user's delivery details
                deliveryFields.style.display = 'block';
            } else {
                // Hide additional fields
                deliveryFields.style.display = 'none';
            }
        }
    </script>

</body>

</html>