<?php
session_start();

include 'connection/connection.php'; 

// categories from the database
$sql = "SELECT category_id, category_name, c_description, c_picture FROM category";
$result = $conn->query($sql);

$newArrivalsQuery = "
    SELECT p.product_id, p.product_name, p.p_image, p.price
    FROM new_arrivals na
    INNER JOIN product p ON na.product_id = p.product_id
";
$newArrivalsResult = $conn->query($newArrivalsQuery);

// Handle logout request
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_unset();
    session_destroy();
    header('Location: forms/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gifty House</title>
    <link rel="icon" href="logo.png" type="image/png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">

    <style>

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        .carousel-inner img {
            height: 450px;
            object-fit: cover;
        }

        .carousel-caption {
            background: rgba(0, 0, 0, 0.6);
            padding: 20px;
            border-radius: 8px;
        }

        .carousel-caption h5 {
            font-size: 1.7rem;
            font-weight: bold;
            color: #f5d547;
            margin-bottom: 20px;
        }

        .carousel-caption p {
            font-size: 1rem;
            color: #fff;
            margin-bottom: 25px;
        }

        .carousel-caption .btn {
            background-color: #f5d547;
            color: #2a3d66;
            border-radius: 5px;
            padding: 8px 16px;
            text-transform: uppercase;
            font-weight: 500;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease-in-out;
        }

        .carousel-caption .btn:hover {
            background-color: #F26D6D;
            color: #fff;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3);
        }

        .carousel-caption.right {
            top: 50%;
            right: 15%;
            left: auto;
            transform: translateY(-50%);
        }

        .carousel-caption.left {
            top: 50%;
            left: 15%;
            right: auto;
            transform: translateY(-50%);
        }

        @media (max-width: 768px) {
            .carousel-caption h5 {
                font-size: 1.5rem;
            }

            .carousel-caption p {
                font-size: 0.9rem;
            }
        }


        .review-item {
            background: url('./img/back.jpg') no-repeat center center;
            background-size: cover;
            height: 505px;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .review-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 460px;
            background: rgba(0, 0, 0, 0.6);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
            margin: 0 10px;
        }

        .review-text {
            font-size: 1.2rem;
            color: #fff;
            margin-bottom: 15px;
            font-style: italic;
        }

        .reviewer-name {
            font-size: 1.3rem;
            font-weight: bold;
            color: #f5d547;
        }

        .reviews-heading {
            text-align: center;
            font-size: 2.5rem;
            font-weight: bold;
            color: #f5d547;
            margin-bottom: 30px;
        }

      
        @media (max-width: 768px) {
            .review-content {
                height: auto;
                padding: 15px;
            }

            .review-text {
                font-size: 1rem;
            }

            .reviewer-name {
                font-size: 1.1rem;
            }

            .reviews-heading {
                font-size: 1.8rem;
            }
        }


        .categories-section {
            padding: 40px 0;
            background-color: #fff;
        }

        .categories-section h2 {
            text-align: center;

            font-weight: bold;
            color: rgb(236, 207, 76);
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }


        .categories-section .card {
            border: none;
            margin-top: 25px;
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            background: linear-gradient(135deg, #ffffff, #f5f5f5);
            height: 100%;
            display: flex;
            flex-direction: column;
        }


        .categories-section .card:hover {
            transform: translateY(-12px) scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.25);
        }

        .categories-section .card-img-top {
            height: 400px;
            object-fit: cover;
            border-bottom: 3px solid #2a3d66;
            transition: transform 0.4s ease;
        }

        .categories-section .card-body {
            padding: 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .categories-section .card-title {
            font-size: 1.6rem;
            font-weight: bold;
            color: #2a3d66;
            margin-bottom: 10px;
        }

        .categories-section .card-text {
            font-size: 1rem;
            color: #555;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .categories-section .btn {
            background-color: #f5d547;
            color: #2a3d66;
            border: none;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: bold;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            text-transform: uppercase;
        }

        .categories-section .btn:hover {
            background-color: #F26D6D;
            color: #fff;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }


       
        /* New Arrival Section */
        .new-arrival-section {
            padding: 60px 15px;
            background-color: #fff;
            color: #333;
            margin: 0 auto;
            max-width: 1200px;
        }

        .new-arrival-section h2 {
            text-align: center;
            font-weight: bold;
            color: #eccf4c;
            margin-bottom: 40px;
            font-size: 2.5rem;
            letter-spacing: 1.5px;
        }

        .new-arrival-card {
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 450px;
        }

        .new-arrival-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .new-arrival-card img {
            height: 60%;
            object-fit: cover;
            width: 100%;
            transition: transform 0.3s ease;
        }

        .new-arrival-card .card-body {
            padding: 15px;
            text-align: center;
        }

        .new-arrival-card .card-text {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 10px;
        }

        .new-arrival-card .price {
            font-size: 1.1rem;
            font-weight: bold;
            color: #2a3d66;
            margin-bottom: 10px;
        }

        .new-arrival-card .btn {
            background-color: #f5d547;
            color: #2a3d66;
            border-radius: 25px;
            padding: 8px 16px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .new-arrival-card .btn:hover {
            background-color: #f26d6d;
            color: #fff;
        }

        /* Swiper Navigation Buttons */
        .swiper-button-next,
        .swiper-button-prev {
            background: rgba(255, 255, 255, 0.9);
            color: #2a3d66;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 10;
            transition: all 0.3s ease;
        }

        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            background: #f26d6d;
            color: #fff;
        }

        .swiper-button-next::after,
        .swiper-button-prev::after {
            font-size: 1.5rem;
        }

        /* Spacing adjustments */
        .swiper-container {
            margin: 0 auto;
            position: relative;
            padding: 0 15px;
        }

        /* Media Queries */
        @media (max-width: 768px) {
            .new-arrival-card {
                height: 400px;
            }

            .new-arrival-card img {
                height: 55%;
            }

            .new-arrival-section h2 {
                font-size: 2rem;
            }
        }

        @media (max-width: 576px) {
            .new-arrival-card {
                height: 350px;
            }

            .new-arrival-card img {
                height: 50%;
            }

            .swiper-button-next,
            .swiper-button-prev {
                width: 35px;
                height: 35px;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Carousel -->
    <div id="customCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#customCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#customCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#customCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            <button type="button" data-bs-target="#customCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
        </div>

        <div class="carousel-inner">
            <!-- Slide 1 -->
            <div class="carousel-item active">
                <img src="./img/home1.jpg" class="d-block w-100" alt="Beautiful Landscape">
                <div class="carousel-caption right">
                    <h5>Exceptional Gifts for Every Occasion</h5>
                    <p>Find something unique and meaningful for your loved ones.</p>
                    <a href="shop.php" class="btn">Shop Now</a>
                </div>
            </div>
            <!-- Slide 2 -->
            <div class="carousel-item">
                <img src="./img/home2.jpg" class="d-block w-100" alt="Modern Architecture">
                <div class="carousel-caption left">
                    <h5>Celebrate Life’s Special Moments</h5>
                    <p>Explore a range of gifts tailored for every celebration.</p>
                    <a href="shop.php" class="btn">Shop Now</a>
                </div>
            </div>
            <!-- Slide 3 -->
            <div class="carousel-item">
                <img src="./img/home3.jpg" class="d-block w-100" alt="Beautiful Landscape">
                <div class="carousel-caption right">
                    <h5>Perfect Presents for Every Budget</h5>
                    <p>Beautifully crafted gifts, perfect for any budget.</p>
                    <a href="shop.php" class="btn">Shop Now</a>
                </div>
            </div>
            <!-- Slide 4 -->
            <div class="carousel-item">
                <img src="./img/home4.jpg" class="d-block w-100" alt="Cityscape at Night">
                <div class="carousel-caption left">
                    <h5>Timeless Gifts, Modern Elegance</h5>
                    <p>Experience the art of gifting with our premium collection.</p>
                    <a href="shop.php" class="btn">Shop Now</a>
                </div>
            </div>
        </div>

        <!-- Navigation Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#customCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#customCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>


    <div class="new-arrival-section">
    <h2>New Arrivals</h2>
    <div class="swiper-container">
    <div class="swiper-wrapper">
    <?php
    if ($newArrivalsResult->num_rows > 0) {
        // Loop through products
        while ($row = $newArrivalsResult->fetch_assoc()) {
            echo '<div class="swiper-slide">';
            echo '    <div class="new-arrival-card">';
            echo '        <img src="admin' . htmlspecialchars($row['p_image']) . '" class="card-img-top" alt="' . htmlspecialchars($row['product_name']) . '">';
            echo '        <div class="card-body">';
            echo '            <p class="card-text">' . htmlspecialchars($row['product_name']) . '</p>';
            echo '            <p class="price">Rs. ' . number_format($row['price'], 2) . '</p>';
            echo '            <a href="single_product.php?product_id=' . $row['product_id'] . '" class="btn">Shop Now</a>';
            echo '        </div>';
            echo '    </div>';
            echo '</div>';
        }
    } else {
        echo '<p class="text-center">No new arrivals available.</p>';
    }
    ?>
</div>

        <!-- Add Pagination -->
        <div class="swiper-pagination"></div>
        <!-- Add Navigation -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
</div>


<div class="categories-section">
    <div class="container">
        <h2 class="text-center mb-4">Explore Our Categories</h2>
        <div class="row gy-5">
            <?php
            if ($result->num_rows > 0) {
               
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="col-12 col-sm-6 col-md-4">';
                    echo '    <div class="card">';
                    echo '        <img src="admin' . htmlspecialchars($row['c_picture']) . '" class="card-img-top" alt="' . htmlspecialchars($row['category_name']) . '">';
                    echo '        <div class="card-body">';
                    echo '            <h5 class="card-title">' . htmlspecialchars($row['category_name']) . '</h5>';
                    echo '            <p class="card-text">' . htmlspecialchars($row['c_description']) . '</p>';
                    echo '<a href="shop.php?category=' . urlencode($row['category_name']) . '" class="btn">Shop Now</a>';

                    echo '        </div>';
                    echo '    </div>';
                    echo '</div>';
                }
            } else {
                echo '<p class="text-center">No categories available.</p>';
            }
            ?>
        </div>
    </div>
</div>


    <!-- Customer Reviews Section -->
    <div id="customerReviews" class="carousel slide mt-5" data-bs-ride="carousel" data-bs-interval="4000">
        <div class="reviews-heading">Happy Customers</div>
        <div class="carousel-inner">
            <!-- Review 1 -->
            <div class="carousel-item active review-item">
                <div class="review-content">
                    <p class="review-text">"Absolutely amazing experience! The gift collection is diverse and unique. I found the perfect present for my friend's birthday. Highly recommend!"</p>
                    <p class="reviewer-name">- Ranjith Gamage -</p>
                </div>
            </div>
            <!-- Review 2 -->
            <div class="carousel-item review-item">
                <div class="review-content">
                    <p class="review-text">"Perfect for every occasion! I’ve never seen such a thoughtful range of gifts. The quality and service are top-notch!"</p>
                    <p class="reviewer-name">- Sadamali Cooray -</p>
                </div>
            </div>
            <!-- Review 3 -->
            <div class="carousel-item review-item">
                <div class="review-content">
                    <p class="review-text">"A seamless shopping experience. The user-friendly website and variety of options make it my go-to place for gifts. Highly satisfied!"</p>
                    <p class="reviewer-name">- Vibodha Perera -</p>
                </div>
            </div>
            <!-- Review 4 -->
            <div class="carousel-item review-item">
                <div class="review-content">
                    <p class="review-text">"Highly recommend! The attention to detail in packaging and the timely delivery made all the difference. Fantastic experience!"</p>
                    <p class="reviewer-name">- Vinith Perera -</p>
                </div>
            </div>
        </div>

        <!-- Navigation Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#customerReviews" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#customerReviews" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- Footer -->
    <?php include 'footer.html'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Swiper JS -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        const swiper = new Swiper('.swiper-container', {
            slidesPerView: 4,
            spaceBetween: 20,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                1200: {
                    slidesPerView: 4,
                },
                992: {
                    slidesPerView: 3,
                },
                768: {
                    slidesPerView: 2,
                },
                576: {
                    slidesPerView: 1,
                },
            },
        });
    </script>


</body>

</html>