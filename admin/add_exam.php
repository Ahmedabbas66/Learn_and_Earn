<?php

include '../components/connect.php';

if (isset($_COOKIE['tutor_id'])) {
$tutor_id = $_COOKIE['tutor_id'];
} else {
$tutor_id = '';
header('location:login.php');
}

if (isset($_POST['submit'])) {

$id = unique_id();
$status = $_POST['status'];
$status = filter_var($status, FILTER_SANITIZE_STRING);
$title = $_POST['title'];
$title = filter_var($title, FILTER_SANITIZE_STRING);
$description = $_POST['description'];
$description = filter_var($description, FILTER_SANITIZE_STRING);
$playlist = $_POST['playlist'];
$playlist = filter_var($playlist, FILTER_SANITIZE_STRING);

$thumb = $_FILES['thumb']['name'];
$thumb = filter_var($thumb, FILTER_SANITIZE_STRING);
$thumb_ext = pathinfo($thumb, PATHINFO_EXTENSION);
$rename_thumb = unique_id() . '.' . $thumb_ext;
$thumb_size = $_FILES['thumb']['size'];
$thumb_tmp_name = $_FILES['thumb']['tmp_name'];
$thumb_folder = '../uploaded_files/' . $rename_thumb;

$video = $_FILES['video']['name'];
$video = filter_var($video, FILTER_SANITIZE_STRING);
$video_ext = pathinfo($video, PATHINFO_EXTENSION);
$rename_video = unique_id() . '.' . $video_ext;
$video_tmp_name = $_FILES['video']['tmp_name'];
$video_folder = '../uploaded_files/' . $rename_video;

if ($thumb_size > 2000000) {
    $message[] = 'image size is too large!';
} else {
    $add_playlist = $conn->prepare("INSERT INTO `content`(id, tutor_id, playlist_id, title, description, video, thumb, status) VALUES(?,?,?,?,?,?,?,?)");
    $add_playlist->execute([$id, $tutor_id, $playlist, $title, $description, $rename_video, $rename_thumb, $status]);
    move_uploaded_file($thumb_tmp_name, $thumb_folder);
    move_uploaded_file($video_tmp_name, $video_folder);
    $message[] = 'new course uploaded!';
}
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
        <select name="playlist" class="box" required>
            <option value="" disabled selected>-- select subject --</option>
            <?php
            $select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
            $select_playlists->execute([$tutor_id]);
            if ($select_playlists->rowCount() > 0) {
                while ($fetch_playlist = $select_playlists->fetch(PDO::FETCH_ASSOC)) {
            ?>
                    <option value="<?= $fetch_playlist['id']; ?>"><?= $fetch_playlist['title']; ?></option>
                <?php
                }
                ?>
            <?php
            } else {
                echo '<option value="" disabled>no playlist created yet!</option>';
            }
            ?>
        </select>
        <p>select time and date <span>*</span></p>
        <input type="time" required class="box">
        <input type="date" required class="box">
        <p>select exam duration time in min <span>*</span></p>
        <input type="number" required class="box">
        <p>select exam degree <span>*</span></p>
        <input type="number" required class="box">
        <div id="componentContainer"></div>
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
                <input type="text" placeholder="Enter your question here" required class="box">
                <div class="options">
                    <div class="option">
                        <p><input type="radio" name="question${number}" value="A"> A</p>
                        <input type="text" placeholder="Option A" class="box">
                    </div>
                    <div class="option">
                        <p><input type="radio" name="question${number}" value="B"> B</p>
                        <input type="text" placeholder="Option B" class="box">
                    </div>
                    <div class="option">
                        <p><input type="radio" name="question${number}" value="C"> C</p>
                        <input type="text" placeholder="Option C" class="box">
                    </div>
                    <div class="option">
                        <p><input type="radio" name="question${number}" value="D"> D</p>
                        <input type="text" placeholder="Option D" class="box">
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