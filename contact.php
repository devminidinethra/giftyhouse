<?php
session_start();

include 'connection/connection.php';

$error_message = '';
$success_message = '';


$user_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : '';
$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!$user_id) {
    $error_message = 'Please log in to submit a message.';
  } else {
    
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $message = $conn->real_escape_string($_POST['message']);

  
    $sql = "INSERT INTO message (user_id, c_name, c_email, message, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isss', $user_id, $name, $email, $message);

    if ($stmt->execute()) {
      $success_message = 'Your message has been sent successfully!';
    } else {
      $error_message = 'Error submitting message: ' . $conn->error;
    }

    $stmt->close();
  }
}

$conn->close();

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
  <title>Gift Shop</title>
  <link rel="icon" href="logo.png" type="image/png">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="./css/style.css">

  <style>
    body {
      animation: pageLoad 1s ease-out forwards;
    }

    .contact-header {
      position: relative;
      background-image: url('img/main.jpg');
      background-size: cover;
      background-position: center;
      height: 60vh;
      color: white;
      text-align: left;
      padding: 0;

    }

    .contact-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 1;
    }

    .contact-header-text {
      position: absolute;
      top: 50%;
      left: 20px;
      transform: translateY(-50%);
      z-index: 2;
      color: #D4AF37;
      font-size: 5rem;
      font-weight: 700;
      text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
      animation: slideInUp 1.5s ease-out;
    }

    .contact-header-text p {
      font-size: 2rem;
      color: #F7E7CE;
      margin-top: 1rem;
      font-weight: 300;
    }

    .hero-section .btn {
      background-color: #D4AF37;
      font-size: 1.1rem;
      padding: 15px 30px;
      border-radius: 30px;
      text-transform: uppercase;
      font-weight: bold;
      border: none;
      transition: all 0.3s ease;
    }

    .hero-section .btn:hover {
      background-color: #fff !important;
      color: #000 !important;
      border: 1px solid #fff;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .contact-section {
      background-color: #fff;
      padding: 60px 0;
    }

    .contact-section h2 {
      font-size: 2.5rem;
      color: #2A3D66;
      margin-bottom: 30px;
      text-align: center;
      animation: fadeInUp 1.5s ease-out;
    }

    .contact-form {
      background-color: #F7E7CE;
      padding: 50px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      animation: zoomIn 1.5s ease-out;
      margin-top: 50px;
    }

    .contact-form label {
      font-size: 1.1rem;
      font-weight: bold;
      color: #2A3D66;
    }

    .contact-form input,
    .contact-form textarea {
      width: 100%;
      padding: 15px;
      border: 1px solid #A9BCA9;
      border-radius: 5px;
      margin-bottom: 20px;
      font-size: 1rem;
      background-color: #fff;
      color: #636E72;
    }

    .contact-form input:focus,
    .contact-form textarea:focus {
      border-color: #D4AF37;
      box-shadow: 0 0 5px #D4AF37;
      outline: none;

    }


    .contact-form button {
      background-color: #D4AF37;
      color: #fff;
      font-size: 1.1rem;
      font-weight: bold;
      padding: 15px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      width: 100%;
    }

    .contact-form button:hover {
      background-color: #F26D6D;
      color: #fff;
    }

    .contact-info {
      background-color: rgb(225, 228, 235);
      color: #F7E7CE;
      padding: 40px 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      animation: fadeIn 1.5s ease-out;
    }

    .contact-info p {
      font-size: 1.1rem;
    }

    .contact-info i {
      color: #D4AF37;
      margin-right: 10px;
    }

    .contact-info .fa-phone-alt,
    .contact-info .fa-envelope,
    .contact-info .fa-map-marker-alt {
      font-size: 1.5rem;
    }

    .contact-info p {
      margin-bottom: 20px;
    }

    .contact-info a {
      color: #D4AF37;
      text-decoration: none;
    }

    .contact-info a:hover {
      color: #F26D6D;
    }

    /* Social Media Icons */
    .social-media {
      text-align: center;
      margin-top: 30px;
    }

    .social-media .social-icon {
      margin: 0 15px;
      color: #D4AF37;
      font-size: 1.5rem;
      transition: color 0.3s ease;
    }

    .social-media .social-icon:hover {
      color: #F26D6D;
    }

    .contact-info-card {
      background-image: url('img/contact_us.jpg');
      background-size: cover;
      background-position: center;
      height: 100%;
      position: relative;
      padding: 20px;
    }


    .contact-info-card .overlay-text {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      color: white;
      text-align: center;
      background-color: rgba(0, 0, 0, 0.6);
      padding: 20px;
      border-radius: 10px;
      z-index: 1;
    }
  </style>
</head>

<body>

  <!-- PHP Include Navbar -->
  <?php include 'navbar.php'; ?>

  <div class="contact-header">
    <div class="contact-header-text">
      <h1>Contact Us</h1>
      <p>We're here to assist you. Reach out to us for any inquiries or support.</p>
    </div>
  </div>


  <section class="contact-section" id="contact">
    <div class="container">
      <div class="row">

        <div class="col-lg-6 mb-4">
          <div class="position-relative">
            <img src="img/contact_us.jpg" class="img-fluid" alt="Contact Image" style="width: 100%; height: 100%; object-fit: cover;">

            <div class="position-absolute top-50 start-50 translate-middle text-white text-center" style="z-index: 1; background-color: rgba(0, 0, 0, 0.6); padding: 20px; border-radius: 10px;">
              <p>
                <strong>Call Us:</strong><br>
                +94 719946378<br>
                +94 715656812
              </p>
              <p>
                <strong>Location:</strong><br>
                52/B/2 Suriyapaluwa rd,<br>
                Eldeniya , Kadawatha
              </p>
              <p>
                <strong>Business Hours:</strong><br>
                Mon - Fri: 10 AM - 8 PM<br>
                Sat - Sun: Closed
              </p>
            </div>
          </div>
        </div>

        <!-- Contact Form -->
        <div class="col-lg-6">
          <div class="contact-form">
            <?php if (!empty($error_message)): ?>
              <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
              </div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
              <div class="alert alert-success" role="alert">
                <?php echo $success_message; ?>
              </div>
            <?php endif; ?>
            <form method="POST" action="">
              <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name"
                  value="<?php echo ($user_name && $user_name !== 'Guest' ? htmlspecialchars($user_name) : ''); ?>" required>
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter a valid email" value="<?php echo htmlspecialchars($user_email); ?>" required>
              </div>
              <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea class="form-control" id="message" name="message" rows="3" placeholder="Enter your message" required></textarea>
              </div>
              <button type="submit" class="btn btn-submit w-100">Submit</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <?php include 'footer.html'; ?>


  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>