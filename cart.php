<?php
session_start();



if (isset($_POST['add_to_cart'])) {

    //if user has already added  to cart
    if (isset($_SESSION['cart'])) {

        $products_array_ids = array_column($_SESSION['cart'], "product_id");

        //if product is already add to cart or not
        if (!in_array($_POST['product_id'], $products_array_ids)) {

            $product_id = $_POST['product_id'];

            $product_array = array(
                'product_id' => $_POST['product_id'],
                'product_name' => $_POST['product_name'],
                'price' =>  $_POST['price'],
                'p_image' => $_POST['p_image'],
                'quantity' => $_POST['quantity']
            );

            $_SESSION['cart'][$product_id] = $product_array;

        } else {
            echo '<script>alert("Product was already to cart)</script>';

        }

       
    } else {

        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $product_price = $_POST['price'];
        $Product_image = $_POST['p_image'];
        $product_quantity = $_POST['quantity'];

        $product_array = array(
            'product_id' => $product_id,
            'product_name' => $product_name,
            'price' => $product_price,
            'p_image' => $Product_image,
            'quantity' => $product_quantity
        );

        $_SESSION['cart'][$product_id] = $product_array;
    }

    calculateTotalCart();


    //remove order from cart
} elseif (isset($_POST['remove_product'])) {

    $product_id = $_POST['product_id'];
    unset($_SESSION['cart'][$product_id]);


    
    calculateTotalCart();

} elseif (isset($_POST['edit_quantity'])) {

    $product_id = $_POST['product_id'];
    $product_quantity = $_POST['product_quantity'];
    $_SESSION['cart'][$product_id]['quantity'] = $product_quantity;


    calculateTotalCart();

    header("Location: cart.php");
    exit();


} else {
   
}


function calculateTotalCart() {

    $total_price = 0;
    $total_quantity = 0;

    foreach($_SESSION['cart'] as $key => $value){

        $product = $_SESSION['cart'][$key];

        $price = $product['price'];
        $quantity = $product['quantity'];

        $total_price = $total_price + ($price * $quantity);
        $total_quantity = $total_quantity + $quantity;



    }

   $_SESSION ['total'] = $total_price;
   $_SESSION['quantity'] = $total_quantity;

}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Gifty House</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            color: #333;
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

        .cart table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .cart th {
            text-align: left;
            padding: 10px 20px;
            color: #fff;
            background-color: #2a3d66;
            border-radius: 8px 8px 0 0;
        }

        .cart td {
            padding: 15px 20px;
        }

        .cart td img {
            width: 150px;
            height: 150px;
            margin-right: 10px;
            border-radius: 8px;
        }

        .cart td input {
            width: 50px;
            height: 35px;
            padding: 5px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: center;
        }

        .cart td a {
            color: #D4AF37;
            text-decoration: none;
        }

        .cart .remove-btn {
            color: #F26D6D;
            text-decoration: none;
            font-size: 15px;
            background-color: white;
            border: none;
            width: 100%;
            text-align: left;
        }

        .cart .edit-btn {
            color: #F26D6D;
            text-decoration: none;
            font-size: 14px;
            background-color: white;
            border: none;

        }

        .cart .product-info p {
            margin: 3px;
        }

        .cart-total {
            display: flex;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .cart-total table {
            width: 100%;
            max-width: 500px;
            border-top: 3px solid #D4AF37;
            padding-top: 20px;
        }

        td:last-child {
            text-align: right;
        }

        th:last-child {
            text-align: right;
        }

        .checkout-btn {
            display: flex;
            justify-content: flex-end;
            background-color: #D4AF37;
            color: #ffffff;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .checkout-btn:hover {
            background-color: #F26D6D;
            color: #fff;
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.3);
            transform: scale(1.05);
        }

        .checkout-container {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }

        .cart .product-info {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .cart h5 {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }

        /* Styling the product row */
        .cart .product-info div {
            flex: 1;
        }

        .cart .product-info small {
            color: #9E9E9E;
        }

        .cart .product-info a {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <a href="shop.php" class="back-button" title="Go Back">
        <i class="bi bi-arrow-left"></i>
    </a>

    <section class="cart container my-5 py-5">
        <div class="container mt-5">
            <h5 class="font-weight-bolde"> Your Cart</h5>
        </div>

        <table class="mt-5 pt-5">
            <tr>
                <th>Products</th>
                <th>Quantity</th>
                <th>SubTotal</th>
            </tr>


                <tr>
                    <td>
                    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) { ?>
    <?php foreach ($_SESSION['cart'] as $key => $value) { ?>
        <tr>
            <td>
                <div class="product-info">
                    <img src="admin<?php echo htmlspecialchars($value['p_image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($value['product_name']); ?>">
                    <div>
                        <p><?php echo htmlspecialchars($value['product_name']); ?></p>
                        <small><span>Rs.</span><?php echo htmlspecialchars($value['price']); ?></small>
                        <br>
                        <form action="cart.php" method="post">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($value['product_id']); ?>">
                            <input type="submit" name="remove_product" class="remove-btn" value="Remove">
                        </form>
                    </div>
                </div>
            </td>
            <td>
                <form action="cart.php" method="post">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($value['product_id']); ?>">
                    <input type="number" name="product_quantity" value="<?php echo htmlspecialchars($value['quantity']); ?>">
                    <input type="submit" class="edit-btn" value="Edit" name="edit_quantity">
                </form>
            </td>
            <td>
                <span>Rs. </span>
                <span class="product-price"><?php echo htmlspecialchars($value['quantity'] * $value['price']); ?></span>
            </td>
        </tr>
    <?php } ?>
<?php } else { ?>
    <tr>
        <td colspan="3" style="text-align: center; font-size: 1.2rem; color: #888; padding: 20px;">
            Your cart is empty.
        </td>
    </tr>
<?php } ?>
        </table>

        <div class="cart-total">
            <table>
                <tr>
                    <td>Total Amount</td>
                    <td>Rs <?php echo isset($_SESSION['total']) ? $_SESSION['total'] : '0.00'; ?></td>

                </tr>
            </table>
        </div>

        <div class="checkout-container">
            <form action="checkout.php" method="post">
                <input class="btn checkout-btn" value="Checkout" name='checkout' type="submit">
            </form>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'footer.html'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html> 