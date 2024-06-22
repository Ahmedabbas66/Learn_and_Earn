<?php
// Include the database connection file
include 'components/connect.php';

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
    SELECT u.id as user_id, u.name
    FROM users u
    JOIN exam_attempts ea ON u.id = ea.user_id
    WHERE ea.exam_id = ?
");
$select_students->execute([$exam_id]);
$students = $select_students->fetchAll(PDO::FETCH_ASSOC);

if (empty($students)) {
    die("No students found for this exam!");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List of Students</title>
    <style>
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
    }
    </style>
</head>
<body>
    <h1>List of Students for Exam: <?= htmlspecialchars($exam['title']) ?></h1>

    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
            <tr>
                <td><?= htmlspecialchars($student['user_id']) ?></td>
                <td><?= htmlspecialchars($student['name']) ?></td>
                <td>
                    <a href="view_user_answers.php?user_id=<?= $student['user_id'] ?>&exam_id=<?= $exam_id ?>">View Answers</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
