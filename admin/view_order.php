<?php
require '../connection/connection.php';

if (isset($_GET['id'])) {
    $order_id = $_GET['id'];

    $query = "
    SELECT oi.*, p.product_name, p.p_image, p.price, o.order_cost, o.shipping_fee, o.total_cost, u.full_name, u.email, u.contact_number, u.address, oi.payment_status
    FROM order_item oi
    JOIN product p ON oi.product_id = p.product_id
    JOIN orders o ON oi.order_id = o.order_id
    JOIN users u ON o.user_id = u.id
    WHERE oi.order_id = ?
";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order_items = [];
        while ($row = $result->fetch_assoc()) {
            $order_items[] = $row;
        }
    } else {
        header("Location: order_list.php");
        exit();
    }

    $stmt->close();
} else {
    header("Location: order_list.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_paid'])) {
    $update_payment = "UPDATE order_item SET payment_status = 'Paid' WHERE order_id = ?";
    $stmt = $conn->prepare($update_payment);
    $stmt->bind_param("i", $order_id);
    if ($stmt->execute()) {
        header("Location: view_order.php?id=" . $order_id);
        exit();
    }
    $stmt->close();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="icon" href="../logo.png" type="image/png">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #f1f1f1;
            font-family: 'Arial', sans-serif;
        }

        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            margin-top: 40px;
            position: relative;
        }

        h2 {
            text-align: center;
            font-weight: bold;
            color: #333;
            margin-bottom: 30px;
        }

        .img-fluid {
            max-width: 100px;
            height: auto;
            border-radius: 8px;
            transition: transform 0.3s ease;
            object-fit: cover;
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

        .table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
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

        .no-image {
            color: #999;
            font-style: italic;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 14px;
            color: #aaa;
        }

        .footer a {
            color: #D4AF37;
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="container">
    <a href="order_list.php" class="back-button"><i class="fas fa-arrow-left"></i></a>

    <h2>Order Details</h2>
    
    <?php if (!empty($order_items)): ?>
        <div class="user-details mb-4">
            <h4 style="text-decoration: underline;">User Details</h4>
            <p><strong>Name:</strong> <?php echo $order_items[0]['full_name']; ?></p>
            <p><strong>Email:</strong> <?php echo $order_items[0]['email']; ?></p>
            <p><strong>Phone:</strong> <?php echo $order_items[0]['contact_number']; ?></p>
            <p><strong>Address:</strong> <?php echo $order_items[0]['address']; ?></p>
        </div>
    <?php endif; ?>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>Image</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_cost = 0;
            if (!empty($order_items)): 
                foreach ($order_items as $item): 
                    $item_total = $item['price'] * $item['product_quantity'];
                    $total_cost += $item_total;
            ?>
                <tr>
                    <td><?php echo $item['product_id']; ?></td>
                    <td><?php echo $item['product_name']; ?></td>
                    <td>
                        <img src="../admin/<?php echo $item['p_image']; ?>" class="img-fluid" alt="Product Image">
                    </td>
                    <td>Rs. <?php echo $item['price']; ?></td>
                    <td><?php echo $item['product_quantity']; ?></td>
                    <td>Rs. <?php echo $item_total; ?></td>
                </tr>
            <?php endforeach; ?>
                <tr>
                    <td colspan="5" class="text-end"><strong>Total Order Cost:</strong></td>
                    <td>Rs. <?php echo $total_cost; ?></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-end"><strong>Shipping Fee:</strong></td>
                    <td>Rs. <?php echo $order_items[0]['shipping_fee']; ?></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-end"><strong>Total Cost:</strong></td>
                    <td>Rs. <?php echo $order_items[0]['total_cost']; ?></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No products found for this order.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="text-center">
        <h4>Payment Status: 
            <span class="badge bg-<?php echo ($order_items[0]['payment_status'] == 'Paid') ? 'success' : 'danger'; ?>">
                <?php echo $order_items[0]['payment_status']; ?>
            </span>
        </h4>

        <?php if ($order_items[0]['payment_status'] != 'Paid'): ?>
            <form method="POST">
                <button type="submit" name="mark_paid" class="btn btn-success">Mark as Paid</button>
            </form>
        <?php endif; ?>
    </div>

</div>

<footer>© 2025 Gifty House. All Rights Reserved.</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
