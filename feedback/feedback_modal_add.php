<?php

include "../config/db/user.php";
session_start();
session_regenerate_id();
// $_SESSION['antiCSRFToken'] = bin2hex(random_bytes(35));

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


function addNewComment($connection)
{

    // $token = filter_input(INPUT_POST, 'antiCSRFToken', FILTER_SANITIZE_SPECIAL_CHARS);

    // if (!$token || $token !== $_SESSION['antiCSRFToken']) {
    // showNotification("Malicious Attempt!");
    // session_destroy();
    // header("location:../auth/login/login.php");
    // exit;

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

    // check title
    // if (!preg_match($titlePattern, $titleInput)) {
    // showNotification("Invalid title");
    // }

    // check comment
    // if (!preg_match($commentPattern, $commentInput)) {
    // showNotification("Invalid comment");
    // }

    else {
        $uid = $_SESSION['uid'];


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

                // redirect to home
                header("location:../dashboard/home.php");
            }
            // if file selected
            else {
                // if file has proper extension
                if (in_array($fileExtensionLwr, $allowedFileExtensions)) {
                    // check file content
                    $fileContent = file_get_contents($fileTempLocation);

                    // check pdf met data
                    if (preg_match("/^%PDF-1.1/", $fileContent) == false && preg_match("/^%PDF-1.2/", $fileContent) == false && preg_match("/^%PDF-1.3/", $fileContent) == false && preg_match("/^%PDF-1.4/", $fileContent) == false && preg_match("/^%PDF-1.5/", $fileContent) == false && preg_match("/^%PDF-1.6/", $fileContent) == false && preg_match("/^%PDF-1.7/", $fileContent) == false) {
                        showNotification("Invalid File format.");
                    }
                    // check php tags
                    else if (str_contains($fileContent, "<?php")) {
                        session_destroy();
                        header("location:../auth/login/login.php");
                        exit("Wasted!");
                    }
                    // check file error
                    else if ($fileError === 0) {
                        // check file size
                        if ($fileSize < 25000000) {
                            $newFileName = uniqid("", true) . "." . $fileExtensionLwr;
                            $fileUploadRoot = "../uploads/" .
                                $newFileName;
                            move_uploaded_file($fileTempLocation, $fileUploadRoot);
                            // save to db
                            $date = date_create()->format('Y-m-d H:i:s');
                            $stmt = $connection->prepare("INSERT INTO feedbacks (feedback_id, user_id, title,comment,date,status,filePath) VALUES ('', ?, ?, ?, ? , '',?)");
                            $stmt->bind_param('issss', $uid, $titleInput, $_POST['comment'], $date, $newFileName);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            // redirect to home
                            header("location:../dashboard/home.php");
                        }
                        // large file size
                        else {
                            showNotification("File is too large.");
                        }
                    }
                    // file has error
                    else {
                        showNotification("File error.Try another file.");
                    }
                }
                // invalid extension
                else {
                    showNotification("Invalid File format.");
                }
            }
        } catch (Exception $e) {
            showNotification("Something went wrong");
        }
    }
    // }
}

if ($_POST) {

    try {
        if ($_POST['isSubmit'] == 1) {
            $secret = "6LdM_jMgAAAAAHomg-xBvg2IXJMljM-mJMEPAtU8";
            $response = $_POST['g-recaptcha-response'];
            $remoteip = $_SERVER['REMOTE_ADDR'];
            $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response&remoteip=$remoteip";
            $data = file_get_contents($url);
            $row = json_decode($data, true);
            if ($row['success'] == "true") {
                addNewComment($connection);
            } else {
                showNotification("Captcha Failed");
            }
        }
    } catch (Exception $e) {
        showNotification("Something Went Wrong");
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
            enctype="multipart/form-data" autocomplete="off">

            <div class="feedback_modal_file_upload_container" width="100%" height="500px">
                <input type="file" name="file">
            </div>

            <div class="feedback_modal_form">

                <input type="text" name="isSubmit" value="1" hidden>
                <input type="text" name="antiCSRFToken" value="<?= $_SESSION['antiCSRFToken'] ?? '' ?>" hidden>
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


                <!-- captcha -->

                <div class="g-recaptcha" data-sitekey="6LdM_jMgAAAAAC7VXyp8sdSNulMdZa8s68zNDsWE"></div>


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