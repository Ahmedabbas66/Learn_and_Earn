<?php
// Include the database connection file

// include '../components/connect.php';

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

// Fetch questions and options from the database
$select_questions = $conn->prepare("SELECT * FROM questions WHERE exam_id = ?");
$select_questions->execute([$exam_id]);
$questions = $select_questions->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Exam</title>

<!-- font awesome cdn link  -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

<!-- custom css file link  -->
<link rel="stylesheet" href="../css/admin_style.css">

<style>
    .options {
        margin-top: 10px;
    }

    .option {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }

    .option input[type="text"] {
        margin-left: 10px;
        flex: 1;
    }

    /* Style the radio buttons */
    input[type="radio"] {
        width: 15px; /* Set the width */
        height: 15px; /* Set the height */
        margin-right: 10px; /* Space between radio button and label text */
        appearance: none; /* Remove default styling */
        border: 1px solid #007bff; /* Add border */
        border-radius: 50%; /* Make it round */
        outline: none; /* Remove outline */
        cursor: pointer; /* Change cursor to pointer */
        position: relative; /* For positioning the inner circle */
    }

    input[type="radio"]:checked::before {
        content: '';
        width: 10px; /* Inner circle width */
        height: 10px; /* Inner circle height */
        background-color: #007bff; /* Inner circle color */
        border-radius: 50%; /* Make the inner circle round */
        position: absolute; /* Position it absolutely inside the radio button */
        top: 50%; /* Center it vertically */
        left: 50%; /* Center it horizontally */
        transform: translate(-50%, -50%); /* Center it using transform */
    }
</style>

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="video-form">

    <h1 class="heading"><?= htmlspecialchars($exam['title']) ?></h1>

    <form action="" method="post" enctype="multipart/form-data">

    <p>Description: </p>
    <textarea name="description" class="box" required maxlength="1000" cols="30" rows="10"><?= htmlspecialchars($exam['description']) ?></textarea>

    <p>Subject: </p>
    <input value="<?= htmlspecialchars($exam['subject']) ?>" type="text" name="text" readonly class="box">

    <p>Time and Date: </p>
    <input value="<?= htmlspecialchars($exam['time']) ?>" type="time" name="time" required class="box">
    <input value="<?= htmlspecialchars($exam['date']) ?>" type="date" name="date" required class="box">

    <p>Exam Duration Time in MINs: </p>
    <input value="<?= htmlspecialchars($exam['duration']) ?>" type="number" name="duration" required class="box">

    <p>Exam Degree: </p>
    <input value="<?= htmlspecialchars($exam['degree']) ?>" type="number" name="degree" required class="box">

    <div id="componentContainer">

    <?php foreach ($questions as $index => $question) : ?>
        <!-- <div class="question"> -->
            <p>Question <?= $index + 1 ?>: </p>
            <input value="<?= htmlspecialchars($question['text']) ?>" type="text" name="questions" required class="box">
            
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
                            <p>(Correct Answer)</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <!-- </div> -->
    <?php endforeach; ?>
    </div>
    </form>

</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
