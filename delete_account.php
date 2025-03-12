<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Deleted</title>
    <link rel="icon" href="logo.png" type="image/png">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
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

        .container {
            max-width: 600px;
            padding-top: 50px;
            padding-bottom: 50px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: white;
            padding: 40px;
            text-align: center;
        }

        .card-header {
            font-size: 1.5rem;
            font-weight: bold;
            color: rgb(31, 56, 106);
        }

        .card-body {
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 20px;
        }

        .delete-btn {
            font-size: 1.1rem;
            padding: 10px 20px;
            border-radius: 50px;
            background-color: #D4AF37;
            color: #2A3D66;
        }

        .delete-btn:hover {
            background-color: #F26D6D;
            color: white;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <!-- Back Button -->
    <a href="profile.php" class="back-button" title="Go Back">
        <i class="bi bi-arrow-left"></i>
    </a>

    <div class="container">
        <div class="card">
            <div class="card-header">
                Account Deleted
            </div>
            <div class="card-body">
                <h3 class="text-success">We're sorry to see you go!</h3>
                <p>Your account has been successfully deleted. If you change your mind, you can always register again.</p>
                <a href="index.html" class="btn btn-warning btn-lg delete-btn">Go back to the homepage</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
