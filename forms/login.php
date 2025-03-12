<?php
session_start();
require '../connection/connection.php'; 

$email = $password = "";
$emailErr = $passwordErr = "";

if (isset($_COOKIE['loginEmail'])) {
    $email = $_COOKIE['loginEmail'];
}
if (isset($_COOKIE['loginPassword'])) {
    $password = $_COOKIE['loginPassword'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["loginEmail"])) {
        $emailErr = "Email is required";
    } else {
        $email = $_POST["loginEmail"];
    }

    if (empty($_POST["loginPassword"])) {
        $passwordErr = "Password is required";
    } else {
        $password = $_POST["loginPassword"];
    }

    if (empty($emailErr) && empty($passwordErr)) {
        $sql = "SELECT id, password, full_name, role FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = $user['role']; 
                $_SESSION['logged_in'] = true; 

                if (!empty($_POST['rememberMe'])) {
                    setcookie("loginEmail", $email, time() + (30 * 24 * 60 * 60), "/"); // 30 days
                    setcookie("loginPassword", $password, time() + (30 * 24 * 60 * 60), "/"); // 30 days
                } else {
                    setcookie("loginEmail", "", time() - 3600, "/");
                    setcookie("loginPassword", "", time() - 3600, "/");
                }

                if ($user['role'] == 'admin') {
                    $_SESSION['admin_logged_in'] = true;
                    header("Location: ../admin/index.php");
                } else {
                    header("Location: ../Home.php");
                }
                exit();
            } else {
                $passwordErr = "Incorrect password!";
            }
        } else {
            $emailErr = "No account found with that email!";
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
    <title>Login Form</title>
    <link rel="icon" href="../logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="login-wrapper shadow-lg">
            <div class="login-left">
                <img src="gift.jpg" alt="Gift Image">
            </div>
            <div class="login-right">
                <div class="w-100">
                    <h1 class="text-center mb-3 font-weight-bold">Login</h1>
                    <h3 class="text-center mb-3">Welcome to the Gifty House</h3>
                    <p class="text-center text-muted mb-4">Find the perfect gifts for every occasion!</p>
                    
                    <!-- Display error messages -->
                    <?php if (!empty($emailErr) || !empty($passwordErr)): ?>
                        <div class="alert alert-danger text-center">
                            <?php 
                                if (!empty($emailErr)) echo htmlspecialchars($emailErr) . "<br>";
                                if (!empty($passwordErr)) echo htmlspecialchars($passwordErr);
                            ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="login.php">
                        <div class="form-group">
                            <label for="loginEmail">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="loginEmail" name="loginEmail" placeholder="Enter Email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <label for="loginPassword">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="loginPassword" name="loginPassword" placeholder="Enter Password" value="<?php echo htmlspecialchars($password ?? ''); ?>" required>
                                <span class="input-group-text" id="togglePassword" style="cursor: pointer;"><i class="bi bi-eye" id="eyeIcon"></i></span>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <input type="checkbox" id="rememberMe" name="rememberMe">
                            <label for="rememberMe">Remember Me</label>
                        </div>
                        <button type="submit" name="login" class="btn login-button mt-4">Login</button>
                    </form>
                    <a href="register.php" class="register-link">Don't have an account? Register</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle the password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('loginPassword');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', () => {
            // Toggle the password field type
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;

            // Toggle the eye icon
            eyeIcon.classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>
