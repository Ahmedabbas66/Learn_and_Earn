<?php

include '../components/connect.php';

if (isset($_COOKIE['tutor_id'])) {
    $tutor_id = $_COOKIE['tutor_id'];
} else {
    $tutor_id = '';
    header('location:login.php');
}

// if (isset($_GET['get_id'])) {
//     $get_id = $_GET['get_id'];
// } else {
//     $get_id = '';
//     header('location:subject.php');
// }

if (isset($_POST['delete_subject'])) {
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
    header('locatin:subjects.php');
}

if (isset($_POST['delete_video'])) {
    $delete_id = $_POST['video_id'];
    $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);
    $verify_video = $conn->prepare("SELECT * FROM `exams` WHERE id = ? LIMIT 1");
    $verify_video->execute([$delete_id]);
    if ($verify_video->rowCount() > 0) {
        $delete_video_thumb = $conn->prepare("SELECT * FROM `exams` WHERE id = ? LIMIT 1");
        $delete_video_thumb->execute([$delete_id]);
        $fetch_thumb = $delete_video_thumb->fetch(PDO::FETCH_ASSOC);
        $delete_video = $conn->prepare("SELECT * FROM `exams` WHERE id = ? LIMIT 1");
        $delete_video->execute([$delete_id]);
        $fetch_video = $delete_video->fetch(PDO::FETCH_ASSOC);
        $delete_exams = $conn->prepare("DELETE FROM `exams` WHERE id = ?");
        $delete_exams->execute([$delete_id]);
        $message[] = 'exam deleted!';
    } else {
        $message[] = 'exam already deleted!';
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exams</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

    <?php include '../components/admin_header.php'; ?>

    <section class="contents">

        <h1 class="heading">subject exam students</h1>

        <div class="box-container">

            <?php
            $select_videos = $conn->prepare("SELECT * FROM `exams` WHERE tutor_id = ? AND subject_id = ?");
            // $select_videos->execute([$tutor_id, $subject_id]);
            if ($select_videos->rowCount() > 0) {
                while ($fecth_videos = $select_videos->fetch(PDO::FETCH_ASSOC)) {
                    $video_id = $fecth_videos['id'];
            ?>
                    <div class="box">
                        <div class="flex">
                            <div><i class="fas fa-dot-circle" style="<?php if ($fecth_videos['status'] == 'active') {
                                                                            echo 'color:limegreen';
                                                                        } else {
                                                                            echo 'color:red';
                                                                        } ?>"></i><span style="<?php if ($fecth_videos['status'] == 'active') {
                                                                                            echo 'color:limegreen';
                                                                                        } else {
                                                                                            echo 'color:red';
                                                                                        } ?>"><?= $fecth_videos['status']; ?></span></div>
                            <div><i class="fas fa-calendar"></i><span><?= $fecth_videos['date']; ?></span></div>
                        </div>
                        <img src="../uploaded_files/<?= $fecth_videos['thumb']; ?>" class="thumb" alt="">
                        <h3 class="title"><?= $fecth_videos['title']; ?></h3>
                        <form action="" method="post" class="flex-btn">
                            <input type="hidden" name="video_id" value="<?= $video_id; ?>">
                            <a href="display_exam.php?exam_id=<?= $video_id; ?>" class="option-btn">update</a>
                            <input type="submit" value="delete" class="delete-btn" onclick="return confirm('delete this exam?');" name="delete_video">
                        </form>
                        <a href="display_exam.php?exam_id=<?= $video_id; ?>" class="btn">view exam</a>
                    </div>
            <?php
                }
            } else {
                echo '<p class="empty">no students added yet !</p>';
            }
            ?>

        </div>

    </section>















    <?php include '../components/footer.php'; ?>

    <script src="../js/admin_script.js"></script>

</body>

</html>