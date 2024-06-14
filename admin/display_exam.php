<?php
// Include the database connection file
include '../components/connect.php';

// Get the exam ID from the URL parameter
$exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;

// Fetch exam details from the database
$select_exam = $conn->prepare("SELECT * FROM exams WHERE id = ?");
$select_exam->execute([$exam_id]);
$exam = $select_exam->fetch(PDO::FETCH_ASSOC);

if (!$exam) {
    die("Exam not found!");
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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Exam</title>
<link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="exam-view">
    <h1 class="heading"><?= htmlspecialchars($exam['title']) ?></h1>
    <p><?= htmlspecialchars($exam['description']) ?></p>
    <p>Subject: <?= htmlspecialchars($exam['subject']) ?></p>
    <p>Date: <?= htmlspecialchars($exam['date']) ?> Time: <?= htmlspecialchars($exam['time']) ?></p>
    <p>Duration: <?= htmlspecialchars($exam['duration']) ?> minutes</p>
    <p>Degree: <?= htmlspecialchars($exam['degree']) ?></p>

    <?php foreach ($questions as $index => $question) : ?>
        <div class="question">
            <p>Question <?= $index + 1 ?>: <?= htmlspecialchars($question['text']) ?></p>
            <div class="options">
                <?php
                $select_options = $conn->prepare("SELECT * FROM options WHERE question_id = ?");
                $select_options->execute([$question['id']]);
                $options = $select_options->fetchAll(PDO::FETCH_ASSOC);
                foreach ($options as $option) :
                ?>
                    <div class="option">
                        <p><?= htmlspecialchars($option['text']) ?></p>
                        <?php if ($option['is_correct']) : ?>
                            <span class="correct-answer">(Correct Answer)</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

</section>

<?php include '../components/footer.php'; ?>

</body>
</html>
