<?php

if (session_status() == PHP_SESSION_NONE) {
   session_start();
}

include 'components/connect.php';

$user_id = $_SESSION['user_id'] ?? '';

if (isset($_POST['cancel'])) {
   $booking_id = $_POST['booking_id'];
   $booking_id = filter_var($booking_id, FILTER_SANITIZE_STRING);

   // Fetch the booking to check the current status
   $verify_booking = $conn->prepare("SELECT * FROM `bookings` WHERE booking_id = ? AND user_id = ?");
   $verify_booking->execute([$booking_id, $user_id]);

   if ($verify_booking->rowCount() > 0) {
      $fetch_booking = $verify_booking->fetch(PDO::FETCH_ASSOC);

      if ($fetch_booking['status'] === 'BOOKING CONFIRMED') {
         $warning_msg[] = 'Booking is already confirmed by the admin and cannot be canceled!';
      } elseif ($fetch_booking['status'] === 'BOOKING CANCELED') {
         $warning_msg[] = 'Booking is already canceled!';
      } else {
         // Proceed with cancellation
         $update_booking = $conn->prepare("UPDATE `bookings` SET status = 'BOOKING CANCELED' WHERE booking_id = ?");
         $update_booking->execute([$booking_id]);
         $success_msg[] = 'Booking canceled successfully!';
      }
   } else {
      $warning_msg[] = 'Booking not found or unauthorized access!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>My Bookings</title>

   <!-- Swiper CSS -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS -->
   <link rel="stylesheet" href="css/style.css">
</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <!-- Booking Section -->
   <section class="bookings">
      <h1 class="heading">My Bookings</h1>

      <div class="box-container">
         <?php
         $select_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ?");
         $select_bookings->execute([$user_id]);

         if ($select_bookings->rowCount() > 0) {
            while ($fetch_booking = $select_bookings->fetch(PDO::FETCH_ASSOC)) {
         ?>
               <div class="box">
                  <p>Booking ID : <span><?= $fetch_booking['booking_id']; ?></span></p>
                  <p>Name : <span><?= $fetch_booking['name']; ?></span></p>
                  <p>Email : <span><?= $fetch_booking['email']; ?></span></p>
                  <p>Number : <span><?= $fetch_booking['number']; ?></span></p>
                  <p>Check-in : <span><?= $fetch_booking['check_in']; ?></span></p>
                  <p>Check-out : <span><?= $fetch_booking['check_out']; ?></span></p>
                  <p>Rooms : <span><?= $fetch_booking['rooms']; ?></span></p>
                  <p>Adults : <span><?= $fetch_booking['adults']; ?></span></p>
                  <p>Children : <span><?= $fetch_booking['childs']; ?></span></p>
                  <p>Date Booked : <span><?= date("F j, Y, g:i a", strtotime($fetch_booking['date_booked'])); ?></span></p>
                  <p>Receipt: <img src="receipt/<?= $fetch_booking['receipt']; ?>" alt="Proof of Payment" width="100"></p>
                  <p>Status : <span><?= $fetch_booking['status']; ?></span></p>

                  <form action="" method="POST">
                     <input type="hidden" name="booking_id" value="<?= $fetch_booking['booking_id']; ?>">

                     <?php if ($fetch_booking['status'] === 'BOOKING CONFIRMED'): ?>
                        <button type="button" class="btn btn-secondary" disabled>CAN'T CANCEL</button>
                     <?php elseif ($fetch_booking['status'] === 'BOOKING CANCELED'): ?>
                        <button type="button" class="btn btn-danger" disabled>BOOKING CANCELED</button>
                     <?php else: ?>
                        <input type="submit" value="Cancel Booking" name="cancel" class="btn btn-warning" onclick="return confirm('Are you sure you want to cancel this booking?');">
                     <?php endif; ?>
                  </form>
               </div>
         <?php
            }
         } else {
         ?>
            <div class="box" style="text-align: center;">
               <p style="padding-bottom: .5rem; text-transform: capitalize;">No bookings found!</p>
               <a href="index.php#reservation" class="btn">Book Now</a>
            </div>
         <?php
         }
         ?>
      </div>
   </section>

   <?php include 'components/footer.php'; ?>

   <!-- Swiper JS -->
   <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

   <!-- SweetAlert -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

   <!-- Custom JS -->
   <script src="js/script.js"></script>

   <?php include 'components/message.php'; ?>

</body>

</html>
