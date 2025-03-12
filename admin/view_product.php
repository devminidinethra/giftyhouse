<?php
require '../connection/connection.php';


if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    $sql = "SELECT p.product_id, p.product_name, p.p_image, p.p_image2, p.p_image3, p.p_image4, c.category_name 
            FROM product p
            JOIN category c ON p.category_id = c.category_id
            WHERE p.product_id = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $product_id);
    if (!$stmt->execute()) {
        die("Execution failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $product = $result->fetch_assoc();



    $order_sql = "SELECT u.full_name AS customer_name, u.email AS customer_email, u.address AS customer_address, oi.product_quantity AS order_quantity 
FROM order_item oi
JOIN orders o ON oi.order_id = o.order_id
JOIN users u ON o.user_id = u.id
WHERE oi.product_id = ?";


    $order_stmt = $conn->prepare($order_sql);
    if (!$order_stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $order_stmt->bind_param("s", $product_id);
    if (!$order_stmt->execute()) {
        die("Execution failed: " . $order_stmt->error);
    }

    $order_result = $order_stmt->get_result();
} else {
    echo "Product ID is missing.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link rel="icon" href="../logo.png" type="image/png">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
            position: relative;
        }

        h2 {
            text-align: center;
            font-weight: bold;
            color: #333;
        }

        .table {
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
        }

        .table th {
            background-color: #D4AF37;
            color: white;
            text-align: center;
        }

        .table td {
            vertical-align: middle;
            text-align: center;
        }

        .img-fluid {
            max-width: 100px;
            height: auto;
            border-radius: 5px;
            transition: transform 0.3s ease;
        }

        .img-fluid:hover {
            transform: scale(1.1);
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 1.5rem;
            color: #333;
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
    </style>
</head>

<body>

    <div class="container">
        <a href="product_list.php" class="back-button"><i class="fas fa-arrow-left"></i></a>

        <h2>Product Details</h2>

        <!-- Product Details Table -->
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product Name</th>
                    <th>Image 1</th>
                    <th>Image 2</th>
                    <th>Image 3</th>
                    <th>Image 4</th>
                    <th>Category Name</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['product_id']); ?></td>
                        <td><?php echo htmlspecialchars($product['product_name']); ?></td>

                        <?php
                        $images = [
                            !empty($product['p_image']) ? '<img src="../admin/' . htmlspecialchars($product['p_image']) . '" class="img-fluid">' : '',
                            !empty($product['p_image2']) ? '<img src="../admin/' . htmlspecialchars($product['p_image2']) . '" class="img-fluid">' : '',
                            !empty($product['p_image3']) ? '<img src="../admin/' . htmlspecialchars($product['p_image3']) . '" class="img-fluid">' : '',
                            !empty($product['p_image4']) ? '<img src="../admin/' . htmlspecialchars($product['p_image4']) . '" class="img-fluid">' : ''
                        ];

                        foreach ($images as $image) {
                            if (empty($image)) {
                                echo "<td></td>";
                            } else {
                                echo "<td>$image</td>";
                            }
                        }
                        ?>

                        <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-danger">No product found for ID: <?php echo htmlspecialchars($product_id); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Customer Orders Table -->
        <h3 class="mt-5">Customers Who Ordered This Product</h3>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Order Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $order_count = $order_result->num_rows;
                if ($order_count > 0):
                ?>
                    <?php while ($order = $order_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['customer_email']); ?></td>
                            <td><?php echo htmlspecialchars($order['customer_address']); ?></td>
                            <td><?php echo htmlspecialchars($order['order_quantity']); ?></td>
                        </tr>
                    <?php endwhile; ?>

                    <tr>
                        <td colspan="3" class="text-end fw-bold">Total Customers :</td>
                        <td class="fw-bold"><?php echo $order_count; ?></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-danger">No customers found who ordered this product.</td>
                    </tr>
                <?php endif; ?>
            </tbody>

        </table>

    </div>

    <!-- Footer -->
    <footer>
        © 2025 Gifty House. All Rights Reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$conn->close();
?>