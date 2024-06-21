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

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    // Update exam fields
    if ($_POST['title'] !== $_POST['original_title']) {
        $stmt = $conn->prepare("UPDATE exams SET title = ? WHERE id = ?");
        $stmt->execute([$_POST['title'], $exam['id']]);
    }
    if ($_POST['description'] !== $_POST['original_description']) {
        $stmt = $conn->prepare("UPDATE exams SET description = ? WHERE id = ?");
        $stmt->execute([$_POST['description'], $exam['id']]);
    }
    if ($_POST['time'] !== $_POST['original_time']) {
        $stmt = $conn->prepare("UPDATE exams SET time = ? WHERE id = ?");
        $stmt->execute([$_POST['time'], $exam['id']]);
    }
    if ($_POST['date'] !== $_POST['original_date']) {
        $stmt = $conn->prepare("UPDATE exams SET date = ? WHERE id = ?");
        $stmt->execute([$_POST['date'], $exam['id']]);
    }
    if ($_POST['duration'] !== $_POST['original_duration']) {
        $stmt = $conn->prepare("UPDATE exams SET duration = ? WHERE id = ?");
        $stmt->execute([$_POST['duration'], $exam['id']]);
    }
    if ($_POST['degree'] !== $_POST['original_degree']) {
        $stmt = $conn->prepare("UPDATE exams SET degree = ? WHERE id = ?");
        $stmt->execute([$_POST['degree'], $exam['id']]);
    }

    // Update questions
    foreach ($_POST['questions'] as $question_id => $question_text) {
        if ($question_text !== $_POST['original_questions'][$question_id]) {
            $stmt = $conn->prepare("UPDATE questions SET text = ? WHERE id = ?");
            $stmt->execute([$question_text, $question_id]);
        }
    }

    // Update options
    foreach ($_POST['options'] as $question_id => $option_group) {
        foreach ($option_group as $option_id => $option_text) {
            if ($option_text !== $_POST['original_options'][$question_id][$option_id]) {
                $stmt = $conn->prepare("UPDATE options SET text = ? WHERE id = ?");
                $stmt->execute([$option_text, $option_id]);
            }
        }
    }

    // Update correct option
    foreach ($_POST['correct_option'] as $question_id => $correct_option_id) {
        // Unset the previous correct option
        $stmt = $conn->prepare("UPDATE options SET is_correct = 0 WHERE question_id = ?");
        $stmt->execute([$question_id]);

        // Set the new correct option
        $stmt = $conn->prepare("UPDATE options SET is_correct = 1 WHERE id = ?");
        $stmt->execute([$correct_option_id]);
    }
}
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
            width: 15px;
            /* Set the width */
            height: 15px;
            /* Set the height */
            margin-right: 10px;
            /* Space between radio button and label text */
            appearance: none;
            /* Remove default styling */
            border: 1px solid #007bff;
            /* Add border */
            border-radius: 50%;
            /* Make it round */
            outline: none;
            /* Remove outline */
            cursor: pointer;
            /* Change cursor to pointer */
            position: relative;
            /* For positioning the inner circle */
        }

        input[type="radio"]:checked::before {
            content: '';
            width: 10px;
            /* Inner circle width */
            height: 10px;
            /* Inner circle height */
            background-color: #007bff;
            /* Inner circle color */
            border-radius: 50%;
            /* Make the inner circle round */
            position: absolute;
            /* Position it absolutely inside the radio button */
            top: 50%;
            /* Center it vertically */
            left: 50%;
            /* Center it horizontally */
            transform: translate(-50%, -50%);
            /* Center it using transform */
        }
    </style>

</head>

<body>

    <?php include '../components/admin_header.php'; ?>

    <section class="video-form">

        <h1 class="heading">view and update exam " <?= htmlspecialchars($exam['title']) ?> "</h1>

        <form action="" method="post" enctype="multipart/form-data">

            <p>Title: </p>
            <input value="<?= htmlspecialchars($exam['title']) ?>" type="text" name="title" class="box">
            <input type="hidden" name="original_title" value="<?= htmlspecialchars($exam['title']) ?>">

            <p>Description: </p>
            <textarea name="description" class="box" required maxlength="1000" cols="30" rows="10"><?= htmlspecialchars($exam['description']) ?></textarea>
            <input type="hidden" name="original_description" value="<?= htmlspecialchars($exam['description']) ?>">

            <p>Subject: </p>
            <input value="<?= htmlspecialchars($exam['subject_title']) ?>" type="text" name="subject" readonly class="box">
            <input type="hidden" name="original_subject" value="<?= htmlspecialchars($exam['subject_title']) ?>">

            <p>Time and Date: </p>
            <input value="<?= htmlspecialchars($exam['time']) ?>" type="time" name="time" required class="box">
            <input type="hidden" name="original_time" value="<?= htmlspecialchars($exam['time']) ?>">
            <input value="<?= htmlspecialchars($exam['date']) ?>" type="date" name="date" required class="box">
            <input type="hidden" name="original_date" value="<?= htmlspecialchars($exam['date']) ?>">

            <p>Exam Duration Time in MINs: </p>
            <input value="<?= htmlspecialchars($exam['duration']) ?>" type="number" name="duration" required class="box">
            <input type="hidden" name="original_duration" value="<?= htmlspecialchars($exam['duration']) ?>">

            <p>Exam Degree: </p>
            <input value="<?= htmlspecialchars($exam['degree']) ?>" type="number" name="degree" required class="box">
            <input type="hidden" name="original_degree" value="<?= htmlspecialchars($exam['degree']) ?>">

            <div id="componentContainer">
                <?php foreach ($questions as $index => $question) : ?>
                    <p>Question <?= $index + 1 ?>: </p>
                    <input value="<?= htmlspecialchars($question['text']) ?>" type="text" name="questions[<?= $question['id'] ?>]" required class="box">
                    <input type="hidden" name="original_questions[<?= $question['id'] ?>]" value="<?= htmlspecialchars($question['text']) ?>">

                    <div class="options">
                        <?php
                        $select_options = $conn->prepare("SELECT * FROM options WHERE question_id = ?");
                        $select_options->execute([$question['id']]);
                        $options = $select_options->fetchAll(PDO::FETCH_ASSOC);
                        $letters = ['A', 'B', 'C', 'D'];
                        $counter = 0;
                        foreach ($options as $option) :
                            $letter = $letters[$counter++];
                        ?>
                            <div class="option">
                                <p>
                                    <input type="radio" name="correct_option[<?= $question['id'] ?>]" value="<?= $option['id'] ?>" <?= $option['is_correct'] ? 'checked' : '' ?>> <?= $letter ?>
                                </p>
                                <input value="<?= htmlspecialchars($option['text']) ?>" type="text" name="options[<?= $question['id'] ?>][<?= $option['id'] ?>]" class="box">
                                <input type="hidden" name="original_options[<?= $question['id'] ?>][<?= $option['id'] ?>]" value="<?= htmlspecialchars($option['text']) ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <input type="submit" value="update exam" name="update" class="btn">
        </form>

    </section>

    <?php include '../components/footer.php'; ?>

    <script src="../js/admin_script.js"></script>

</body>

</html>