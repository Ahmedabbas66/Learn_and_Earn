<?php

include '../components/connect.php';

if (isset($_COOKIE['tutor_id'])) {
    $tutor_id = $_COOKIE['tutor_id'];
} else {
    $tutor_id = '';
    header('location:login.php');
}

// Get the exam ID from the URL parameter
$exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;

// Fetch exam details from the database
$select_exam = $conn->prepare("SELECT * FROM exams WHERE id = ?");
$select_exam->execute([$exam_id]);
$exam = $select_exam->fetch(PDO::FETCH_ASSOC);

if (!$exam) {
    die("Exam not found!");
}

// Fetch students who took the exam
$select_students = $conn->prepare("
    SELECT u.id as user_id, u.name, u.image
    FROM users u
    JOIN exam_attempts ea ON u.id = ea.user_id
    WHERE ea.exam_id = ?
");
$select_students->execute([$exam_id]);
$students = $select_students->fetchAll(PDO::FETCH_ASSOC);

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
    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

    <?php include '../components/admin_header.php'; ?>

    <!-- videos container section starts  -->

    <section class="videos-container">

        <h1 class="heading">subject exams</h1>

        <div class="box-container">

            <?php if (empty($students)) : ?>
                <p class="empty">no students added yet!</p>
            <?php else : ?>
                <?php foreach ($students as $student) : ?>
                    <a href="view_answer.php?user_id=<?= htmlspecialchars($student['user_id']) ?>&exam_id=<?= $exam_id ?>" class="box"> <i class="fa-solid fa-eye"></i>
                        <img src="../uploaded_files/<?= $student['image']; ?>" alt="">
                        <h3><?= htmlspecialchars($student['name']) ?></h3>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </section>

    <!-- videos container section ends -->











    <?php include '../components/footer.php'; ?>

    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

</body>

</html>