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


function editComment($connection, $commentId)
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
            $editStmt = $connection->prepare("UPDATE feedbacks SET title=?,comment=? WHERE feedback_id=?");
            $editStmt->bind_param('ssi',  $titleInput, $commentInput, $commentId);
            $editStmt->execute();
            $result = $editStmt->get_result();
        } catch (Exception $e) {
            showNotification("Something went wrong");
        }


        // redirect to home
        header("location:../dashboard/home.php");
    }
}

if ($_GET) {

    $fullName = filter_var($_GET['full_name'], FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);
    $title = filter_var($_GET['title'], FILTER_SANITIZE_SPECIAL_CHARS);
    $comment = filter_var($_GET['comment'], FILTER_SANITIZE_SPECIAL_CHARS);
    $commentId = filter_var($_GET['commentId'], FILTER_SANITIZE_SPECIAL_CHARS);
} else if ($_POST) {
    editComment($connection, $_POST['commentId']);
}


?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Complaint</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- Nav -->
    <?php include '../inc/navigation.php' ?>



    <section class="feedback_modal" id="feedback-modal">


        <form class="feedback_modal_form_container" action="./feedback_modal_edit.php" method="POST">

            <div class="feedback_modal_file_upload_container">
                File Upload
            </div>

            <div class="feedback_modal_form">

                <input type="text" name="commentId" value="<?php echo (isset($commentId)) ? $commentId : ''; ?>" hidden>
                <!-- Name -->
                <div class="feedback_form_input_container">
                    <label for="fullName" class="feedback_form_label">Full Name</label>
                    <input type="text" name="full_name" value="<?php echo (isset($fullName)) ? $fullName : ''; ?>">
                </div>

                <!-- Email -->
                <div class="feedback_form_input_container">
                    <label for="email" class="feedback_form_label">Email</label>
                    <input type="text" name="email" value="<?php echo (isset($email)) ? $email : ''; ?>">
                </div>

                <!-- Email -->
                <div class="feedback_form_input_container">
                    <label for="email" class="feedback_form_label">Title</label>
                    <input type="text" name="title" value="<?php echo (isset($title)) ? $title : ''; ?>">
                </div>

                <!-- Text Area -->
                <div class="feedback_form_input_container">
                    <label for="comment" class="feedback_form_label">Comment</label>
                    <textarea name="comment" cols="30"
                        rows="12"><?php echo (isset($comment)) ? $comment : ''; ?></textarea>
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