<?php
// Include the database connection file
include '../components/connect.php';

if (isset($_COOKIE['tutor_id'])) {
    $tutor_id = $_COOKIE['tutor_id'];
} else {
    $tutor_id = '';
    header('location:login.php');
}

// Get the exam ID and user ID from the URL parameters
$exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';

if (empty($exam_id) || empty($user_id)) {
    die("Invalid exam ID or user ID!");
}

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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student Answers</title>

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

        input[type="radio"] {
            width: 15px;
            height: 15px;
            margin-right: 10px;
            appearance: none;
            border: 1px solid #007bff;
            border-radius: 50%;
            outline: none;
            cursor: pointer;
            position: relative;
        }

        input[type="radio"]:checked::before {
            content: '';
            width: 10px;
            height: 10px;
            background-color: #007bff;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        #timer {
            font-size: 20px;
            font-weight: bold;
            color: red;
        }
    </style>

</head>

<body>

    <?php include '../components/admin_header.php'; ?>

    <section class="video-form">

        <h1 class="heading">student name: " <?= htmlspecialchars($user['name']) ?> ".</h1>

        <h1 class="heading">student degree: <br> " <br>
        <?php
        foreach ($attempts as $attempt) {
            $correct_answers_count = 0;
            $total_questions = count($questions);

            foreach ($questions as $question) {
                // Fetch the user's answer for this question
                $select_answer = $conn->prepare("SELECT ua.*, o.text as option_text FROM user_answers ua JOIN options o ON ua.selected_option_id = o.id WHERE ua.attempt_id = ? AND ua.question_id = ?");
                $select_answer->execute([$attempt['id'], $question['id']]);
                $user_answer = $select_answer->fetch(PDO::FETCH_ASSOC);

                // Fetch the correct answer for this question
                $select_correct_answer = $conn->prepare("SELECT o.text as correct_option_text FROM options o WHERE o.question_id = ? AND o.is_correct = 1");
                $select_correct_answer->execute([$question['id']]);
                $correct_answer = $select_correct_answer->fetch(PDO::FETCH_ASSOC);

                if ($user_answer && $correct_answer && $user_answer['option_text'] == $correct_answer['correct_option_text']) {
                    $correct_answers_count++;
                }
            }

            echo "Attempt on (" . htmlspecialchars($attempt['attempt_date']) . "): " . $correct_answers_count . " / " . $total_questions . " questions.<br>";
        }
        ?>
        "</h1>

        <!-- <h1 class="heading" id="student-degree">student degree: " "</h1> -->

        <form id="examForm" action="" method="post" enctype="multipart/form-data">

            <p>Student ID: </p>
            <input value="<?= htmlspecialchars($user['NationalID']) ?>" type="text" name="title" class="box" readonly>

            <p>Title: </p>
            <input value="<?= htmlspecialchars($exam['title']) ?>" type="text" name="title" class="box" readonly>

            <p>Description: </p>
            <textarea name="description" class="box" required maxlength="1000" cols="30" rows="10" readonly><?= htmlspecialchars($exam['description']) ?></textarea>

            <p>Subject: </p>
            <input value="<?= htmlspecialchars($exam['subject_title']) ?>" type="text" name="subject" readonly class="box">

            <p>Time and Date: </p>
            <input value="<?= htmlspecialchars($exam['time']) ?>" type="time" name="time" required class="box" readonly>
            <input value="<?= htmlspecialchars($exam['date']) ?>" type="date" name="date" required class="box" readonly>

            <p>Exam Duration Time in MINs: </p>
            <input value="<?= htmlspecialchars($exam['duration']) ?>" type="number" name="duration" required class="box" readonly>

            <p>Exam Degree: </p>
            <input value="<?= htmlspecialchars($exam['degree']) ?>" type="number" name="degree" required class="box" readonly>

            <p>Created At: </p>
            <input value="<?= htmlspecialchars($exam['created_at']) ?>" type="text" name="degree" required class="box" readonly>

            <?php foreach ($attempts as $attempt) : ?>
                <div id="componentContainer" style="border: solid 2px gray  ; padding: 7px; border-radius: 15px; margin-bottom:10px ;">

                    <p>Attempt on: </p>
                    <input value="<?= $attempt['attempt_date'] ?>" type="text" name="degree" required class="box" readonly>

                    <?php foreach ($questions as $index => $question) : ?>
                        <p>Question <?= $index + 1 ?>: </p>

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

                        <input value="<?= htmlspecialchars($question['text']) ?>" type="text" name="questions[<?= $question['id'] ?>]" required class="box" readonly>

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
                                        <input type="radio" name="correct_option[<?= $question['id'] ?>]attempt[<?= $attempt['id'] ?>]" value="<?= $option['id'] ?>" <?= htmlspecialchars($option['text']) == htmlspecialchars($user_answer['option_text']) ? 'checked' : '' ?> disabled> <?= $letter ?>
                                    </p>
                                    <input value="<?= htmlspecialchars($option['text']) ?>" type="text" name="options[<?= $question['id'] ?>][<?= $option['id'] ?>]" class="box" readonly <?= htmlspecialchars($option['text']) == htmlspecialchars($correct_answer['correct_option_text']) ? 'style="color: green;"' : '' ?>>
                                    <input type="hidden" name="original_options[<?= $question['id'] ?>][<?= $option['id'] ?>]" value="<?= htmlspecialchars($option['text']) ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

        </form>

    </section>

    <?php include '../components/footer.php'; ?>


    <script src="../js/admin_script.js"></script>

</body>

</html>