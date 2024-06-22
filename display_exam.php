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

    <?php include 'components/user_exam_header.php'; ?>

    <section class="video-form">

        <h1 class="heading">Exam name: " <?= htmlspecialchars($exam['title']) ?> "</h1>
        <h1 class="heading">Timer: <span id="timer"></span></h1>

        <form action="" method="post" enctype="multipart/form-data">

            <input type="hidden" id="exam_duration" value="<?= htmlspecialchars($exam['duration']) ?>">

            <p>Your Name: </p>
            <input type="text" name="title" placeholder="Enter Your Full Name" class="box" required>

            <p>Your department : </p>
            <input type="text" name="title" placeholder="Enter Your Department " class="box" required>

            <p>Title: </p>
            <input value="<?= htmlspecialchars($exam['title']) ?>" type="text" name="title" class="box" readonly>
            <input type="hidden" name="original_title" value="<?= htmlspecialchars($exam['title']) ?>">

            <p>Description: </p>
            <textarea name="description" class="box" required maxlength="1000" cols="30" rows="10" readonly><?= htmlspecialchars($exam['description']) ?></textarea>
            <input type="hidden" name="original_description" value="<?= htmlspecialchars($exam['description']) ?>">

            <p>Subject: </p>
            <input value="<?= htmlspecialchars($exam['subject_title']) ?>" type="text" name="subject" readonly class="box">
            <input type="hidden" name="original_subject" value="<?= htmlspecialchars($exam['subject_title']) ?>">

            <p>Time and Date: </p>
            <input value="<?= htmlspecialchars($exam['time']) ?>" type="time" name="time" required class="box" readonly>
            <input type="hidden" name="original_time" value="<?= htmlspecialchars($exam['time']) ?>">
            <input value="<?= htmlspecialchars($exam['date']) ?>" type="date" name="date" required class="box" readonly>
            <input type="hidden" name="original_date" value="<?= htmlspecialchars($exam['date']) ?>">

            <p>Exam Duration Time in MINs: </p>
            <input value="<?= htmlspecialchars($exam['duration']) ?>" type="number" name="duration" required class="box" readonly>
            <input type="hidden" name="original_duration" value="<?= htmlspecialchars($exam['duration']) ?>">

            <p>Exam Degree: </p>
            <input value="<?= htmlspecialchars($exam['degree']) ?>" type="number" name="degree" required class="box" readonly>
            <input type="hidden" name="original_degree" value="<?= htmlspecialchars($exam['degree']) ?>">

            <div id="componentContainer">
                <?php foreach ($questions as $index => $question) : ?>
                    <p>Question <?= $index + 1 ?>: </p>
                    <input value="<?= htmlspecialchars($question['text']) ?>" type="text" name="questions[<?= $question['id'] ?>]" required class="box" readonly>
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
                                    <input type="radio" name="correct_option[<?= $question['id'] ?>]" value="<?= $option['id'] ?>" <?= $option['is_correct'] ? '' : '' ?>> <?= $letter ?>
                                </p>
                                <input value="<?= htmlspecialchars($option['text']) ?>" type="text" name="options[<?= $question['id'] ?>][<?= $option['id'] ?>]" class="box" readonly>
                                <input type="hidden" name="original_options[<?= $question['id'] ?>][<?= $option['id'] ?>]" value="<?= htmlspecialchars($option['text']) ?>">
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
                    alert('Time is up! Submitting the exam.');
                    document.getElementById('examForm').submit();
                }
            }, 1000);
        };
    </script>

    <script>
        document.addEventListener('keydown', function(event) {
            if (event.ctrlKey && event.key === 'r') {
                event.preventDefault();
            }
        });

        // document.addEventListener('keydown', function(event) {
        //     if (event.metaKey && event.key === 'g') {
        //         event.preventDefault();
        //         event.stopPropagation();
        //         console.log('Win+G combination pressed. Default action prevented.');
        //     }
        // });

        // window.addEventListener('load', function() {
        //     window.addEventListener('keydown', function(event) {
        //         if (event.metaKey && event.key.toLowerCase() === 'g') {
        //             event.preventDefault();
        //             event.stopPropagation();
        //             console.log('Win+G combination pressed. Default action prevented.');
        //         }
        //     });
        // });

        document.addEventListener('contextmenu', function(event) {
            event.preventDefault();
        });

        window.addEventListener('blur', function() {
            // Show an alert with only an OK button
            alert("You have switched away from the exam page. You will now be redirected to the home page.");

            // Redirect to home.php
            window.location.href = 'home.php';
        });

        window.addEventListener('beforeunload', function(event) {
            // Customize the confirmation message
            var confirmationMessage = "You are about to reload the page. You will now be redirected to the home page.";

            // Show a custom confirmation dialog with only an OK button
            if (confirm(confirmationMessage)) {
                // Redirect to home.php
                window.location.href = 'home.php';
            }

            // Set the confirmation message in some browsers
            (event || window.event).returnValue = confirmationMessage; // For IE and Firefox
            return confirmationMessage; // For other browsers
        });

        // // Listen for keydown events on the window
        // window.addEventListener('keydown', function(event) {
        //     // Check if the key combination is Ctrl+Tab
        //     if (event.ctrlKey && event.key === 'Tab') {
        //         // Prevent the default browser behavior
        //         event.preventDefault();
        //     }
        // });

        // // Listen for keydown events on the window
        // window.addEventListener('keydown', function(event) {
        //     // Check if the key combination is Ctrl+Tab
        //     if (event.ctrlKey && event.key === 'Tab') {
        //         // Prevent the default browser behavior
        //         event.preventDefault();
        //         // Display an alert
        //         alert("You have pressed Ctrl+Tab. Click OK to continue.");
        //         window.location.href = "home.php";
        //     }
        // });

        // Listen for keydown events on the window
        window.addEventListener('keydown', function(event) {
            // Check if the key combination is Ctrl+Tab
            if (event.ctrlKey && event.key === 'Tab') {
                // Prevent the default browser behavior
                event.preventDefault();
                // Redirect to home.php
                window.location.href = "home.php";
            }
        });

        // // Listen for fullscreen change events
        // document.addEventListener('fullscreenchange', function(event) {
        //     // Check if the page is not in fullscreen mode
        //     if (!document.fullscreenElement) {
        //         // Show a confirmation dialog with only an OK button
        //         var confirmationMessage = "You are not in fullscreen mode. You will now be redirected to the home page.";
        //         alert(confirmationMessage);

        //         // Redirect to home.php
        //         window.location.href = 'home.php';
        //     }
        // });

        window.onresize = function() {
            alert("You have resized the window. Click OK to go to the home page.");
            window.location.href = "home.php";
        };
    </script>


    <script src="js/admin_script.js"></script>

</body>

</html>