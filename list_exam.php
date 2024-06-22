<?php
// Example page to list all exams with a link to view students who took each exam

// Include the database connection file
include 'components/connect.php';

// Fetch all exams from the database
$select_exams = $conn->prepare("SELECT * FROM exams");
$select_exams->execute();
$exams = $select_exams->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exams List</title>
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
    <h1>Exams List</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($exams as $exam): ?>
            <tr>
                <td><?= htmlspecialchars($exam['id']) ?></td>
                <td><?= htmlspecialchars($exam['title']) ?></td>
                <td>
                    <a href="list_students.php?exam_id=<?= $exam['id'] ?>">View Students</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
