<?php 

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: forms/login.php");
    exit();
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

if (empty($cart)) {
    header("Location: home.php");
    exit();
}

// Calculate total price
$totalPrice = isset($_SESSION['total']) ? $_SESSION['total'] : 0;
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Gifty House</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .checkout-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }

        .checkout-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .checkout-header h2 {
            font-size: 3rem;
            color: #2c3e50;
            font-weight: 700;
        }

        .order-summary {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }

        .order-summary h3 {
            font-size: 1.7rem;
            color: #2c3e50;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .cart-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }

        .cart-item img {
            max-width: 120px;
            height: auto;
            border-radius: 8px;
            margin-right: 20px;
        }

        .cart-item-details {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .cart-item-details span {
            font-size: 1.1rem;
            color: #7f8c8d;
        }

        .cart-item-details .total {
            font-weight: bold;
            color: rgb(50, 89, 129);
        }

        .cart-item-details .price, .cart-item-details .quantity {
            color: #2c3e50;
        }

        .cart-item-details .price {
            font-weight: 600;
        }

        .checkout-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .btn {
            font-weight: 600;
            padding: 12px 30px;
            font-size: 1.2rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #f5d547;
            color: #2a3d66;
            border: none;
        }

        .btn-primary:hover {
            background-color: #F26D6D;
            color: white;
        }

        .btn-secondary {
            background-color: #e4e6e8;
            border: none;
            color: #333;
        }

        .btn-secondary:hover {
            background-color: #ccc;
        }

        .total-price {
            font-size: 1.4rem;
            font-weight: 700;
            color: #2c3e50;
        }

        .total-price h5 {
            font-size: 1.2rem;
            color: #7f8c8d;
            margin-top: 5px;
        }

      
        @media (max-width: 768px) {
            .checkout-container {
                padding: 15px;
            }

            .checkout-header h2 {
                font-size: 2.5rem;
            }

            .cart-item {
                flex-direction: column;
                align-items: center;
                margin-bottom: 15px;
            }

            .cart-item img {
                max-width: 100px;
            }

            .cart-item-details {
                flex-direction: column;
                align-items: flex-start;
                width: 100%;
            }

            .checkout-actions {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                max-width: 100%;
            }

            .total-price h3 {
                font-size: 1.5rem;
            }

            .total-price h5 {
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .checkout-header h2 {
                font-size: 2rem;
            }

            .order-summary {
                padding: 20px;
            }

            .total-price h3 {
                font-size: 1.4rem;
            }

            .total-price h5 {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
<div class="container checkout-container">
        <div class="checkout-header">
            <h2>Checkout</h2>
        </div>

        <div class="order-summary">
            <h3>Your Order</h3>
            <div class="cart-items">
                <?php if (!empty($cart)): ?>
                    <?php foreach ($cart as $item): ?>
                        <div class="cart-item">
                            <img src="admin<?= htmlspecialchars($item['p_image']); ?>" alt="<?= htmlspecialchars($item['product_name']); ?>">
                            <div class="cart-item-details">
                                <div>
                                    <span><strong><?= htmlspecialchars($item['product_name']); ?></strong></span><br>
                                    <span class="price">Rs. <?= number_format($item['price'], 2); ?></span> |
                                    <span class="quantity"><?= $item['quantity']; ?> pcs</span>
                                </div>
                                <div>
                                    <span class="total">Total: Rs. <?= number_format($item['price'] * $item['quantity'], 2); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Your cart is empty.</p>
                <?php endif; ?>
            </div>

            <hr>

            <div class="total-price">
                <h5>Total Price:</h5>
                <h3>Rs. <?= number_format($totalPrice, 2); ?></h3>
            </div>
        </div>
        
        <div class="checkout-actions">
            <?php if (!empty($cart)): ?>
                <a href="shipping.php" class="btn btn-primary">Proceed to Shipping</a>
            <?php endif; ?>
            <a href="cart.php" class="btn btn-secondary">Go Back to Cart</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>