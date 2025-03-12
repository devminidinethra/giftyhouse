<?php

session_start();
include '../connection/connection.php';

if (!isset($_SESSION['total']) || empty($_SESSION['total'])) {
    die("Error: Order total is missing.");
}

if (isset($_POST['Proceed_to_Payment'])) {


    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $user_province = $_POST['user_province'];
    $address = $_POST['address'];
    $order_cost = (int)$_SESSION['total'];
    $order_status = "Pending";
    $order_date = date('Y-m-d H:i:s');
    $user_id = $_SESSION['user_id'];
    
    $user_province = isset($_POST['user_province']) && !empty($_POST['user_province']) ? $_POST['user_province'] : 'Not Provided';

    // Check if delivering to another user
    $deliver_to_other = isset($_POST['deliver_to_other']) ? true : false;
    $other_user_name = $deliver_to_other ? $_POST['other_full_name'] : null;
    $other_user_email = $deliver_to_other ? $_POST['other_email'] : null;

    // Insert into orders table
    $stmt = $conn->prepare("INSERT INTO orders (order_cost, order_status, user_id, user_phone, user_province, user_address, order_date, other_user_name, other_user_email) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("isissssss", $order_cost, $order_status, $user_id, $phone, $user_province, $address, $order_date, $other_user_name, $other_user_email);

    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;
        echo "Order placed successfully! Order ID: " . $order_id;
    } else {
        die("Order Insertion Failed: " . $stmt->error);
    }

    // Ensure cart is not empty
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        die("Error: Cart is empty.");
    }

    foreach ($_SESSION['cart'] as $key => $product) {
        $product_id = $product['product_id'];
        $product_name = $product['product_name'];
        $product_price = $product['price'];
        $product_image = $product['p_image'];
        $product_quantity = $product['quantity'];

        // Step 1: Check if there is enough stock before inserting order item
        $stmt_check = $conn->prepare("SELECT quantity FROM product WHERE product_id = ?");
        if (!$stmt_check) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt_check->bind_param("s", $product_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        $row = $result->fetch_assoc();
        $current_stock = $row['quantity'] ?? 0;

        if ($current_stock < $product_quantity) {
            die("Error: Not enough stock for product ID: " . $product_id);
        }

        // Step 2: Insert into order_item table
        $stmt1 = $conn->prepare("INSERT INTO order_item (order_id, product_id, product_name, product_image, product_price, product_quantity, user_id, order_date) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?);");

        if (!$stmt1) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt1->bind_param("isssiiis", $order_id, $product_id, $product_name, $product_image, $product_price, $product_quantity, $user_id, $order_date);

        if (!$stmt1->execute()) {
            die("Order Item Insertion Failed: " . $stmt1->error);
        }

        // Step 3: Reduce product stock only for the ordered product
        $stmt2 = $conn->prepare("UPDATE product SET quantity = quantity - ? WHERE product_id = ? AND quantity >= ?");
        if (!$stmt2) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt2->bind_param("isi", $product_quantity, $product_id, $product_quantity);

        if (!$stmt2->execute()) {
            die("Stock Update Failed: " . $stmt2->error);
        }
    }

    echo "All order items inserted and stock updated successfully!";


    // Empty the cart after placing the order
    unset($_SESSION['cart']);
    $_SESSION['cart'] = []; 

    // Reset cart quantity count
    unset($_SESSION['quantity']);
    $_SESSION['quantity'] = 0; 

    
    $_SESSION['order_id'] = $order_id;

    header('location: ../to_pay.php?order_status=Order Placed Successfully');
    exit();
} else {
}
