<?php
session_start();

$paymentMethod = isset($_GET['method']) ? $_GET['method'] : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation - Gifty House</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .confirmation-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 40px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        .confirmation-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .confirmation-header h2 {
            font-size: 2.2rem;
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .confirmation-message {
            font-size: 1.3rem;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .confirmation-text {
            font-size: 1.1rem;
            color: #636e72;
            margin-bottom: 30px;
        }

        .btn-back {
            background-color: #f5d547;
            color: #2a3d66;
            border: none;
            padding: 12px 30px;
            font-size: 1.125rem;
            border-radius: 8px;
            width: 100%;
            transition: background-color 0.3s, color 0.3s;
        }

        .btn-back:hover {
            background-color: #F26D6D;
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .confirmation-container {
                padding: 25px;
            }

            .confirmation-header h2 {
                font-size: 1.8rem;
            }

            .confirmation-message {
                font-size: 1.25rem;
            }

            .confirmation-text {
                font-size: 1rem;
            }

            .btn-back {
                padding: 12px 25px;
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .confirmation-container {
                padding: 15px;
            }

            .confirmation-header h2 {
                font-size: 1.8rem;
            }

            .confirmation-message {
                font-size: 1.2rem;
            }

            .confirmation-text {
                font-size: 0.9rem;
            }

            .btn-back {
                font-size: 0.95rem;
            }
        }
    </style>
</head>

<body>
    <div class="container confirmation-container">
        <div class="confirmation-header">
            <h2>Payment Confirmation</h2>
        </div>

        <div class="confirmation-message">
            <?php
            if ($paymentMethod == 'cash') {
                echo "Your payment method is <strong>Cash on Delivery</strong>.";
            } elseif ($paymentMethod == 'card') {
                echo "Your payment method is <strong>Card Payment</strong>.";
            } else {
                echo "Payment method not recognized.";
            }
            ?>
        </div>

        <div class="confirmation-text">
            Thank you for shopping with us! Your order is now confirmed, and we will process it shortly. We appreciate your business!
        </div>

        <div class="text-center">
            <a href="home.php" class="btn btn-back">Back to Home</a>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
