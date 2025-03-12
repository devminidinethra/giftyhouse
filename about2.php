<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - Gifty House</title>
  <link rel="icon" href="logo.png" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    
    body {
      font-family: 'Arial', sans-serif;
      background-color: #fff;
      margin: 0;
      padding: 0;
      animation: pageLoad 1s ease-out;
      
    }
    
    .about-image {
      max-width: 100%;
      border-radius: 15px;
      transition: transform 0.3s ease, opacity 0.3s ease;
      transform: translateX(-15px);
    }

    
    .about-image:hover {
      transform: scale(1.05) translateX(-15px);
      opacity: 0.9;
    }

    
    .about-text {
      text-align: left;
      color: #636E72;
    }

  
    .about-title {
      font-size: 2.4rem;
      font-weight: bold;
      color: #2A3D66;
      margin-bottom: 20px;
      animation: fadeInUp 1s ease-out;
      
    }

    
    .about-paragraph {
      font-size: 1.1rem;
      line-height: 1.8;
      color: #636E72;
      margin-bottom: 1.5rem;
      animation: fadeInUp 1s ease-out;
    
    }

    
    .btn-custom {
      background-color: #D4AF37;
      color: #fff;
      border: none;
      padding: 10px 25px;
      font-size: 1.1rem;
      border-radius: 5px;
      transition: background-color 0.3s ease, transform 0.3s ease;
      animation: bounceIn 1s ease-out;

    }

    .btn-custom:hover {
      background-color: #F26D6D;
      transform: scale(1.05);
  
    }

  
    @media (max-width: 768px) {
      .about-title {
        font-size: 2.2rem;
      }

      .about-paragraph {
        font-size: 1rem;
      }

      .about-image {
        max-width: 90%;
      }
    }

    .section-header {
      color: #2A3D66;
      font-size: 2rem;
      font-weight: bold;
      margin-top: 50px;
      text-align: center;
      animation: fadeInUp 1s ease-out;

    }

    .section-divider {
      border-top: 3px solid #D4AF37;
      width: 50px;
      margin: 20px auto;
    }

    .about-text a {
      color: #D4AF37;
      text-decoration: none;
      font-weight: bold;
    }

    .about-text a:hover {
      color: #F26D6D;
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
  </style>
</head>

<body>
  
  <a href="about.php" class="back-button" title="Go Back">
    <i class="bi bi-arrow-left"></i>
  </a>
  <section class="container about-section">
    <div class="row align-items-center">
      
      <div class="col-md-6">
        <img src="img/about2.jpg" alt="Gifty House" class="img-fluid about-image">
      </div>
     
      <div class="col-md-6 about-text">
        <h2 class="about-title mb-4">About Us</h2>
        <p class="about-paragraph">
          Welcome to <strong>Gifty House</strong>, where every gift is a celebration of thoughtfulness, creativity, and joy.
        </p>
        <p class="about-paragraph">
          At Gifty House, we understand that a gift is more than just an item—it’s an expression of love, appreciation, and care. That’s why we are dedicated to curating a distinctive selection of high-quality, meaningful gifts that make every occasion truly unforgettable.
          From beautifully crafted keepsakes to innovative and personalized items, we cater to all your gifting needs.
        </p>
        <p class="about-paragraph">
          Whether you're celebrating a birthday, wedding, anniversary, or simply sharing a token of appreciation, we believe every moment deserves a touch of thoughtfulness. With a wide range of unique gifts, we strive to make your shopping experience as special as the moments you’re celebrating.
        </p>
        <p class="about-paragraph">
          Our journey began with a vision to redefine the art of gift-giving. Guided by a passion for creativity and a commitment to excellence, we’ve built Gifty House as a trusted destination for heartfelt gifts. We collaborate with artisans and trusted brands worldwide to bring you products that stand out in quality, uniqueness, and design.
        </p>
        <p class="about-paragraph">
          At the heart of Gifty House is our unwavering dedication to customer satisfaction. Our team is committed to providing exceptional service, ensuring that your experience with us is seamless, personalized, and enjoyable. We take pride in helping you find the perfect gift to brighten someone’s day and create lasting memories.
        </p>
        <p class="about-paragraph">
          Thank you for choosing Gifty House. We’re honored to be a part of your special moments, and we look forward to continuing this journey of spreading joy, one gift at a time.
        </p>
      </div>
    </div>
  </section>

  <section class="container">
    <div class="text-center">
      <h3 class="section-header">Our Story</h3>
      <div class="section-divider"></div>
      <p class="about-paragraph">
        At Gifty House, we have a simple goal—helping you make life’s precious moments even more memorable through thoughtful gifts. Join us on our mission to bring joy and meaning to the world, one gift at a time.
      </p>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>