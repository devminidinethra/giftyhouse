<?php
include('connection/connection.php');


session_start();
$user_id = $_SESSION['user_id']; 


$query = "SELECT m.message, m.reply, u.full_name, u.email FROM message m
          JOIN users u ON m.user_id = u.id
          WHERE m.user_id = ? ORDER BY m.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id); 
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $messages = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $messages = [];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Messages</title>
    <link rel="icon" href="../logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">

    <style>
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: aliceblue;
            margin: 0;
            padding: 0;
        }

        .message-card {
            border: none;
            border-radius: 10px;
            margin-bottom: 30px;
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 25px;
            transition: transform 0.3s, box-shadow 0.3s ease-in-out;
        }

        .message-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.2);
        }

        .message-card h5 {
            font-weight: bold;
            color: #2a3d66;
            margin-bottom: 10px;
        }

        .message-card h6 {
            font-size: 0.95rem;
            color: #777;
            margin-bottom: 18px;
        }

        .message-card .message-text {
            font-size: 1rem;
            color: #444;
        }

        .message-card .reply {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin-top: 18px;
            color: #2a3d66;
            font-style: italic;
        }

        /* Button Styling */
        .back-btn {
            background-color: #D4AF37;
            color: #ffffff;
            border: none;
            border-radius: 30px;
            padding: 12px 24px;
            font-size: 1.1rem;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s, box-shadow 0.3s ease-in-out;
        }

        .back-btn:hover {
            background-color: #F26D6D;
            color: white;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

    

        /* Top bar section */
        .top-bar {
            background-color: #ffffff;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .top-bar h4 {
            font-size: 1.6rem;
            color: #2a3d66;
        }

        /* Adjust for responsive design */
        @media (max-width: 768px) {
            .top-bar {
                padding: 15px;
            }

            .back-btn {
                font-size: 1rem;
                padding: 10px 20px;
            }

            .message-card {
                padding: 18px;
            }
        }
    </style>
</head>

<body>

    <main class="main-content">
        <div class="container">
            <div class="top-bar d-flex justify-content-between align-items-center">
                <h4>Your Messages</h4>
                <a href="profile.php" class="btn back-btn">Back to Profile</a>
            </div>

            <div class="messages-section">
                <?php if (empty($messages)): ?>
                    <p class="text-center text-muted">You don't have any messages yet.</p>
                <?php else: ?>
                    <?php foreach ($messages as $message): ?>
                        <div class="message-card">
                            <h5><?= htmlspecialchars($message['full_name']) ?></h5>
                            <h6><i class="bi bi-envelope"></i> <?= htmlspecialchars($message['email']) ?></h6>
                            <p class="message-text"><?= nl2br(htmlspecialchars($message['message'])) ?></p>

                            <?php if ($message['reply']): ?>
                                <div class="reply">
                                    <p><strong>Reply:</strong> <?= nl2br(htmlspecialchars($message['reply'])) ?></p>
                                </div>
                            <?php else: ?>
                                <div class="reply">
                                    <p><strong>Reply:</strong> No reply yet.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'footer.html'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
