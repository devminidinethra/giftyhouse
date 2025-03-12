<?php
session_start();
require '../connection/connection.php';

$name = $email = $password = $confirmPassword = "";
$nameErr = $emailErr = $passwordErr = $confirmPasswordErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = $_POST["name"];
    }

    if (empty($_POST["registerEmail"])) {
        $emailErr = "Email is required";
    } else {
        $email = $_POST["registerEmail"];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    if (empty($_POST["registerPassword"])) {
        $passwordErr = "Password is required";
    } else {
        $password = $_POST["registerPassword"];
    }

    if (empty($_POST["confirmPassword"])) {
        $confirmPasswordErr = "Confirm password is required";
    } else {
        $confirmPassword = $_POST["confirmPassword"];
    }

    if (empty($nameErr) && empty($emailErr) && empty($passwordErr) && empty($confirmPasswordErr)) {
        if ($password == $confirmPassword) {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Check if email already exists in the database
            $sql = "SELECT id FROM users WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {

                $emailErr = "Email already exists!";
            } else {

                // Insert user into the users table only
                $sql = "INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, 'user')";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $name, $email, $hashedPassword);

                if ($stmt->execute()) {
                    // Redirect to login page after successful registration
                    header("Location: ../forms/login.php");
                    exit();
                }
            }
        } else {
            $confirmPasswordErr = "Passwords do not match!";
        }
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="icon" href="../logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <div class="register-wrapper shadow-lg">
            <div class="register-left">
                <img src="gift.jpg" alt="Gift Image">
            </div>
            <div class="register-right">
                <div class="w-100">
                    <h1 class="text-center mb-3 font-weight-bold">Register</h1>
                    <h3 class="text-center mb-3">Join the Gifty House</h3>
                    <p class="text-center text-muted mb-4">Create an account to access exclusive gifts!</p>
                    <form method="post" action="register.php">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Full Name" required pattern="[A-Za-z ]+" title="Only alphabetic characters and spaces are allowed">
                                <span class="text-danger"><?php echo $nameErr; ?></span>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <label for="registerEmail">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="registerEmail" name="registerEmail" placeholder="Enter Email" required>
                                <span class="text-danger"><?php echo $emailErr; ?></span>
                            </div>
                            <div class="form-group mt-3">
                                <label for="registerPassword">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" id="registerPassword" name="registerPassword" placeholder="Enter Password" required minlength="8" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, and one number">
                                    <span class="input-group-text" id="togglePassword" style="cursor: pointer;"><i class="bi bi-eye" id="eyeIcon"></i></span>
                                </div>
                                <span class="text-danger"><?php echo $passwordErr; ?></span>
                            </div>

                            <div class="form-group mt-3">
                                <label for="confirmPassword">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required minlength="8">
                                    <span class="input-group-text" id="toggleConfirmPassword" style="cursor: pointer;"><i class="bi bi-eye" id="eyeIconConfirm"></i></span>
                                </div>
                                <span class="text-danger"><?php echo $confirmPasswordErr; ?></span>
                            </div>


                            <button type="submit" class="btn register-button mt-4">Register</button>
                    </form>
                    <a href="login.php" class="login-link">Already have an account? Login</a>
                </div>
            </div>
        </div>
    </div>


    <script>
        // Toggle the password visibility for registerPassword field
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('registerPassword');
        const eyeIcon = document.getElementById('eyeIcon');

        // Toggle the password visibility for confirmPassword field
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordField = document.getElementById('confirmPassword');
        const eyeIconConfirm = document.getElementById('eyeIconConfirm');

        // Add event listener for the registerPassword field
        togglePassword.addEventListener('click', () => {
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
            eyeIcon.classList.toggle('bi-eye-slash');
        });

        // Add event listener for the confirmPassword field
        toggleConfirmPassword.addEventListener('click', () => {
            const type = confirmPasswordField.type === 'password' ? 'text' : 'password';
            confirmPasswordField.type = type;
            eyeIconConfirm.classList.toggle('bi-eye-slash');
        });
    </script>
</body>

</html