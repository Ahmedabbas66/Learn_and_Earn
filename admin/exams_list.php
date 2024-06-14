<?php
// Example page: exams_list.php

// Include the database connection file
include '../components/connect.php';

// Fetch all exams from the database
$select_exams = $conn->prepare("SELECT * FROM exams");
$select_exams->execute();
$exams = $select_exams->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Exam List</title>
<link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="exam-list">
    <h1 class="heading">Exam List</h1>

    <ul>
        <?php foreach ($exams as $exam) : ?>
            <li>
                <a href="display_exam.php?exam_id=<?= $exam['id'] ?>"><?= htmlspecialchars($exam['title']) ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>

<?php include '../components/footer.php'; ?>

</body>
</html>
