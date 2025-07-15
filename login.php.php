<?php
session_start();

include __DIR__ . '/../includes/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $message = "Please fill all fields.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $name, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;

                header("Location: vehicleslisting.php");
                exit();
            } else {
                $message = "Incorrect password.";
            }
        } else {
            $message = "Email not registered.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Login - Vehicle Bazar</title>

    <!-- Internal CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('../assets/images/backgroundimage2.jpg') no-repeat center center fixed;
            background-size: 1300px 600px;
            padding: 100px;
            margin: 0;
            min-height: 50vh;
        }

        .container {
            max-width: 400px;
            background: white;
            margin: auto;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 8px #ccc;
        }

        h2 {
            text-align: center;
        }

        label {
            display: block;
            margin-top: 12px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            margin-top: 20px;
            width: 100%;
            background-color: #28a745;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
        }

        .message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>User Login</h2>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <label>Email:</label>
        <input type="email" name="email" required />

        <label>Password:</label>
        <input type="password" name="password" required />

        <button type="submit">Submit</button>
    </form>

    <p class="login-link">Don't have an account? <a href="register.php">Register here</a></p>
</div>

<!-- JavaScript file link -->
<script src="../assets/js/user_register.js"></script>
</body>
</html>
