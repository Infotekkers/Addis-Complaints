<?php

include "../config/db/user.php";
session_start();
session_regenerate_id();
$_SESSION['antiCSRFToken'] = bin2hex(random_bytes(35));

if (!isset($_SESSION['uid'])) {
    header("location:../auth/login/login.php");
    exit("Unauthenticated!");
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
    // check token

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
    elseif (!preg_match($emailPattern, $emailInput)) {
        showNotification("Invalid Email");
    }

    // check comment
    // if (!preg_match($commentPattern, $commentInput)) {
    //     showNotification("Invalid comment");
    // } 

    else {
        $uid  = $_SESSION['uid'];
        try {
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
                $editStmt = $connection->prepare("UPDATE feedbacks SET title=?,comment=? WHERE feedback_id=?");
                $editStmt->bind_param('ssi',  $titleInput, $commentInput, $commentId);
                $editStmt->execute();
                $result = $editStmt->get_result();
            } else {
                if (in_array($fileExtensionLwr, $allowedFileExtensions)) {
                    if ($fileError === 0) {
                        if ($fileSize < 25000000) {
                            $newFileName = uniqid("", true) . "." . $fileExtensionLwr;
                            $fileUploadRoot = "../uploads/" . $newFileName;
                            move_uploaded_file($fileTempLocation, $fileUploadRoot);


                            $editStmt = $connection->prepare("UPDATE feedbacks SET title=?,comment=?,filePath=? WHERE feedback_id=?");
                            $editStmt->bind_param('sssi',  $titleInput, $commentInput, $newFileName, $commentId,);
                            $editStmt->execute();
                            $result = $editStmt->get_result();
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

if ($_GET) {

    $fullName = filter_var($_GET['full_name'], FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);
    $title = filter_var($_GET['title'], FILTER_SANITIZE_SPECIAL_CHARS);
    $comment = filter_var($_GET['comment'], FILTER_SANITIZE_SPECIAL_CHARS);
    $commentId = filter_var($_GET['commentId'], FILTER_SANITIZE_SPECIAL_CHARS);
    $filePath = filter_var($_GET['filePath'], FILTER_SANITIZE_SPECIAL_CHARS);
} else if ($_POST) {

    $secret = "6LdM_jMgAAAAAHomg-xBvg2IXJMljM-mJMEPAtU8";
    $response = $_POST['g-recaptcha-response'];
    $remoteip = $_SERVER['REMOTE_ADDR'];
    $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response&remoteip=$remoteip";
    $data = file_get_contents($url);
    $row = json_decode($data, true);
    if ($row['success'] == "true") {
        editComment($connection, $_POST['commentId']);
    } else {
        // header("Refresh:0");
        showNotification("Captcha Failed");
    }
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


        <form class="feedback_modal_form_container" action="./feedback_modal_edit.php" method="POST"
            enctype="multipart/form-data">

            <input type="text" name="antiCSRFToken" value="<?= $_SESSION['antiCSRFToken'] ?? '' ?>" hidden>

            <div class="feedback_modal_file_upload_container">
                <input type="file" name="file">

                <?php
                if ($filePath !== '') {
                    $_SESSION['filePath'] = $filePath;
                    $_SESSION['commentId'] = $commentId
                ?>
                <a href="../download/download_feedback.php" class="file-download-button">Download</a>
                <?php }
                ?>

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

                <!-- Title -->
                <div class="feedback_form_input_container">
                    <label for="email" class="feedback_form_label">Title</label>
                    <select name="title" value="<?php echo (isset($title)) ? $title : ''; ?>" required>
                        <option disabled selected></option>
                        <option value="Corruption" <?php if ($title == 'Corruption') : ?> selected="selected"
                            <?php endif; ?>>Corruption</option>
                        <option value="Parking Issues" <?php if ($title == 'Parking Issues') : ?> selected="selected"
                            <?php endif; ?>>Parking Issues</option>
                        <option value="Potholes" <?php if ($title == 'Potholes') : ?> selected="selected"
                            <?php endif; ?>>
                            Potholes</option>
                        <option value="Public Property Abuse" <?php if ($title == 'Public Property Abuse') : ?>
                            selected="selected" <?php endif; ?>>Public Property Abuse</option>
                        <option value="Transport Issues" <?php if ($title == 'Transport Issues') : ?>
                            selected="selected" <?php endif; ?>>Transport Issues</option>
                    </select>
                </div>

                <!-- Text Area -->
                <div class="feedback_form_input_container">
                    <label for="comment" class="feedback_form_label">Comment</label>
                    <!-- <span id="word-count">0/100</span> -->
                    <span>(500 characters minimum)</span>
                    <textarea name="comment" cols="30" rows="12" minlength="500"
                        required><?php echo (isset($comment)) ? $comment : ''; ?></textarea>
                </div>


                <!-- captcha -->
                <div class="captcha_container">
                    <div class="g-recaptcha" data-sitekey="6LdM_jMgAAAAAC7VXyp8sdSNulMdZa8s68zNDsWE"></div>
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

<!-- Google Captcha v2 -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>