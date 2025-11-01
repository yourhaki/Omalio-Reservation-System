<!-- header section starts  -->
<?php

if (session_status() == PHP_SESSION_NONE) {
   session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
   session_start();
   session_unset();
   session_destroy();
   header("Location: index.php");
   exit();
}

?>

<section class="header">

   <div class="flex">
      <a href="#home" class="logo">Ommalio's Homestay</a>
      <div style='display: flex; gap: 10px;'>
         <?php if (isset($_SESSION['user_id'])): ?>
            <form method='POST' id='logout-form'>
               <button class='btn' name='logout' onclick="return confirm('Are you sure you want to log out?')">Logout</button>
            </form>
         <?php else: ?>
            <a href="./client/login.php" class="btn">Login</a>
         <?php endif; ?>
      </div>
      <div id="menu-btn" class="fas fa-bars"></div>
   </div>

   <nav class="navbar">
      <a href="index.php#home">Home</a>
      <a href="index.php#about">Rooms</a>
      <a href="index.php#gallery">Gallery</a>
      <a href="index.php#location">Location</a>
      <a href="index.php#reservation">Reservation</a>
      <a href="index.php#faqs">Faqs</a>
      <a href="bookings.php">My bookings</a>
   </nav>

</section>

<!-- header section ends -->