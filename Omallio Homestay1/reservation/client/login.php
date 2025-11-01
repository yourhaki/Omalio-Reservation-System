<?php

include '../components/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login-btn'])) {
        $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
        $password = $_POST['password'];
    }

    if (empty($username) || empty($password)) {
        echo "<script>alert('Both username and password are required!')</script>";
        exit;
    }

    $get_user = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $get_user->execute([$username]);

    if ($get_user->rowCount() > 0) {

        $user = $get_user->fetch(PDO::FETCH_ASSOC);
        $hashedPassword = $user['password'];

        if (password_verify($password, $hashedPassword)) {

            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            echo "Login successful! Redirecting...";
            header("location: ../index.php");
            exit;
        } else {
            echo "<script>alert('Incorrect password!'</script>";
        }
    } else {
        echo "<script>alert('Username not found!')</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

    <!-- login section starts  -->

    <section class="form-container" style="min-height: 100vh;">

        <form action="" method="POST">
            <h3>Welcome User!</h3>
            <input type="text" name="username" placeholder="Enter Username" maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="password" placeholder="Enter Password" maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
            <div style='display: flex; flex-direction: column;'>
                <input type="submit" value="login now" name="login-btn" class="btn">
                <a href="./register.php" class='btn'>Create account</a>
            </div>
        </form>

    </section>

    <!-- login section ends -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <?php include '../components/message.php'; ?>

</body>

</html>