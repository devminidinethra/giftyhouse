<?php
require '../connection/connection.php';


if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];

    
    $query = "
        SELECT p.* 
        FROM product p
        WHERE p.category_id = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
       
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    } else {
        
        $no_products_found = true;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Product Details</title>
    <link rel="icon" href="../logo.png" type="image/png">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">

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

        .no-products-message {
            text-align: center;
            font-size: 18px;
            color: #e74c3c;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="container">
        <a href="category_list.php" class="back-button"><i class="fas fa-arrow-left"></i></a>

        <h2>Product Details in Category (Total: <?php echo isset($products) ? count($products) : 0; ?>)</h2>


        <?php if (isset($no_products_found) && $no_products_found): ?>
            <div class="no-products-message">No products are available in this category.</div>
        <?php else: ?>
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
                        <th>Price</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['product_id']; ?></td>
                            <td><?php echo $product['product_name']; ?></td>
                            <td>
                                <?php if (!empty($product['p_image'])): ?>
                                    <img src="../admin/<?php echo $product['p_image']; ?>" class="img-fluid" alt="Product Image 1">
                                <?php else: ?>
                                    <span class="no-image">No Image Available</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($product['p_image2'])): ?>
                                    <img src="../admin/<?php echo $product['p_image2']; ?>" class="img-fluid" alt="Product Image 2">
                                <?php else: ?>
                                    <span class="no-image">No Image Available</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($product['p_image3'])): ?>
                                    <img src="../admin/<?php echo $product['p_image3']; ?>" class="img-fluid" alt="Product Image 3">
                                <?php else: ?>
                                    <span class="no-image">No Image Available</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($product['p_image4'])): ?>
                                    <img src="../admin/<?php echo $product['p_image4']; ?>" class="img-fluid" alt="Product Image 4">
                                <?php else: ?>
                                    <span class="no-image">No Image Available</span>
                                <?php endif; ?>
                            </td>
                            <td>Rs. <?php echo $product['price']; ?></td>
                            <td><?php echo $product['p_description']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
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
