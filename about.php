<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
   $user_id = $_COOKIE['user_id'];
} else {
   $user_id = '';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>about</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <!-- about section starts  -->

   <section class="about">

      <div class="row">

         <div class="image">
            <img src="images/about-img.svg" alt="">
         </div>

         <div class="content">
            <h3>why choose us?</h3>
            <p>Welcome to our Website, your ultimate destination for comprehensive exam preparation and course videos. Our mission is to empower students with the knowledge and skills they need to succeed academically and professionally. <br>

               At our Website, we believe that quality education should be accessible to everyone. That’s why we offer a vast library of high-quality, expertly crafted video content covering a wide range of subjects and exam preparation materials. Whether you’re studying for standardized tests, professional certifications, or academic exams, we have you covered.</p>
            <!-- <a href="courses.html" class="inline-btn">our courses</a> -->
         </div>

      </div>

      <div class="box-container">

         <div class="box">
            <i class="fas fa-graduation-cap"></i>
            <div>
               <h3>+1k</h3>
               <span>online courses</span>
            </div>
         </div>

         <div class="box">
            <i class="fas fa-user-graduate"></i>
            <div>
               <h3>+25k</h3>
               <span>brilliants students</span>
            </div>
         </div>

         <div class="box">
            <i class="fas fa-chalkboard-user"></i>
            <div>
               <h3>+5k</h3>
               <span>expert teachers</span>
            </div>
         </div>

         <div class="box">
            <i class="fas fa-briefcase"></i>
            <div>
               <h3>100%</h3>
               <span>job placement</span>
            </div>
         </div>

      </div>

   </section>

   <!-- about section ends -->

   <!-- reviews section starts  -->

   <section class="reviews">

      <h1 class="heading">Our Team</h1>

      <div class="box-container">

         <div class="box">
            <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Illo fugiat, quaerat voluptate odio consectetur assumenda fugit maxime unde at ex?</p>
            <div class="user">
               <img src="images/Dr_Sherif.png" alt="">
               <div>
                  <h3>Dr.Sherif</h3>
                  <div class="stars">
                     <i class="fas fa-star"></i>
                     <i class="fas fa-star"></i>
                     <i class="fas fa-star"></i>
                     <i class="fas fa-star"></i>
                     <i class="fas fa-star"></i>
                  </div>
               </div>
            </div>
         </div>

         <div class="box">
            <p>Hello! I'm Mohamed Ayman, a front-end developer with over two years of experience in creating engaging and user-friendly web experiences. I have a strong foundation in front-end technologies and design principles, driven by my passion for blending creativity with technical skills. I focus on building intuitive and visually appealing websites.</p>
            <div class="user">
               <img src="images/Moahmed_Ayman.jpg" alt="">
               <div>
                  <h3>Mohamed Ayman</h3>
                  <div class="stars">
                     <i class="fas fa-star"></i>
                     <i class="fas fa-star"></i>
                     <i class="fas fa-star"></i>
                     <i class="fas fa-star"></i>
                     <i class="fas fa-star"></i>
                  </div>
               </div>
            </div>
         </div>

         <div class="box">
            <p>Hello! I'm Ahmed Abbas, a dedicated backend developer with over three years of experience in building robust, scalable, and efficient server-side applications. My journey into backend development began with a fascination for problem-solving and a desire to understand how things work behind the scenes, leading me to pursue a degree in Computer Science from FCI .</p>
            <div class="user">
               <img src="images/ahmed_abbas.jpeg" alt="">
               <div>
                  <h3>Ahmed Abbas</h3>
                  <div class="stars">
                     <i class="fas fa-star"></i>
                     <i class="fas fa-star"></i>
                     <i class="fas fa-star"></i>
                     <i class="fas fa-star"></i>
                     <i class="fas fa-star"></i>
                  </div>
               </div>
            </div>
         </div>

         <div class="box">
            <p>Hello, my name is Abd Al-Rahman Aziz. I am a front-end developer specializing in creating responsive and engaging web applications using HTML, CSS, JavaScript, and frameworks like React. I am passionate about staying updated with the latest technologies and delivering high-quality digital experiences. I look forward to connecting and exploring new opportunities.</p>
            <div class="user">
               <img src="images/Abd_Al-Rahman_Aziz.jpeg" alt="">
               <div>
                  <h3>Abd Al-Rahman Aziz</h3>
                  <div class="stars">
                     <i class="fas fa-star"></i>
                     <i class="fas fa-star"></i>
                     <i class="fas fa-star"></i>
                     <i class="fas fa-star"></i>
                     <i class="fas fa-star"></i>
                  </div>
               </div>
            </div>
         </div>

         <div class="box">
            <p>Hello, I'm Tarek. I am a front-end developer with two years of experience in the field. I have a strong passion for web development and user interface design. I am currently pursuing a degree in Computer Science and Information Technology to further enhance my skills. I am dedicated to staying up-to-date with the latest trends and technologies in the industry. <br><br></p>
            <div class="user">
               <img src="images/Tarek_Aldoushi.jpeg" alt="">
               <div>
                  <h3>Tarek Aldoushi</h3>
                  <div class="stars">
                     <i class="fas fa-star"></i>
                     <i class="fas fa-star"></i>
                     <i class="fas fa-star"></i>
                     <i class="fas fa-star"></i>
                     <i class="fas fa-star"></i>
                  </div>
               </div>
            </div>
         </div>

         <div class="box">
            <p>Welcome I’m Seham nassar, machine learning programmer with a passion for developing intelligent systems. Alongside my technical expertise, I bring strong communication and soft skills to every project. I look forward to connecting with you! <br><br><br><br></p>
            <div class="user">
               <img src="images/Seham_nassar.jpeg" alt="">
               <div>
                  <h3>Seham Nassar</h3>
                  <div class="stars">
                     <i class="fas fa-star"></i>
                     <i class="fas fa-star"></i>
                     <i class="fas fa-star"></i>
                     <i class="fas fa-star"></i>
                     <i class="fas fa-star"></i>
                  </div>
               </div>
            </div>
         </div>

      </div>

   </section>

   <!-- reviews section ends -->










   <?php include 'components/footer.php'; ?>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>

</body>

</html>