<?php

include '../components/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['register-btn']) {

        $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];

        if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
            echo "<script>alert('All fields are required!')</script>";
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Invalid email format!')</script>";
            exit;
        }

        if ($password !== $confirmPassword) {
            echo "<script>alert('Passwords do not match!')</script>";
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $check_email = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $check_email->execute([$email]);


        if ($check_email->rowCount() > 0) {
            echo "<script>alert('Email already registered!')</script>";
        } else {
            $insert_user = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $insert_user->execute([$username, $email, $hashedPassword]);

            header('location: ../index.php');
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login User</title>

x    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

    <!-- register section starts  -->

    <section class="form-container">

        <form action="" method="POST">
            <h3>User Registration</h3>
            <input type="text" name="username" placeholder="Enter Username" maxlength="20" class="box" required>
            <input type="email" name="email" placeholder="Enter Email Address" class="box" required>
            <input type="password" name="password" placeholder="Enter Password" maxlength="20" class="box" required>
            <input type="password" name="confirm_password" placeholder="Confirm password" maxlength="20" class="box" required>
            <div style='display: flex; flex-direction: column;'>
                <input type="submit" value="register now" name="register-btn" class="btn">
                <a href="./login.php" class='btn'>Already have an account</a>
            </div>
        </form>

    </section>

    <!-- register section ends -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

    <?php include '../components/message.php'; ?>

</body>

</html>