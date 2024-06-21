<?php

include '../components/connect.php';

if (isset($_COOKIE['tutor_id'])) {
    $tutor_id = $_COOKIE['tutor_id'];
} else {
    $tutor_id = '';
    header('location:login.php');
}


if (isset($_POST['submit'])) {
    $status = $_POST['status'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $subject = $_POST['subject'];
    $time = $_POST['time'];
    $date = $_POST['date'];
    $duration = $_POST['duration'];
    $degree = $_POST['degree'];

    $id = unique_id();
    $status = $_POST['status'];
    $status = filter_var($status, FILTER_SANITIZE_STRING);
    $title = $_POST['title'];
    $title = filter_var($title, FILTER_SANITIZE_STRING);
    $description = $_POST['description'];
    $description = filter_var($description, FILTER_SANITIZE_STRING);
    $subject = $_POST['subject'];
    $subject = filter_var($subject, FILTER_SANITIZE_STRING);

    $thumb = $_FILES['thumb']['name'];
    $thumb = filter_var($thumb, FILTER_SANITIZE_STRING);
    $thumb_ext = pathinfo($thumb, PATHINFO_EXTENSION);
    $rename_thumb = unique_id() . '.' . $thumb_ext;
    $thumb_size = $_FILES['thumb']['size'];
    $thumb_tmp_name = $_FILES['thumb']['tmp_name'];
    $thumb_folder = '../uploaded_files/' . $rename_thumb;


    if ($thumb_size > 2000000) {
        $message[] = 'image size is too large!';
    } else {
        $insert_exam = $conn->prepare("INSERT INTO exams (id ,status, title, description, subject, time, date, duration, degree ,tutor_id,subject_id ,thumb) VALUES (?, ?, ?, ?, ?, ?, ?, ? ,? , ?, ?, ?)");
        $insert_exam->execute([$id, $status, $title, $description, $subject, $time, $date, $duration, $degree, $tutor_id, $subject, $rename_thumb]);
        move_uploaded_file($thumb_tmp_name, $thumb_folder);
        //    $message[] = 'new course uploaded!';
    }


    $exam_id = $conn->lastInsertId();

    if (isset($_POST['questions']) && is_array($_POST['questions'])) {
        foreach ($_POST['questions'] as $question) {
            $question_text = $question['text'];
            $insert_question = $conn->prepare("INSERT INTO questions (exam_id, text) VALUES (?, ?)");
            $insert_question->execute([$exam_id, $question_text]);

            $question_id = $conn->lastInsertId();

            foreach ($question['options'] as $option_value => $option_text) {
                $is_correct = ($question['correct'] == $option_value) ? 1 : 0;
                $insert_option = $conn->prepare("INSERT INTO options (question_id, text, is_correct) VALUES (?, ?, ?)");
                $insert_option->execute([$question_id, $option_text, $is_correct]);
            }
        }
    }

    $message[] = 'New Exam created successfully!';

    // echo "Exam created successfully!";
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Exam</title>

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

        <h1 class="heading">create and upload exam</h1>

        <form action="" method="post" enctype="multipart/form-data">
            <p>exam status <span>*</span></p>
            <select name="status" class="box" required>
                <option value="" selected disabled>-- select status</option>
                <option value="active">active</option>
                <option value="deactive">deactive</option>
            </select>
            <p>exam title <span>*</span></p>
            <input type="text" name="title" maxlength="100" required placeholder="enter exam title" class="box">
            <p>exam description <span>*</span></p>
            <textarea name="description" class="box" required placeholder="write description" maxlength="1000" cols="30" rows="10"></textarea>
            <p>subject <span>*</span></p>
            <select name="subject" class="box" required>
                <option value="" disabled selected>-- select subject --</option>
                <?php
                $select_subjects = $conn->prepare("SELECT * FROM `subject` WHERE tutor_id = ?");
                $select_subjects->execute([$tutor_id]);
                if ($select_subjects->rowCount() > 0) {
                    while ($fetch_subject = $select_subjects->fetch(PDO::FETCH_ASSOC)) {
                ?>
                        <option value="<?= $fetch_subject['id']; ?>"><?= $fetch_subject['title']; ?></option>
                    <?php
                    }
                    ?>
                <?php
                } else {
                    echo '<option value="" disabled>no subject created yet!</option>';
                }
                ?>
            </select>
            <p>select time and date <span>*</span></p>
            <input type="time" name="time" required class="box">
            <input type="date" name="date" required class="box">

            <p>select exam duration time in min <span>*</span></p>
            <input type="number" name="duration" required class="box">
            <p>select exam degree <span>*</span></p>
            <input type="number" name="degree" required class="box">
            <p>select thumbnail <span>*</span></p>
            <input type="file" name="thumb" accept="image/*" required class="box">
            <div id="componentContainer">
            </div>
            <input type="button" id="createButton" value="create question" class="btn">
            <input type="submit" value="upload exam" name="submit" class="btn">
        </form>

    </section>















    <?php include '../components/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const createButton = document.getElementById('createButton');
            const componentContainer = document.getElementById('componentContainer');
            let questionCounter = 1;

            createButton.addEventListener('click', function() {
                createQuestionComponent(questionCounter);
                questionCounter++;
            });

            function createQuestionComponent(number) {
                // Create a new div element
                const newComponent = document.createElement('div');
                newComponent.className = 'component';

                // Set the inner HTML of the new component
                newComponent.innerHTML = `
                <p>Question ${number}</p>
                <input type="text" name="questions[${number}][text]" placeholder="Enter your question here" required class="box">
                <div class="options">
                    <div class="option">
                        <p><input type="radio" name="questions[${number}][correct]" value="A"> A</p>
                        <input type="text" name="questions[${number}][options][A]" placeholder="Option A" class="box">
                    </div>
                    <div class="option">
                        <p><input type="radio" name="questions[${number}][correct]" value="B"> B</p>
                        <input type="text" name="questions[${number}][options][B]" placeholder="Option B" class="box">
                    </div>
                    <div class="option">
                        <p><input type="radio" name="questions[${number}][correct]" value="C"> C</p>
                        <input type="text" name="questions[${number}][options][C]" placeholder="Option C" class="box">
                    </div>
                    <div class="option">
                        <p><input type="radio" name="questions[${number}][correct]" value="D"> D</p>
                        <input type="text" name="questions[${number}][options][D]" placeholder="Option D" class="box">
                    </div>
                </div>
                <button class="delete-btn" onclick="deleteQuestion(this)"><i class="fa-solid fa-trash"></i><span> delete question</span></button>

            `;

                // Append the new component to the container
                componentContainer.appendChild(newComponent);
            }

            window.deleteQuestion = function(button) {
                const component = button.parentElement;
                component.remove();
                updateQuestionNumbers();
            };

            function updateQuestionNumbers() {
                const components = document.querySelectorAll('.component');
                components.forEach((component, index) => {
                    const questionNumber = index + 1;
                    const questionHeader = component.querySelector('p');
                    questionHeader.textContent = `Question ${questionNumber}`;

                    const radioButtons = component.querySelectorAll('input[type="radio"]');
                    radioButtons.forEach(radio => {
                        radio.name = `question${questionNumber}`;
                    });
                });
                questionCounter = components.length + 1;
            }
        });
    </script>

    <script src="../js/admin_script.js"></script>

</body>

</html>