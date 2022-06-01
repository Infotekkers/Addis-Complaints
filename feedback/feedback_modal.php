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
    $fullNameInput = filter_var($_POST['fullName'], FILTER_SANITIZE_SPECIAL_CHARS);
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
        $date = date_create()->format('Y-m-d H:i:s');
        $stmt = $connection->prepare("INSERT INTO feedbacks (feedback_id, user_id, title,comment,date,status) VALUES ('', ?, ?, ?, ? , '')");
        $stmt->bind_param('isss', $uid, $titleInput, $commentInput, $date,);

        $stmt->execute();
        $result = $stmt->get_result();

        header("location:../dashboard/home.php");
    }
}

if ($_POST) {
    $isEdit =
        filter_var($_POST['isEdit'], FILTER_SANITIZE_SPECIAL_CHARS);

    $isAdd =
        filter_var($_POST['isAdd'], FILTER_SANITIZE_SPECIAL_CHARS);

    $isSubmit =
        filter_var($_POST['isSubmit'], FILTER_SANITIZE_SPECIAL_CHARS);

    $fullName = filter_var($_POST['fullName'], FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_SPECIAL_CHARS);
    $title = filter_var($_POST['title'], FILTER_SANITIZE_SPECIAL_CHARS);
    $comment = filter_var($_POST['comment'], FILTER_SANITIZE_SPECIAL_CHARS);
    $commentId = filter_var($_POST['commentId'], FILTER_SANITIZE_SPECIAL_CHARS);

    if ($isAdd == "1") {
    } else if ($isEdit == "1") {
        $fullNameEdit = $fullName;
        $emailEdit = $email;
        $titleEdit = $title;
        $commentEdit = $comment;
    } else if ($isSubmit == "1") {
        addNewComment($connection);
    }
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


        <form class="feedback_modal_form_container" action="../feedback/feedback_modal.php" method="POST">

            <div class="feedback_modal_file_upload_container">
                File Upload
            </div>

            <div class="feedback_modal_form">

                <input type="text" name="isSubmit" value="1" hidden>
                <input type="text" name="isAdd" value="0" hidden>
                <input type="text" name="isEdit" value="0" hidden>

                <!-- Name -->
                <div class="feedback_form_input_container">
                    <label for="fullName" class="feedback_form_label">Full Name</label>
                    <input type="text" name="fullName" value="<?php echo (isset($fullNameEdit)) ? $fullName : ''; ?>">
                </div>

                <!-- Email -->
                <div class="feedback_form_input_container">
                    <label for="email" class="feedback_form_label">Email</label>
                    <input type="text" name="email" value="<?php echo (isset($emailEdit)) ? $email : ''; ?>">
                </div>

                <!-- Email -->
                <div class="feedback_form_input_container">
                    <label for="email" class="feedback_form_label">Title</label>
                    <input type="text" name="title" value="<?php echo (isset($titleEdit)) ? $title : ''; ?>">
                </div>

                <!-- Text Area -->
                <div class="feedback_form_input_container">
                    <label for="comment" class="feedback_form_label">Comment</label>
                    <textarea name="comment" cols="30"
                        rows="12"><?php echo (isset($commentEdit)) ? $comment : ''; ?></textarea>
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