<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
}

if (isset($_GET['get_id'])) {
    $get_id = $_GET['get_id'];
} else {
    $get_id = '';
    header('location:home.php');
}

if (isset($_POST['save_list'])) {

    if ($user_id != '') {

        $list_id = $_POST['list_id'];
        $list_id = filter_var($list_id, FILTER_SANITIZE_STRING);

        $select_list = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ? AND subject_id = ?");
        $select_list->execute([$user_id, $list_id]);

        if ($select_list->rowCount() > 0) {
            $remove_bookmark = $conn->prepare("DELETE FROM `bookmark` WHERE user_id = ? AND subject_id = ?");
            $remove_bookmark->execute([$user_id, $list_id]);
            $message[] = 'subject removed!';
        } else {
            $insert_bookmark = $conn->prepare("INSERT INTO `bookmark`(user_id, subject_id) VALUES(?,?)");
            $insert_bookmark->execute([$user_id, $list_id]);
            $message[] = 'subject saved!';
        }
    } else {
        $message[] = 'please login first!';
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
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <?php include 'components/user_header.php'; ?>

    <!-- subject section starts  -->

    <section class="playlist">

        <h1 class="heading">subject details</h1>

        <div class="row">

            <?php
            $select_subject = $conn->prepare("SELECT * FROM `subject` WHERE id = ? and status = ? LIMIT 1");
            $select_subject->execute([$get_id, 'active']);
            if ($select_subject->rowCount() > 0) {
                $fetch_subject = $select_subject->fetch(PDO::FETCH_ASSOC);

                $subject_id = $fetch_subject['id'];

                $count_videos = $conn->prepare("SELECT * FROM `exams` WHERE subject_id = ?");
                $count_videos->execute([$subject_id]);
                $total_videos = $count_videos->rowCount();

                $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ? LIMIT 1");
                $select_tutor->execute([$fetch_subject['tutor_id']]);
                $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);


            ?>

                <div class="col">
                    <form action="" method="post" class="save-list">
                        <input type="hidden" name="list_id" value="<?= $subject_id; ?>">
                    </form>
                    <div class="thumb">
                        <span><?= $total_videos; ?> exam</span>
                        <img src="uploaded_files/<?= $fetch_subject['thumb']; ?>" alt="">
                    </div>
                </div>

                <div class="col">
                    <div class="tutor">
                        <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
                        <div>
                            <h3><?= $fetch_tutor['name']; ?></h3>
                            <span><?= $fetch_tutor['profession']; ?></span>
                        </div>
                    </div>
                    <div class="details">
                        <h3><?= $fetch_subject['title']; ?></h3>
                        <p><?= $fetch_subject['description']; ?></p>
                        <div class="date"><i class="fas fa-calendar"></i><span><?= $fetch_subject['date']; ?></span></div>
                    </div>
                </div>

            <?php
            } else {
                echo '<p class="empty">this subject was not found!</p>';
            }
            ?>

        </div>

    </section>

    <!-- subject section ends -->

    <!-- videos container section starts  -->

    <section class="videos-container">

        <h1 class="heading">subject exams</h1>

        <div class="box-container">

            <?php
            $select_exams = $conn->prepare("SELECT * FROM `exams` WHERE subject_id = ? AND status = ? ORDER BY date DESC");
            $select_exams->execute([$get_id, 'active']);
            if ($select_exams->rowCount() > 0) {
                while ($fetch_exams = $select_exams->fetch(PDO::FETCH_ASSOC)) {
            ?>
                    <a href="display_exam.php?exam_id=<?= $fetch_exams['id']; ?>" class="box">
                        <i class="fa-solid fa-eye"></i>
                        <img src="uploaded_files/<?= $fetch_exams['thumb']; ?>" alt="">
                        <h3><?= $fetch_exams['title']; ?></h3>
                    </a>
            <?php
                }
            } else {
                echo '<p class="empty">no exams added yet!</p>';
            }
            ?>

        </div>

    </section>

    <!-- videos container section ends -->











    <?php include 'components/footer.php'; ?>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>