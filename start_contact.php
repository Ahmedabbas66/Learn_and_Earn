<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
   $user_id = $_COOKIE['user_id'];
} else {
   $user_id = '';
}

if (isset($_POST['submit'])) {

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $msg = $_POST['msg'];
   $msg = filter_var($msg, FILTER_SANITIZE_STRING);

   $select_contact = $conn->prepare("SELECT * FROM `contact` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $select_contact->execute([$name, $email, $number, $msg]);

   if ($select_contact->rowCount() > 0) {
      $message[] = 'message sent already!';
   } else {
      $insert_message = $conn->prepare("INSERT INTO `contact`(name, email, number, message) VALUES(?,?,?,?)");
      $insert_message->execute([$name, $email, $number, $msg]);
      $message[] = 'message sent successfully!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>contact</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php include 'components/start_header.php'; ?>

   <!-- contact section starts  -->

   <section class="contact">

      <div class="row">

         <div class="image">
            <img src="images/contact-img.svg" alt="">
         </div>

         <form action="" method="post">
            <h3>get in touch</h3>
            <input type="text" placeholder="enter your name" required maxlength="100" name="name" class="box">
            <input type="email" placeholder="enter your email" required maxlength="100" name="email" class="box">
            <input type="number" min="0" max="99999999999" placeholder="enter your number" required maxlength="10" name="number" class="box">
            <textarea name="msg" class="box" placeholder="enter your message" required cols="30" rows="10" maxlength="1000"></textarea>
            <input type="submit" value="send message" class="inline-btn" name="submit">
         </form>

      </div>

      <div class="box-container">

         <div class="box">
            <i class="fas fa-phone"></i>
            <h3>Phone Number</h3>
            <a href="tel:+201288365384">+20 128 836 5384</a>
            <a href="tel:+201554576054">+20 155 457 6054</a>
         </div>

         <div class="box">
            <i class="fas fa-envelope"></i>
            <h3>Email Address</h3>
            <a href="mailto:mo.ay.ammar@gmail.com">mo.ay.ammar@gmail.com</a>
            <a href="mailto:anasbhai@gmail.com">ahamedadelapas@gmail.com</a>
         </div>

         <div class="box">
            <i class="fas fa-map-marker-alt"></i>
            <h3>Address</h3>
            <a href="https://www.google.com/maps/dir//Qism+Shebeen+El-Kom,+Shibin+el+Kom,+Menofia+Governorate+6131614/@30.5756599,30.9673189,13z/data=!4m8!4m7!1m0!1m5!1m1!1s0x14f7d6bf14e416e9:0xc49ca19e02abe2d2!2m2!1d31.0085187!2d30.5756664?entry=ttu">Faculty of Computers and Information Menoufia University</a>
         </div>


      </div>

   </section>

   <!-- contact section ends -->











   <?php include 'components/footer.php'; ?>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>

</body>

</html>