<?php
session_start();
include 'connection/connection.php';

// Handle logout request
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_unset();
    session_destroy();
    header('Location: forms/login.php');
    exit;
}

// Ensure product_id is provided
if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
    echo '<script>alert("Error: No product selected."); window.location="home.php";</script>';
    exit();
}

$product_id = filter_var($_GET['product_id'], FILTER_SANITIZE_STRING);

// Fetch the product details
$stmt = $conn->prepare("SELECT * FROM product WHERE product_id = ?");
$stmt->bind_param("s", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product || empty($product['product_name']) || empty($product['price']) || empty($product['p_image'])) {
    echo '<script>alert("Error: Product details are incomplete."); window.location="home.php";</script>';
    exit();
}

// Fetch related products based on category
$category_name = $product['category_name'];
$related_products_stmt = $conn->prepare("SELECT * FROM product WHERE category_name = ? AND product_id != ? LIMIT 4");
$related_products_stmt->bind_param("ss", $category_name, $product_id);
$related_products_stmt->execute();
$related_products_result = $related_products_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }

        #mainImg {
            width: 100%;
            height: auto;
            max-height: 500px;
            object-fit: contain;
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .small-img-group {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .small-img-col {
            flex-basis: 24%;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .small-img-col:hover {
            transform: scale(1.05);
        }

        .small-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        .single-product {
            padding-top: 20px;
            margin-top: 0;
        }

        .single-product h6 {
            font-size: 16px;
            color: #9E9E9E;
        }

        .single-product h3 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
        }

        .single-product h2 {
            font-size: 2rem;
            font-weight: 600;
            color: #F26D6D;
            margin-bottom: 20px;
        }

        .single-product input {
            width: 70px;
            height: 40px;
            padding-left: 10px;
            font-size: 16px;
            margin-right: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .single-product input:focus {
            outline: none;
            border-color: #F26D6D;
        }

        .single-product .buy-btn {
            background-color: #f5d547;
            color: #2a3d66;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: 600;
            text-transform: uppercase;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            border: none;
            transition: all 0.3s ease-in-out;
        }

        .buy-btn {
            text-decoration: none;

        }

        .single-product .buy-btn:hover {
            background-color: #F26D6D;
            color: #fff;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3);
        }

        .related-products {
            text-align: center;
            margin-top: 50px;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .related-products h3 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #2a3d66;
        }

        .product-card {
            background: #fff;
            border: none;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
            text-align: center;
            margin-bottom: 30px;
            height: 460px;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
        }

        .product-card img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            height: 300px;
        }

        .product-card h5 {
            margin-top: 10px;
            font-size: 18px;
            font-weight: 600;
        }

        .product-card p {
            font-size: 16px;
            font-weight: bold;
            color: #2a3d66;
        }

        .product-card .buy-btn {
            background-color: #f5d547;
            color: #2a3d66;
            border-radius: 5px;
            padding: 6px 8px;
            text-transform: uppercase;
            font-weight: 500;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease-in-out;
            border: none;
        }

        .product-card .buy-btn:hover {
            background-color: #F26D6D;
            color: #fff;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <section class="container single-product my-5 pt-5">
        <div class="row mt-5">
            <div class="col-lg-5 col-md-6 col-sm-12">
                <img src="admin/<?php echo htmlspecialchars($product['p_image']); ?>" class="img-fluid w-100 p-1" id="mainImg">
                <div class="small-img-group">
                    <?php if (!empty($product['p_image2'])): ?>
                        <div class="small-img-col">
                            <img src="admin/<?php echo htmlspecialchars($product['p_image']); ?>" class="small-img">
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($product['p_image2'])): ?>
                        <div class="small-img-col">
                            <img src="admin/<?php echo htmlspecialchars($product['p_image2']); ?>" class="small-img">
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($product['p_image3'])): ?>
                        <div class="small-img-col">
                            <img src="admin/<?php echo htmlspecialchars($product['p_image3']); ?>" class="small-img">
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($product['p_image4'])): ?>
                        <div class="small-img-col">
                            <img src="admin/<?php echo htmlspecialchars($product['p_image4']); ?>" class="small-img">
                        </div>
                    <?php endif; ?>
                </div>


            </div>

            <div class="col-lg-6 col-md-12 col-12">
                <h6><?php echo htmlspecialchars($product['category_name']); ?></h6>
                <h3 class="py-4"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                <h2>Price: Rs. <?php echo number_format($product['price'], 2); ?></h2>

                <form action="cart.php" method="post">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
                    <input type="hidden" name="p_image" value="<?php echo htmlspecialchars($product['p_image']); ?>">
                    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>">
                    <input type="hidden" name="price" value="<?php echo htmlspecialchars($product['price']); ?>">
                    <input type="number" name="quantity" value="1">
                    <button class="buy-btn" type="submit" name="add_to_cart">Add to Cart</button>
                </form>

                <h4 class="mt-5 mb-5">Product Details:</h4>
                <span><?php echo htmlspecialchars($product['p_description']); ?></span>
            </div>
        </div>
    </section>

    <section class="container related-products">
        <h3>Related Products</h3>
        <div class="row">
            <?php if ($related_products_result->num_rows > 0): ?>
                <?php while ($related_product = $related_products_result->fetch_assoc()): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="product-card">
                            <img src="admin/<?php echo htmlspecialchars($related_product['p_image']); ?>">
                            <h5><?php echo htmlspecialchars($related_product['product_name']); ?></h5>
                            <p>Rs. <?php echo number_format($related_product['price'], 2); ?></p>
                            <a href="single_product.php?product_id=<?php echo urlencode($related_product['product_id']); ?>" class="buy-btn">Shop Now</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center w-100">No related products found.</p>
            <?php endif; ?>
        </div>
    </section>


    <?php include 'footer.html'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        var mainImg = document.getElementById("mainImg");
        var smallImg = document.getElementsByClassName("small-img");

        for (let i = 0; i < smallImg.length; i++) {
            smallImg[i].onclick = function() {
                mainImg.src = this.src;
               
            };
        }
    </script>
</body>

</html>