<?php
session_start();
include 'connection/connection.php';
require_once('TCPDF/tcpdf.php');
include 'invoice_class/invoice.php';

// Handle logout request
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_unset();
    session_destroy();
    header('Location: forms/login.php');
    exit;
}

if (!isset($_GET['order_id'])) {
    die("Order ID is missing.");
}

$order_id = intval($_GET['order_id']);


$stmt = $conn->prepare("
    SELECT o.order_id, o.order_cost, o.order_status, o.order_date, o.user_id, o.shipping_fee,o.total_cost,
           u.full_name, u.email, u.contact_number, u.address,
           o.other_user_name, o.other_user_email,
           oi.product_name, oi.product_image, oi.product_price, oi.product_quantity
    FROM orders o
    JOIN order_item oi ON o.order_id = oi.order_id
    JOIN users u ON o.user_id = u.id
    WHERE o.order_id = ?
");

$stmt->bind_param('i', $order_id);
$stmt->execute();
$order_details = $stmt->get_result();

if ($order_details->num_rows === 0) {
    die("No details found for this order.");
}

// Fetch first row for order & user info
$order_info = $order_details->fetch_assoc();
$order_date = (!empty($order_info['order_date']) && $order_info['order_date'] != '0000-00-00')
    ? date('d M Y', strtotime($order_info['order_date']))
    : "Date not available";

// Fetch shipping fee and calculate total cost
$shipping_fee = $order_info['shipping_fee'] ?? 0;
$total_cost = $order_info['order_cost'] + $shipping_fee;

$other_user_name = !empty($order_info['other_user_name']) ? htmlspecialchars($order_info['other_user_name']) : "N/A";
$other_user_email = !empty($order_info['other_user_email']) ? htmlspecialchars($order_info['other_user_email']) : "N/A";

$customer_details = [
    'full_name' => $order_info['full_name'],
    'email' => $order_info['email'],
    'contact_number' => $order_info['contact_number'],
    'address' => $order_info['address']
];

$order_items = [];
do {
    $order_items[] = [
        'product_name' => $order_info['product_name'],
        'price' => $order_info['product_price'],
        'product_quantity' => $order_info['product_quantity']
    ];
} while ($order_info = $order_details->fetch_assoc());


if (isset($_GET['download_pdf'])) {
    $invoice = new Invoice($order_id, $order_items, $customer_details, $shipping_fee); 
    $invoice->generatePDF();
    exit;
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - #<?php echo $order_id; ?></title>
    <link rel="icon" href="logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .order-details-container {
            max-width: 90%;
            margin: auto;
            padding: 20px;
            background: white;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
            margin-top: 30px;
        }

        /* Table Styling */
        table {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            border-collapse: collapse;
        }

        thead {
            background-color: #D4AF37;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        th,
        td {
            text-align: center;
            padding: 12px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        tr:hover {
            background-color: #f1f1f1;
            transition: 0.3s;
        }

        /* Product Image */
        .product-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        /* Status Badge */
        .status {
            padding: 6px 10px;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            min-width: 80px;
        }

        .status.on_hold {
            background-color: #FFC107;
            color: white;
        }

        .status.completed {
            background-color: #28A745;
            color: white;
        }

        .status.cancelled {
            background-color: #DC3545;
            color: white;
        }

        /* Back Button */
        .back-btn {
            background-color: #D4AF37;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            margin-right: 10px;
        }

        .back-btn:hover {
            background-color: #F26D6D;
            color: white;
            transform: scale(1.05);
        }
    </style>
</head>

<body>

    <!-- PHP Include Navbar -->
    <?php include 'navbar.php'; ?>
    <div class="order-details-container">
        <h2>Order Details - #<?php echo $order_id; ?></h2>
        <p>Ordered on:
            <?php
            if (!empty($order_date) && $order_date != '0000-00-00') {
                echo date('d M Y', strtotime($order_date));
            } else {
                echo "Date not available";
            }
            ?>
        </p>

        <?php if (!empty($other_user_name) && trim($other_user_name) !== "N/A" && !empty($other_user_email) && trim($other_user_email) !== "N/A"): ?>
            <h4>Recipient Information</h4>
            <p><strong>Name:</strong> <?php echo $other_user_name; ?></p>
            <p><strong>Email:</strong> <?php echo $other_user_email; ?></p>
        <?php endif; ?>


        <table class="table table-striped table-hover table-bordered">
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Product Image</th>
            <th>Price (Rs.)</th>
            <th>Quantity</th>
            <th>Total Cost (Rs.)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $order_details->data_seek(0);
        $total_order_cost = 0;
        while ($row = $order_details->fetch_assoc()) {
            $total_cost_per_product = $row['product_price'] * $row['product_quantity'];
            $total_order_cost += $total_cost_per_product; ?>
            <tr>
                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                <td>
                    <img src="admin/<?php echo htmlspecialchars($row['product_image']); ?>" class="product-img" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                </td>
                <td>Rs. <?php echo number_format($row['product_price'], 2); ?></td>
                <td><?php echo $row['product_quantity']; ?></td>
                <td>Rs. <?php echo number_format($total_cost_per_product, 2); ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<!-- Display Shipping Fee and Total Cost -->
<div class="order-summary">
    <p><strong>Shipping Fee:</strong> Rs. <?php echo number_format($shipping_fee, 2); ?></p>
    <p><strong>Total Cost (Including Shipping):</strong> Rs. <?php echo number_format($total_cost, 2); ?></p>
</div>


        <a href="order.php" class="back-btn">Back to Orders</a>
        <a href="?order_id=<?php echo $order_id; ?>&download_pdf=true" class="back-btn">Download as PDF</a>
    </div>

    <?php include 'footer.html'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>