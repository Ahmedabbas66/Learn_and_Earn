<?php
// Include the database connection file
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Insert a new exam attempt
    $insert_attempt = $conn->prepare("INSERT INTO exam_attempts (user_id, exam_id) VALUES (?, ?)");
    $insert_attempt->execute([$user_id, $exam_id]);
    $attempt_id = $conn->lastInsertId();

    // Insert user answers
    foreach ($_POST['correct_option'] as $question_id => $selected_option_id) {
        $insert_answer = $conn->prepare("INSERT INTO user_answers (attempt_id, question_id, selected_option_id) VALUES (?, ?, ?)");
        $insert_answer->execute([$attempt_id, $question_id, $selected_option_id]);
    }

    // Redirect to home.php after submission
    header("Location: home.php");
    exit();
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
    <link rel="stylesheet" href="css/admin_style.css">

    <script src="https://webgazer.cs.brown.edu/webgazer.js"></script>

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
            padding-bottom: 1.8rem;
            font-size: 2.5rem;
            color: var(--black);
            text-transform: capitalize;
            margin-bottom: 2rem;
        }
    </style>
    <style>
        #warning {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: red;
            color: white;
            font-size: 24px;
            display: none;
            z-index: 1000;
            border-radius: 30px;
        }

        #webgazerVideoFeed {
            position: fixed;
            top: 90px;
            left: 1200px;
            z-index: 1000;
            width: 200px;
            height: auto;
        }

        /* Adjust the position and appearance of the prediction points */
        #webgazerFaceOverlay {
            position: fixed;
            top: 90px;
            left: 1200px;
            z-index: 1000;
        }

        #webgazerFaceFeedbackBox {
            position: fixed;
            top: 90px;
            left: 1200px;
            z-index: 1000;
        }
    </style>

</head>

<body>

    <?php include 'components/user_exam_header.php'; ?>

    <div id="warning">Please look at the screen!</div>

    <section class="video-form">

        <h1 class="heading">Exam name: " <?= htmlspecialchars($exam['title']) ?> "</h1>
        <h1 class="heading">Timer: <span id="timer"></span></h1>

        <form id="examForm" action="" method="post" enctype="multipart/form-data">
            <input type="hidden" id="exam_duration" value="<?= htmlspecialchars($exam['duration']) ?>">

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

            <div id="componentContainer">
                <?php foreach ($questions as $index => $question) : ?>
                    <p>Question <?= $index + 1 ?>: </p>
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
                                    <input type="radio" name="correct_option[<?= $question['id'] ?>]" value="<?= $option['id'] ?>" required> <?= $letter ?>
                                </p>
                                <input value="<?= htmlspecialchars($option['text']) ?>" type="text" name="options[<?= $question['id'] ?>][<?= $option['id'] ?>]" class="box" readonly>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <input type="submit" value="Submit Exam" name="update" class="btn">
        </form>

    </section>

    <?php include 'components/footer.php'; ?>

    <script>
        function submitExam() {
            document.getElementById('examForm').submit();
        }

        window.onload = function() {
            var duration = document.getElementById('exam_duration').value;
            var timer = document.getElementById('timer');

            var timeRemaining = duration * 60;

            var countdown = setInterval(function() {
                var minutes = Math.floor(timeRemaining / 60);
                var seconds = timeRemaining % 60;

                timer.textContent = minutes + ":" + (seconds < 10 ? '0' : '') + seconds;

                timeRemaining--;

                if (timeRemaining < 0) {
                    clearInterval(countdown);
                    submitExam();
                }
            }, 1000);

            const warning = document.getElementById('warning');
            const screenCenterX = window.innerWidth / 2;
            const screenCenterY = window.innerHeight / 2;
            const horizontalTolerance = window.innerWidth / 3;
            const verticalTolerance = window.innerHeight / 2;

            webgazer.setGazeListener((data, elapsedTime) => {
                if (data) {
                    const x = data.x;
                    const y = data.y;

                    const screenLeftBoundary = screenCenterX - horizontalTolerance;
                    const screenRightBoundary = screenCenterX + horizontalTolerance;
                    const screenTopBoundary = screenCenterY - verticalTolerance;
                    const screenBottomBoundary = screenCenterY + verticalTolerance;

                    if (x < screenLeftBoundary || x > screenRightBoundary || y < screenTopBoundary || y > screenBottomBoundary) {
                        warning.style.display = 'block';
                    } else {
                        warning.style.display = 'none';
                    }
                }
            }).begin();

            webgazer.showVideoPreview(true)
                .showPredictionPoints(true)
                .applyKalmanFilter(true)
                .showFaceOverlay(true);

            window.onbeforeunload = function() {
                webgazer.end();
            };

        };

        document.addEventListener('keydown', function(event) {
            if (event.ctrlKey && event.key === 'r') {
                event.preventDefault();
            }
        });

        document.addEventListener('contextmenu', function(event) {
            event.preventDefault();
        });

        window.addEventListener('blur', function() {
            submitExam();
        });

        window.addEventListener('beforeunload', function(event) {
            submitExam();
        });

        window.addEventListener('keydown', function(event) {
            if (event.ctrlKey && event.key === 'Tab') {
                event.preventDefault();
                submitExam();
            }
        });

        window.onresize = function() {
            submitExam();
        };
    </script>


    <script src="js/script.js"></script>

</body>

</html>