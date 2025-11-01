<?php

include 'components/connect.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   if (isset($_POST['check'])) {
      $check_in = filter_var($_POST['check_in'], FILTER_SANITIZE_STRING);

      $total_rooms = 0;
      $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
      $check_bookings->execute([$check_in]);

      while ($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)) {
         $total_rooms += $fetch_bookings['rooms'];
      }

      if ($total_rooms >= 30) {
         $warning_msg[] = 'rooms are not available';
      } else {
         $success_msg[] = 'rooms are available';
      }
   }

   if (isset($_POST['book'])) {

      if (!isset($_SESSION['user_id'])) {
         echo "<script>
                  alert('Please log in before making a reservation.');
                  window.location.href = 'index.php';
               </script>";
         exit;
      }

      $user_id = $_SESSION['user_id'];

      $booking_id = create_unique_id();
      $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
      $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
      $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
      $rooms = filter_var($_POST['rooms'], FILTER_SANITIZE_STRING);
      $check_in = filter_var($_POST['check_in'], FILTER_SANITIZE_STRING);
      $check_out = filter_var($_POST['check_out'], FILTER_SANITIZE_STRING);
      $adults = filter_var($_POST['adults'], FILTER_SANITIZE_STRING);
      $childs = filter_var($_POST['childs'], FILTER_SANITIZE_STRING);

      $fileName = $_FILES['receipt']['name'];
      $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
      $allowdeFormat = array("jpg", "jpeg", "png");

      $temp =  $_FILES['receipt']['tmp_name'];
      // $path = "receipt/" . $fileName;

      $total_rooms = 0;

      $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
      $check_bookings->execute([$check_in]);

      while ($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)) {
         $total_rooms += $fetch_bookings['rooms'];
      }

      if ($total_rooms >= 30) {
         $warning_msg[] = 'rooms are not available';
      } else {

         $verify_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ? AND name = ? AND email = ? AND number = ? AND rooms = ? AND check_in = ? AND check_out = ? AND adults = ? AND childs = ?");
         $verify_bookings->execute([$user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);

         if ($verify_bookings->rowCount() > 0) {
            $warning_msg[] = 'room booked already!';
         } else {
            if (in_array($fileExtension, $allowdeFormat)) {

               $uniqueFileName = uniqid('receipt_', true) . '.' . $fileExtension;
               $path = "receipt/" . $uniqueFileName;

               if (move_uploaded_file($_FILES['receipt']['tmp_name'], $path)) {
                  $book_room = $conn->prepare("INSERT INTO `bookings`(booking_id, user_id, name, email, number, rooms, check_in, check_out, adults, childs, receipt) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
                  $book_room->execute([$booking_id, $user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs, $uniqueFileName]);
                  $success_msg[] = 'Room booked successfully!';
               } else {
                  echo "Failed to upload receipt.";
               }
            }
         }
      }
   }

   if (isset($_POST['send'])) {
      $id = create_unique_id();
      $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
      $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
      $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
      $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);

      $verify_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
      $verify_message->execute([$name, $email, $number, $message]);

      if ($verify_message->rowCount() > 0) {
         $warning_msg[] = 'message sent already!';
      } else {
         $insert_message = $conn->prepare("INSERT INTO `messages`(id, name, email, number, message) VALUES(?,?,?,?,?)");
         $insert_message->execute([$id, $name, $email, $number, $message]);
         $success_msg[] = 'message sent successfully!';
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
   <title>Home</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <!-- home section starts  -->

   <section class="home" id="home">

      <div class="swiper home-slider">

         <div class="swiper-wrapper">

            <div class="box swiper-slide">
               <img src="images/om.jpg" alt="">
               <div class="flex">
                  <h3>AVAILABITY</h3>
               </div>
            </div>

            <div class="box swiper-slide">
               <img src="images/F4.jpg" alt="">
               <div class="flex">
                  <h3>AVAILABITY</h3>
               </div>
            </div>

            <div class="box swiper-slide">
               <img src="images/F5.jpg" alt="">
               <div class="flex">
                  <h3>AVAILABITY</h3>
               </div>
            </div>

         </div>

         <div class="swiper-button-next"></div>
         <div class="swiper-button-prev"></div>

      </div>

   </section>

   <!-- home section ends -->

   <!-- availability section starts  -->

  <!-- availability section starts  -->
<section class="availability" id="availability">
   <form action="" method="post">
      <div class="flex">
         <div class="box">
            <p>Check in <span>*</span></p>
            <input type="date" name="check_in" class="input" required id="check_in">
         </div>
         <div class="box">
            <p>Check out <span>*</span></p>
            <input type="date" name="check_out" class="input" required id="check_out">
         </div>
         <div class="dropdown">
                 <p>GUEST <span>*</span></p>
    <button class="dropdown-btn" id="guestButton"> Adults, 0 Children</button>
    <div class="dropdown-content" id="dropdownContent">
      <p>
        Adults
        <span>
          <button class="decrement" data-type="adults" disabled>-</button>
          <span id="adultCount">0</span>
          <button class="increment" data-type="adults">+</button>
        </span>
      </p>
      <p>
        Children
        <span>
          <button class="decrement" data-type="children" disabled>-</button>
          <span id="childCount">0</span>
          <button class="increment" data-type="children">+</button>
        </span>
      </p>
      <button class="close-btn" id="closeDropdown">Close</button>
    </div>
  </div>
  <script src="script.js"></script>
         <div class="box">
            <p>Room Type<span>*</span></p>
            <select name="rooms" class="input" required>
               <option value="1">Couple Room</option>
               <option value="2">Family Room</option>
               <option value="3">Barkada</option>
            </select>
         </div>
      </div>
      <input type="submit" value="Check Availability" name="check" class="btn">
   </form>
</section>

<script>
   // Get today's date in the format yyyy-mm-dd
   const today = new Date().toISOString().split('T')[0];

   // Set the min attribute for the date inputs
   document.getElementById('check_in').setAttribute('min', today);
   document.getElementById('check_out').setAttribute('min', today);
</script>


   <!-- availability section ends -->

   <!-- about section starts  -->

   <section class="about" id="about">
      <h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font" style="font-size: 2.5rem; color: #00A86B;">OUR ROOMS</h2>

      <div class="row">
         <div class="room">
            <h1>Family Room</h1>
            <div class="image">
               <img src="images/family.jpg" alt="Luxury Room 1 Image">
            </div>
            <div class="content">
               <p>Spacious and comfortable</p>
               <a href="#reservation" class="btn">Make a Reservation</a>
            </div>
         </div>

         <div class="room">
            <h1>Couple Room</h1>
            <div class="image">
               <img src="images/barkada.jpg" alt="Deluxe Room 2 Image">
            </div>
            <div class="content">
               <p>Perfect for a relaxing stay</p>
               <a href="#reservation" class="btn">Make a Reservation</a>
            </div>
         </div>

         <div class="room">
            <h1>Barkada Room</h1>
            <div class="image">
               <img src="images/couple.jpg" alt="Best Room 3 Image">
            </div>
            <div class="content">
               <p>Suite for hangout</p>
               <a href="#reservation" class="btn">Make a Reservation</a>
            </div>
         </div>
      </div>


      <section class="gallery" id="gallery">
         <h3>OUR GALLERY</h3>
         <div class="swiper gallery-slider">
            <div class="swiper-wrapper">
               <img src="images/F1.jpg" class="swiper-slide" alt="">
               <img src="images/F2.jpg" class="swiper-slide" alt="">
               <img src="images/F3.jpg" class="swiper-slide" alt="">
               <img src="images/F4.jpg" class="swiper-slide" alt="">
               <img src="images/F5.jpg" class="swiper-slide" alt="">
            </div>
            <div class="swiper-pagination"></div>
         </div>

      </section>

      <!-- gallery section ends -->
      <section class="location" id="location">
         <h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">Location</h2>
         <div class="container">
            <div class="row">
               <div class="col-lg-8 col-md-8">
                  <iframe class="w-100 h-500" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3831.8363705762913!2d120.86919407426554!3d16.177386436535144!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3391030253a95615%3A0x6507009cf57fae4!2sOmallio%E2%80%99s%20Homestay!5e0!3m2!1sen!2sph!4v1702293611512!5m2!1sen!2sph" loading="lazy"></iframe>
               </div>
               <div class="col-lg-4 col-md-4">
                  <!-- Additional content -->
               </div>
            </div>
         </div>

         <!-- services section ends -->

         <!-- reservation section starts  -->

         <section class="reservation" id="reservation">
            <form action="" method="post" enctype='multipart/form-data'>
               <h3>Make a reservation</h3>
               <div class="flex">
                  <div class="box">
                     <p>your name <span>*</span></p>
                     <input type="text" name="name" maxlength="50" required placeholder="enter your name" class="input">
                  </div>
                  <div class="box">
                     <p>your email <span>*</span></p>
                     <input type="email" name="email" maxlength="50" required placeholder="enter your email" class="input">
                  </div>
                  <div class="box">
                     <p>your number <span>*</span></p>
                     <input type="number" name="number" maxlength="10" min="0" max="9999999999" required placeholder="enter your number" class="input">
                  </div>
                  <div class="box">
                     <p>rooms <span>*</span></p>
                     <select name="rooms" class="input" required>
                        <option value="1" selected>1 Room</option>
                        <option value="2">2 Rooms</option>
                        <option value="3">3 Rooms</option>
                        <option value="4">4 Rooms</option>
                        <option value="5">5 Rooms</option>
                        <option value="6">6 Rooms</option>
                     </select>
                  </div>
                  <div class="box">
                     <p>check in <span>*</span></p>
                     <input type="date" name="check_in" class="input" required>
                  </div>
                  <div class="box">
                     <p>check out <span>*</span></p>
                     <input type="date" name="check_out" class="input" required>
                  </div>
                  <div class="box">
                     <p>Types of Room<span>*</span></p>
                     <select name="room_type" class="input" required id='types_of_room'>
                        <option value="familiy_room" selected>Family Room</option>
                        <option value="couple_room">Couple Room</option>
                        <option value="barkada_room">Barkada Room</option>
                     </select>
                  </div>
                  <div class="box">
                     <p>Number of Nights<span>*</span></p>
                     <input type="text" step='1' value='1' name="number_of_nights" id='number_of_nights' class='input' placeholder='Enter the number of nights you will stay' required>
                  </div>
                  <div class="box">
                     <p>adults <span>*</span></p>
                     <select name="adults" class="input" required>
                        <option value="1" selected>1 adult</option>
                        <option value="2">2 adults</option>
                        <option value="3">3 adults</option>
                        <option value="4">4 adults</option>
                        <option value="5">5 adults</option>
                        <option value="6">6 adults</option>
                     </select>
                  </div>
                  <div class="box">
                     <p>childs <span>*</span></p>
                     <select name="childs" class="input" required>
                        <option value="0" selected>0 child</option>
                        <option value="1">1 child</option>
                        <option value="2">2 childs</option>
                        <option value="3">3 childs</option>
                        <option value="4">4 childs</option>
                        <option value="5">5 childs</option>
                        <option value="6">6 childs</option>
                     </select>
                  </div>

               </div>

               <div style="display: flex; align-items: center; justify-content: space-between;">
    <?php if (isset($_SESSION['user_id'])): ?>
        <div style='display: flex; flex-direction:column; gap: 10px;'>
            <p style='color: #00a86b; font-size: 18px;'>Payment (Screenshot) <span>*</span></p>
            <input type="file" accept=".png, .jpg, .jpeg" class='input' name="receipt" required>

            
            <p style='color: #00a86b; font-size: 15px; font-weight: 600;'>Total Payment: ₱ <span id='payment_amount'>0.00</span></p>
        </div>
    <?php endif; ?>
    <input type="submit" value="book now" name="book" class="btn">
</div>
</form>



         </section>

         <!-- reservation section ends -->

         <!-- gallery section starts  -->


         <!-- gallery section ends -->

         <!-- contact section starts  -->

         <section class="faqs" id="faqs">

            <div class="row">

               <form action="" method="post">
                  <h3>send us message</h3>
                  <input type="text" name="name" required maxlength="50" placeholder="enter your name" class="box">
                  <input type="email" name="email" required maxlength="50" placeholder="enter your email" class="box">
                  <input type="number" name="number" required maxlength="10" min="0" max="9999999999" placeholder="enter your number" class="box">
                  <textarea name="message" class="box" required maxlength="1000" placeholder="enter your message" cols="30" rows="10"></textarea>
                  <input type="submit" value="send message" name="send" class="btn">
               </form>

               <div class="faq">
                  <h3 class="title">frequently asked questions</h3>
                  <div class="box active">
                     <h3>How to cancel?</h3>
                     <p>To cancel a booking, navigate to your account or reservation details, locate the cancellation option, and follow the prompts to confirm.</p>
                  </div>
                  <div class="box">
                     <h3>Is there any vacancy?</h3>
                     <p>To check for room availability and dates, please proceed to the 'Check Availability' section on our website. There, you can explore the available rooms and select the dates that best suit your stay. We look forward to assisting you with your reservation!"</p>
                  </div>
                  <div class="box">
                     <h3>What are payment methods?</h3>
                     <p> Payments at Omallio Homestay can be conveniently made online through GCash and Maya. For your ease and security, we accept these methods as down payments. Experience hassle-free transactions while booking your stay with us!</p>
                  </div>
               </div>
            </div>
         </section>

         <!-- contact section ends -->

         <?php include 'components/footer.php'; ?>

         <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

         <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

         <!-- custom js file link  -->
         <script src="js/script.js"></script>

         <?php include 'components/message.php'; ?>

         <script>
            const paymentAmount = document.getElementById('payment_amount')
            const roomTypes = document.getElementById('types_of_room')
            const numberOfNights = document.getElementById('number_of_nights')

            function calculatePayment() {
               let amountPerNight = 0;
               const nights = parseInt(numberOfNights.value) || 1;

               switch (roomTypes.value) {
                  case 'familiy_room':
                     amountPerNight = 2000;
                     break;
                  case 'couple_room':
                     amountPerNight = 1500;
                     break;
                  case 'barkada_room':
                     amountPerNight = 3000;
                     break;
                  default:
                     amountPerNight = 0;
               }

               const totalPayment = amountPerNight * nights;

               paymentAmount.textContent = `${totalPayment.toLocaleString()}`;
            }

            roomTypes.addEventListener('change', calculatePayment);
            numberOfNights.addEventListener('input', calculatePayment);
         </script>
</body>

</html>