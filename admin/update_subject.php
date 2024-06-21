<?php

include '../components/connect.php';

if (isset($_COOKIE['tutor_id'])) {
   $tutor_id = $_COOKIE['tutor_id'];
} else {
   $tutor_id = '';
   header('location:login.php');
}

if (isset($_GET['get_id'])) {
   $get_id = $_GET['get_id'];
} else {
   $get_id = '';
   header('location:subject.php');
}

if (isset($_POST['submit'])) {

   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);
   $description = $_POST['description'];
   $description = filter_var($description, FILTER_SANITIZE_STRING);
   $status = $_POST['status'];
   $status = filter_var($status, FILTER_SANITIZE_STRING);

   $update_subject = $conn->prepare("UPDATE `subject` SET title = ?, description = ?, status = ? WHERE id = ?");
   $update_subject->execute([$title, $description, $status, $get_id]);

   $old_image = $_POST['old_image'];
   $old_image = filter_var($old_image, FILTER_SANITIZE_STRING);
   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $ext = pathinfo($image, PATHINFO_EXTENSION);
   $rename = unique_id() . '.' . $ext;
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_files/' . $rename;

   if (!empty($image)) {
      if ($image_size > 2000000) {
         $message[] = 'image size is too large!';
      } else {
         $update_image = $conn->prepare("UPDATE `subject` SET thumb = ? WHERE id = ?");
         $update_image->execute([$rename, $get_id]);
         move_uploaded_file($image_tmp_name, $image_folder);
         if ($old_image != '' and $old_image != $rename) {
            unlink('../uploaded_files/' . $old_image);
         }
      }
   }

   $message[] = 'subject updated!';
}

if (isset($_POST['delete'])) {
   $delete_id = $_POST['subject_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);
   $delete_subject_thumb = $conn->prepare("SELECT * FROM `subject` WHERE id = ? LIMIT 1");
   $delete_subject_thumb->execute([$delete_id]);
   $fetch_thumb = $delete_subject_thumb->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_files/' . $fetch_thumb['thumb']);
   $delete_bookmark = $conn->prepare("DELETE FROM `bookmark` WHERE subject_id = ?");
   $delete_bookmark->execute([$delete_id]);
   $delete_subject = $conn->prepare("DELETE FROM `subject` WHERE id = ?");
   $delete_subject->execute([$delete_id]);
   header('location:subjects.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Subject</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

   <?php include '../components/admin_header.php'; ?>

   <section class="playlist-form">

      <h1 class="heading">update subject</h1>

      <?php
      $select_subject = $conn->prepare("SELECT * FROM `subject` WHERE id = ?");
      $select_subject->execute([$get_id]);
      if ($select_subject->rowCount() > 0) {
         while ($fetch_subject = $select_subject->fetch(PDO::FETCH_ASSOC)) {
            $subject_id = $fetch_subject['id'];
            $count_videos = $conn->prepare("SELECT * FROM `exams` WHERE subject_id = ?");
            $count_videos->execute([$subject_id]);
            $total_videos = $count_videos->rowCount();
      ?>
            <form action="" method="post" enctype="multipart/form-data">
               <input type="hidden" name="old_image" value="<?= $fetch_subject['thumb']; ?>">
               <p>subject status <span>*</span></p>
               <select name="status" class="box" required>
                  <option value="<?= $fetch_subject['status']; ?>" selected><?= $fetch_subject['status']; ?></option>
                  <option value="active">active</option>
                  <option value="deactive">deactive</option>
               </select>
               <p>subject title <span>*</span></p>
               <input type="text" name="title" maxlength="100" required placeholder="enter subject title" value="<?= $fetch_subject['title']; ?>" class="box">
               <p>subject description <span>*</span></p>
               <textarea name="description" class="box" required placeholder="write description" maxlength="1000" cols="30" rows="10"><?= $fetch_subject['description']; ?></textarea>
               <p>subject thumbnail <span>*</span></p>
               <div class="thumb">
                  <span><?= $total_videos; ?></span>
                  <img src="../uploaded_files/<?= $fetch_subject['thumb']; ?>" alt="">
               </div>
               <input type="file" name="image" accept="image/*" class="box">
               <input type="submit" value="update subject" name="submit" class="btn">
               <div class="flex-btn">
                  <input type="submit" value="delete" class="delete-btn" onclick="return confirm('delete this subject?');" name="delete">
                  <a href="view_subject.php?get_id=<?= $subject_id; ?>" class="option-btn">view subject</a>
               </div>
            </form>
      <?php
         }
      } else {
         echo '<p class="empty">no subject added yet!</p>';
      }
      ?>

   </section>















   <?php include '../components/footer.php'; ?>

   <script src="../js/admin_script.js"></script>

</body>

</html>