<?php
session_start();

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
    <title>About Us</title>
    <link rel="icon" href="logo.png" type="image/png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    
        body {
            font-family: 'Arial', sans-serif;
            background-color: #ffffff;
            color: #636E72;
            margin: 0;
            padding: 0;
            animation: pageLoad 1s ease-out forwards;
            
        }

        .header {
            position: relative;
            background-image: url('img/main.jpg');
            background-size: cover;
            background-position: center;
            height: 60vh;
            color: white;
            text-align: left;
            padding: 0;
            animation: fadeInHeader 1.5s ease-out;
    
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }


        .header-text {
            position: absolute;
            top: 50%;
            left: 20px;
            transform: translateY(-50%);
            z-index: 2;
            color: #D4AF37;
            font-size: 5rem;
            font-weight: 700;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }


        .header-text p {
            font-size: 2rem;
            color: #F7E7CE;
            margin-top: 1rem;
            font-weight: 300;
        }



        /* About Text and Image Fade-in Animation */
        .about-text h2 {
            font-size: 2.6rem;
            color: #2A3D66;
            font-weight: 700;
            margin-bottom: 1.5rem;
            animation: fadeInUp 1.5s ease-out;
            /
        }

        .about-text p {
            font-size: 1.2rem;
            line-height: 1.7;
            margin-bottom: 1.2rem;
            color: #333333;
            animation: fadeInUp 2s ease-out;
        }


        /* Learn More Button */
        .learn-more-btn {
            background-color: #D4AF37;
            color: #fff;
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.3s ease;
            margin-top: 2rem;
        }

        .learn-more-btn:hover {
            background-color: #F26D6D;
            color: #fff;
            transform: scale(1.1);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        /* Footer Styling */
        .footer {
            background-color: #2A3D66;
            color: white;
            text-align: center;
            padding: 2rem 1rem;
            margin-top: 3rem;
        }

        .footer p {
            font-size: 1.2rem;
        }

        /* Image Section Hover Animation */
        .about-image img {
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            max-width: 100%;
            height: auto;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .about-image img:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.2);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header-text {
                font-size: 4rem;
            }

            .header-text p {
                font-size: 1.5rem;
            }

            .about-text h2 {
                font-size: 2.3rem;
            }

            .about-text p {
                font-size: 1.2rem;
            }

            .learn-more-btn {
                font-size: 1.3rem;
            }
        }
    </style>
</head>

<body>

    <?php include('navbar.php'); ?>

 
    <div class="header">
        <div class="header-text">
            <h1>About Us</h1>
            <p>Discover the heart behind Gifty House.</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row align-items-center">
          
            <div class="col-md-6 about-text">
                <h2>Who We Are</h2>
                <p>Welcome to Gifty House! We specialize in offering a unique collection of gifts designed to bring joy to every occasion. Our mission is to make gift-giving a memorable and heartfelt experience.</p>
                <p>Since our inception, we have been dedicated to providing high-quality, carefully curated items that stand out. Whether you're shopping for birthdays, anniversaries, holidays, or just because, we have something special for everyone.</p>
                <p>Our team works tirelessly to bring you the best in customer service and product quality. Thank you for letting us be part of your celebrations!</p>
                <a href="about2.php" class="learn-more-btn">Learn More</a>
            </div>
        
            <div class="col-md-6 about-image">
                <img src="img/about_1.jpg" alt="Gift store image">
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.html'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>