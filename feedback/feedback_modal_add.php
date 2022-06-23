<?php

include_once "../config/db.php";
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


            // get file info
            $fileToUpload = $_FILES['file'];
            $fileSize = $_FILES['file']['size'];
            $fileTempLocation = $_FILES['file']['tmp_name'];
            $fileError = $_FILES['file']['error'];
            $fileName = $_FILES['file']['name'];
            $fileType = $_FILES['file']['type'];
            $fileExtension = explode(".", $fileName);
            $fileExtensionLwr = strtolower(end($fileExtension));

            $allowedFileExtensions = array("pdf");

            // if no file is selected
            if ($fileSize === 0 && $fileError !== 0) {
                $date = date_create()->format('Y-m-d H:i:s');
                $stmt = $connection->prepare("INSERT INTO feedbacks (feedback_id, user_id, title,comment,date,status,filePath) VALUES ('', ?, ?, ?, ? , '','')");
                $stmt->bind_param('isss', $uid, $titleInput, $_POST['comment'], $date);
                $stmt->execute();
                $result = $stmt->get_result();
            }
            // if file selected
            else {
                // if file is valid
                if (in_array($fileExtensionLwr, $allowedFileExtensions)) {
                    // check file error
                    if ($fileError === 0) {
                        // check file size
                        if ($fileSize < 25000000) {
                            $newFileName = uniqid("", true) . "." . $fileExtensionLwr;
                            $fileUploadRoot = "../uploads/" . $newFileName;

                            move_uploaded_file($fileTempLocation, $fileUploadRoot);

                            // save to db
                            $date = date_create()->format('Y-m-d H:i:s');
                            $stmt = $connection->prepare("INSERT INTO feedbacks (feedback_id, user_id, title,comment,date,status,filePath) VALUES ('', ?, ?, ?, ? , '',?)");
                            $stmt->bind_param('issss', $uid, $titleInput, $_POST['comment'], $date, $newFileName);
                            $stmt->execute();
                            $result = $stmt->get_result();
                        } else {
                            showNotification("File is too large.");
                        }
                    } else {
                        showNotification("File error.Try another file.");
                    }
                } else {
                    showNotification("Invalid File format.");
                }
            }
        } catch (Exception $e) {
            showNotification("Something went wrong");
        }

        // redirect to home
        header("location:../dashboard/home.php");
    }
}

if ($_POST) {

    try {
        if ($_POST['isSubmit'] == 1) {
            addNewComment($connection);
        }
    } catch (Exception $e) {
        $x = 1;
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


        <form class="feedback_modal_form_container" action="./feedback_modal_add.php" method="POST"
            enctype="multipart/form-data">

            <div class="feedback_modal_file_upload_container" width="100%" height="500px">
                <input type="file" name="file">
            </div>

            <div class="feedback_modal_form">

                <input type="text" name="isSubmit" value="1" hidden>
                <!-- Name -->
                <div class="feedback_form_input_container">
                    <label for="fullName" class="feedback_form_label">Full Name</label>
                    <input type="text" name="full_name">
                </div>

                <!-- Email -->
                <div class="feedback_form_input_container">
                    <label for="email" class="feedback_form_label">Email</label>
                    <input type="text" name="email">
                </div>

                <!-- Email -->
                <div class="feedback_form_input_container">
                    <label for="email" class="feedback_form_label">Title</label>
                    <input type="text" name="title">
                </div>

                <!-- Text Area -->
                <div class="feedback_form_input_container">
                    <label for="comment" class="feedback_form_label">Comment</label>
                    <textarea name="comment" cols="30" rows="12"></textarea>
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