<?php
session_start();
include 'connection/connection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $paymentMethod = $_POST['payment_method'];
    $orderId = $_SESSION['order_id']; 

   
    $paymentStatus = ($paymentMethod === 'cash') ? 'Not Paid' : 'Paid';

    
    $sql = "UPDATE order_item SET payment_status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $paymentStatus, $orderId);
    
    if ($stmt->execute()) {

        header("Location: confirmation.php?method=$paymentMethod");
        exit();
    } else {
        echo "Error updating payment status: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Gifty House</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
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

        .payment-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }

        .payment-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .payment-header h2 {
            font-size: 2.8rem;
            color: #2c3e50;
            font-weight: 700;
        }

        .payment-options {
            background-color: #fff;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        .form-check-label {
            font-weight: 600;
            color: #2c3e50;
        }

        .card-details {
            display: none;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease-in-out;
            padding: 30px;
            background-color: #F7E7CE;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .card-details.show {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .card-details h5 {
            font-size: 1.8rem;
            color: #2A3D66;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .card-details .form-label {
            font-weight: 600;
            color: #636E72;
            margin-bottom: 8px;
        }

        .card-details .form-control {
            border-radius: 12px;
            border: 2px solid #D1D5DB;
            padding: 14px;
            margin-bottom: 20px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
            background-color: #fff;
        }

        .card-details .form-control:focus {
            border-color: #D4AF37;
            box-shadow: 0 0 8px #D4AF37;
        }

        .card-details .form-control::placeholder {
            color: #B0B0B0;
        }

        .card-details .form-control:focus::placeholder {
            color: #636E72;
        }

        .card-details .row {
            margin-bottom: 25px;
        }

        .card-details .btn-primary {
            background-color: #D4AF37;
            color: #2A3D66;
            border-radius: 12px;
            font-weight: 600;
            padding: 15px 35px;
            font-size: 1.2rem;
            width: 100%;
            transition: background-color 0.3s, color 0.3s;
        }

        .card-details .btn-primary:hover {
            background-color: #F26D6D;
            color: white;
        }

        .btn-primary {
            background-color: #F5D547;
            color: #2A3D66;
            border: none;
        }

        .btn-primary:hover {
            background-color: #F26D6D;
            color: white;
        }

        @media (max-width: 768px) {
            .payment-container {
                padding: 15px;
            }

            .payment-header h2 {
                font-size: 2.2rem;
            }

            .payment-options {
                padding: 25px;
            }

            .btn-primary {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <!-- Back Button -->
    <a href="to_pay.php" class="back-button" title="Go Back">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div class="container payment-container">
        <div class="payment-header">
            <h2>Choose Your Payment Method</h2>
        </div>

        <div class="payment-options">
            <form method="POST">
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="cash" value="cash" required>
                        <label class="form-check-label" for="cash">Cash on Delivery</label>
                    </div>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="radio" name="payment_method" id="card" value="card">
                        <label class="form-check-label" for="card">Card Payment</label>
                    </div>
                </div>

                <!-- Card Details Section -->
                <div class="card-details mt-4">
                    <h5 class="mb-3">Enter Card Details</h5>
                    <div class="mb-3">
                        <label for="card-name" class="form-label">Name on Card</label>
                        <input type="text" class="form-control" id="card-name" name="card_name" placeholder="DDH Gamage">
                    </div>
                    <div class="mb-3">
                        <label for="card-number" class="form-label">Card Number</label>
                        <input type="text" class="form-control" id="card-number" name="card_number" placeholder="1234 5678 9123 4567">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="expiry-date" class="form-label">Expiry Date</label>
                            <input type="text" class="form-control" id="expiry-date" name="expiry_date" placeholder="MM/YY">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cvv" class="form-label">CVV</label>
                            <input type="text" class="form-control" id="cvv" name="cvv" placeholder="123">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-4">
                    <input type="submit" class="btn btn-primary" value="Pay Now">
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show card details with animation when "Card Payment" is selected
        const cardOption = document.getElementById('card');
        const cashOption = document.getElementById('cash');
        const cardDetails = document.querySelector('.card-details');

        cardOption.addEventListener('change', () => {
            cardDetails.classList.add('show');
        });

        cashOption.addEventListener('change', () => {
            cardDetails.classList.remove('show');
        });
    </script>
</body>

</html>