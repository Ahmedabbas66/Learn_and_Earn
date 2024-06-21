<?php

include '../components/connect.php';

if (isset($_COOKIE['tutor_id'])) {
   $tutor_id = $_COOKIE['tutor_id'];
} else {
   $tutor_id = '';
   header('location:login.php');
}

if (isset($_POST['delete'])) {
   $delete_id = $_POST['subject_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_subject = $conn->prepare("SELECT * FROM `subject` WHERE id = ? AND tutor_id = ? LIMIT 1");
   $verify_subject->execute([$delete_id, $tutor_id]);

   if ($verify_subject->rowCount() > 0) {



      $delete_subject_thumb = $conn->prepare("SELECT * FROM `subject` WHERE id = ? LIMIT 1");
      $delete_subject_thumb->execute([$delete_id]);
      $fetch_thumb = $delete_subject_thumb->fetch(PDO::FETCH_ASSOC);
      $delete_subject = $conn->prepare("DELETE FROM `subject` WHERE id = ?");
      $delete_subject->execute([$delete_id]);
      $message[] = 'subject deleted!';
   } else {
      $message[] = 'subject already deleted!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Subject</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

   <?php include '../components/admin_header.php'; ?>

   <section class="playlists">

      <h1 class="heading">added subject</h1>

      <div class="box-container">

         <div class="box" style="text-align: center;">
               <h3 class="title" style="margin-bottom: .5rem;">Create New subject</h3>
               <a href="add_subject.php" class="btn">Add subject</a>
            </div>

            <?php
         $select_subject = $conn->prepare("SELECT * FROM `subject` WHERE tutor_id = ? ORDER BY date DESC");
         $select_subject->execute([$tutor_id]);
         if ($select_subject->rowCount() > 0) {
            while ($fetch_subject = $select_subject->fetch(PDO::FETCH_ASSOC)) {
               $subject_id = $fetch_subject['id'];
               $count_videos = $conn->prepare("SELECT * FROM `exams` WHERE subject_id = ?");
               $count_videos->execute([$subject_id]);
               $total_videos = $count_videos->rowCount();
         ?>
            <div class="box">
               <div class="flex">
                  <div><i class="fas fa-circle-dot" style="<?php if ($fetch_subject['status'] == 'active') {
                                 echo 'color:limegreen';
                              } else {
                                 echo 'color:red';
                              } ?>"></i><span
                           style="<?php if ($fetch_subject['status'] == 'active') {
                                 echo 'color:limegreen';
                              } else {
                                 echo 'color:red';
                              } ?>"><?= $fetch_subject['status']; ?></span>
                  </div>
                  <div><i class="fas fa-calendar"></i><span><?= $fetch_subject['date']; ?></span></div>
               </div>
               <div class="thumb">
                  <span><?= $total_videos; ?> Exam</span>
                  <img src="../uploaded_files/<?= $fetch_subject['thumb']; ?>" alt="">
               </div>
               <h3 class="title"><?= $fetch_subject['title']; ?></h3>
               <p class="description"><?= $fetch_subject['description']; ?></p>
               <form action="" method="post" class="flex-btn">
                  <input type="hidden" name="subject_id" value="<?= $subject_id; ?>">
                  <a href="update_subject.php?get_id=<?= $subject_id; ?>" class="option-btn">update</a>
                  <input type="submit" value="delete" class="delete-btn"
                        onclick="return confirm('delete this subject?');" name="delete">
               </form>
               <a href="view_subject.php?get_id=<?= $subject_id; ?>" class="btn">view subject</a>
            </div>
            <?php
            }
         } else {
            echo '<p class="empty">no subject added yet!</p>';
         }
         ?>

         </div>

      </section>













   <?php include '../components/footer.php'; ?>

   <script src="../js/admin_script.js"></script>

   <script>
   document.querySelectorAll('.subjects .box-container .box .description').forEach(content => {
      if (content.innerHTML.length > 100) content.innerHTML = content.innerHTML.slice(0, 100);
   });
   </script>

</body>

</html>