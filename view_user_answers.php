<?php
// Include the database connection file
include 'components/connect.php';

// Get the exam ID and user ID from the URL parameters
$exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';

if (empty($exam_id) || empty($user_id)) {
    die("Invalid exam ID or user ID!");
}

// Debugging: Print the user ID and exam ID to ensure they are correct
echo "Debug: User ID = $user_id, Exam ID = $exam_id<br>";

// Fetch exam details from the database
$select_exam = $conn->prepare("SELECT * FROM exams WHERE id = ?");
$select_exam->execute([$exam_id]);
$exam = $select_exam->fetch(PDO::FETCH_ASSOC);

if (!$exam) {
    die("Exam not found!");
}

// Fetch user details
$select_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
$select_user->execute([$user_id]);
$user = $select_user->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // Debugging: Print a message if the user is not found
    die("User not found! Debug: Could not find user with ID $user_id");
}

// Fetch user's attempt details
$select_attempts = $conn->prepare("SELECT * FROM exam_attempts WHERE user_id = ? AND exam_id = ?");
$select_attempts->execute([$user_id, $exam_id]);
$attempts = $select_attempts->fetchAll(PDO::FETCH_ASSOC);

if (empty($attempts)) {
    die("No attempts found for this user on this exam!");
}

// Fetch questions and options from the database
$select_questions = $conn->prepare("SELECT * FROM questions WHERE exam_id = ?");
$select_questions->execute([$exam_id]);
$questions = $select_questions->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View User Answers</title>
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
    <h1>User Answers for Exam: <?= htmlspecialchars($exam['title']) ?></h1>

    <h2>Exam Details</h2>
    <table>
        <tr>
            <th>ID</th>
            <td><?= htmlspecialchars($exam['id']) ?></td>
        </tr>
        <tr>
            <th>Student ID</th>
            <td><?= htmlspecialchars($user['id']) ?></td>
        </tr>
        <tr>
            <th>Student Name</th>
            <td><?= htmlspecialchars($user['name']) ?></td>
        </tr>
        <tr>
            <th>Title</th>
            <td><?= htmlspecialchars($exam['title']) ?></td>
        </tr>
        <tr>
            <th>Description</th>
            <td><?= htmlspecialchars($exam['description']) ?></td>
        </tr>
        <tr>
            <th>Subject</th>
            <td><?= htmlspecialchars($exam['subject']) ?></td>
        </tr>
        <tr>
            <th>Time</th>
            <td><?= htmlspecialchars($exam['time']) ?></td>
        </tr>
        <tr>
            <th>Date</th>
            <td><?= htmlspecialchars($exam['date']) ?></td>
        </tr>
        <tr>
            <th>Duration</th>
            <td><?= htmlspecialchars($exam['duration']) ?></td>
        </tr>
        <tr>
            <th>Degree</th>
            <td><?= htmlspecialchars($exam['degree']) ?></td>
        </tr>
        <tr>
            <th>Created At</th>
            <td><?= htmlspecialchars($exam['created_at']) ?></td>
        </tr>
    </table>

    <h2>User Answers</h2>
    <?php foreach ($attempts as $attempt): ?>
    <h3>Attempt on <?= $attempt['attempt_date'] ?></h3>

    <?php foreach ($questions as $index => $question): ?>
    <h4>Question <?= $index + 1 ?>: <?= htmlspecialchars($question['text']) ?></h4>

    <?php
            // Fetch the user's answer for this question
            $select_answer = $conn->prepare("SELECT ua.*, o.text as option_text FROM user_answers ua JOIN options o ON ua.selected_option_id = o.id WHERE ua.attempt_id = ? AND ua.question_id = ?");
            $select_answer->execute([$attempt['id'], $question['id']]);
            $user_answer = $select_answer->fetch(PDO::FETCH_ASSOC);

            // Fetch the correct answer for this question
            $select_correct_answer = $conn->prepare("SELECT o.text as correct_option_text FROM options o WHERE o.question_id = ? AND o.is_correct = 1");
            $select_correct_answer->execute([$question['id']]);
            $correct_answer = $select_correct_answer->fetch(PDO::FETCH_ASSOC);
            ?>

    <?php if ($user_answer): ?>
    <p>Selected Answer: <?= htmlspecialchars($user_answer['option_text']) ?></p>
    <?php else: ?>
    <p>No answer selected</p>
    <?php endif; ?>

    <?php if ($correct_answer): ?>
    <p>Correct Answer: <?= htmlspecialchars($correct_answer['correct_option_text']) ?></p>
    <?php endif; ?>

    <p>Options:</p>
    <ul>
        <?php
                $select_options = $conn->prepare("SELECT * FROM options WHERE question_id = ?");
                $select_options->execute([$question['id']]);
                $options = $select_options->fetchAll(PDO::FETCH_ASSOC);
                $letters = ['A', 'B', 'C', 'D'];
                $counter = 0;
                foreach ($options as $option):
                    $letter = $letters[$counter++];
                ?>
        <li><?= $letter ?>: <?= htmlspecialchars($option['text']) ?></li>
        <?php endforeach; ?>
    </ul>
    <?php endforeach; ?>
    <?php endforeach; ?>
</body>
</html>
