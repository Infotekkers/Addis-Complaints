<?php

include "../config/db.php";
session_start();

if (!isset($_SESSION['uid'])) {
    header("location:../auth/login/login.php");
}

$show_notification_message = false;
$notification_message_content = "";

function showNotification($notificationMessage)
{
    $show_notification_message = true;
    $notification_message_content = $notificationMessage;
    include '../inc/notification.php';
}


function addNewComment($connection)
{

    $fullNameInput = filter_var($_POST['full_name'], FILTER_SANITIZE_SPECIAL_CHARS);
    $fullNamePattern = "/^[a-zA-Z]+(([',. -][a-zA-Z ])?[a-zA-Z]*)*$/";

    $emailInput = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $emailPattern = "/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/";

    $titleInput = filter_var($_POST['title'], FILTER_SANITIZE_SPECIAL_CHARS);
    $titlePattern = "/^[a-zA-Z0-9_.-]*$/";

    $commentInput = filter_var($_POST['comment'], FILTER_SANITIZE_SPECIAL_CHARS);
    $commentPattern = "/^[a-zA-Z0-9_.-]*$/";

    // check full name
    if (!preg_match($fullNamePattern, $fullNameInput) || strlen($fullNameInput) > 24) {
        showNotification("Invalid Name");
    }
    
    // check email
    if (!preg_match($emailPattern, $emailInput)) {
        showNotification("Invalid Email");
    }
    
    // check title
    // if (!preg_match($titlePattern, $titleInput)) {
    //     showNotification("Invalid title");
    // }

    // check comment
    // if (!preg_match($commentPattern, $commentInput)) {
    //     showNotification("Invalid comment");
    // } 
            
    else {
        $uid  = $_SESSION['uid'];


        try {
            $date = date_create()->format('Y-m-d H:i:s');
            $stmt = $connection->prepare("INSERT INTO feedbacks (feedback_id, user_id, title,comment,date,status) VALUES ('', ?, ?, ?, ? , '')");
            $stmt->bind_param('isss', $uid, $titleInput, $_POST['comment'], $date,);
            $stmt->execute();
            $result = $stmt->get_result();

            echo $uid;
        } catch (Exception $e) {
            showNotification("Something went wrong");
        }


        // redirect to home
        header("location:../dashboard/home.php");
    }
}

if ($_POST) {
    addNewComment($connection);
}


?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Complaint</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- Nav -->
    <?php include '../inc/navigation.php' ?>



    <section class="feedback_modal" id="feedback-modal">


        <form class="feedback_modal_form_container" action="./feedback_modal_add.php" method="POST">

            <div class="feedback_modal_file_upload_container">
                File Upload
            </div>

            <div class="feedback_modal_form">


                <!-- Name -->
                <div class="feedback_form_input_container">
                    <label for="fullName" class="feedback_form_label">Full Name</label>
                    <input type="text" name="full_name" required>
                </div>

                <!-- Email -->
                <div class="feedback_form_input_container">
                    <label for="email" class="feedback_form_label">Email</label>
                    <input type="text" name="email" required>
                </div>

                <!-- Title -->
                <div class="feedback_form_input_container">
                    <label for="email" class="feedback_form_label">Title</label>
                    <select name="title" required>
                        <option disabled selected></option>
                        <option value="Corruption">Corruption</option>
                        <option value="Parking Issues">Parking Issues</option>
                        <option value="Potholes">Potholes</option>
                        <option value="Public Property Abuse">Public Property Abuse</option>
                        <option value="Transport Issues">Transport Issues</option>
                    </select>
                </div>

                <!-- Text Area -->
                <div class="feedback_form_input_container">
                    <label for="comment" class="feedback_form_label">Comment</label>
                    <!-- <span id="word-count">0/100</span> -->
                    <span>(500 characters minimum)</span>
                    <textarea name="comment" cols="30" rows="12" minlength="500" required></textarea>
                </div>

                <!-- submit -->
                <div class=" feedback_submit_button_container">
                    <input type="submit">
                </div>
            </div>

        </form>

        <script>
        document.getElementById("close-modal").addEventListener("click", () => {
            document.getElementById("feedback-modal").style.display = "none";
        })
        </script>
    </section>
</body>