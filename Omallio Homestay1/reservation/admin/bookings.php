<?php

include '../components/connect.php';

if (isset($_COOKIE['admin_id'])) {
   $admin_id = $_COOKIE['admin_id'];
} else {
   $admin_id = '';
   header('location:login.php');
}

if (isset($_POST['delete'])) {

   $delete_id = $_POST['delete_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_delete = $conn->prepare("SELECT * FROM `bookings` WHERE booking_id = ?");
   $verify_delete->execute([$delete_id]);

   if ($verify_delete->rowCount() > 0) {
      $delete_bookings = $conn->prepare("DELETE FROM `bookings` WHERE booking_id = ?");
      $delete_bookings->execute([$delete_id]);
      $success_msg[] = 'Booking Canceled!';
   } else {
      $warning_msg[] = 'Booking Canceled already!';
   }
}

if (isset($_POST['accept'])) {

   $accept_id = $_POST['accept_id'];
   $accept_id = filter_var($accept_id, FILTER_SANITIZE_STRING);

   $verify_accept = $conn->prepare("SELECT * FROM `bookings` WHERE booking_id = ?");
   $verify_accept->execute([$accept_id]);

   if ($verify_accept->rowCount() > 0) {
      $accept_booking = $conn->prepare("UPDATE `bookings` SET status = 'accepted' WHERE booking_id = ?");
      $accept_booking->execute([$accept_id]);
      $success_msg[] = 'Booking accepted!';
   } else {
      $warning_msg[] = 'Booking Accepted Already!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Bookings</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

   <!-- header section starts  -->
   <?php include '../components/admin_header.php'; ?>
   <!-- header section ends -->

   <!-- bookings section starts  -->

   <section class="grid">

      <h1 class="heading">bookings</h1>

      <div class="box-container">

         <?php
         $select_bookings = $conn->prepare("SELECT * FROM `bookings`");
         $select_bookings->execute();
         if ($select_bookings->rowCount() > 0) {
            while ($fetch_bookings = $select_bookings->fetch(PDO::FETCH_ASSOC)) {
         ?>
            <div class="box">
   <div style='display: flex; align-items: center; justify-content: space-between;'>
      <h1>Proof of Payment:</h1>
      <img src="../receipt/<?= $fetch_bookings['receipt'] ?>" width='100' alt="">
   </div>
   <p>Booking id : <span><?= $fetch_bookings['booking_id']; ?></span></p>
   <p>Name : <span><?= $fetch_bookings['name']; ?></span></p>
   <p>Email : <span><?= $fetch_bookings['email']; ?></span></p>
   <p>Number : <span><?= $fetch_bookings['number']; ?></span></p>
   <p>Check in : <span><?= $fetch_bookings['check_in']; ?></span></p>
   <p>Check out : <span><?= $fetch_bookings['check_out']; ?></span></p>
   <p>Rooms : <span><?= $fetch_bookings['rooms']; ?></span></p>
   <p>Adults : <span><?= $fetch_bookings['adults']; ?></span></p>
   <p>Childs : <span><?= $fetch_bookings['childs']; ?></span></p>
   <p>Date Booked : <span><?= date("F j, Y, g:i a", strtotime($fetch_bookings['date_booked'])); ?></span></p>

   

   <?php if ($fetch_bookings['status'] === 'accepted') { ?>
      <p style="color: green; font-weight: bold;">Booking already accepted!</p>
   <?php } else { ?>
      <form action="" method="POST">
         <input type="hidden" name="accept_id" value="<?= $fetch_bookings['booking_id']; ?>">
         <input type="submit" value="Accept Booking" onclick="return confirm('Are you sure you want to accept this booking?');" name="accept" class="btn">
      </form>
   <?php } ?>

   <form action="" method="POST">
      <input type="hidden" name="delete_id" value="<?= $fetch_bookings['booking_id']; ?>">
      <input type="submit" 
             value="<?= $fetch_bookings['status'] === 'deleted' ? 'Booking already canceled' : 'Delete Booking'; ?>" 
             onclick="return confirm('Are you sure you want to delete this booking?');" 
             name="delete" 
             class="btn" 
             <?= $fetch_bookings['status'] === 'deleted' ? 'disabled' : ''; ?>>
   </form>
</div>


            <?php
            }
         } else {
            ?>
            <div class="box" style="text-align: center;">
               <p>no bookings found!</p>
               <a href="dashboard.php" class="btn">go to home</a>
            </div>
         <?php
         }
         ?>

      </div>

   </section>

   <!-- bookings section ends -->

   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

   <!-- custom js file link  -->
   <script src="../js/admin_script.js"></script>

   <?php include '../components/message.php'; ?>

</body>

</html>