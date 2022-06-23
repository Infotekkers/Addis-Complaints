<?php

include "../config/db.php";
include "../inc/redirect.php";
$base_url = "http://localhost:3000";


$show_notification_message = false;
$notification_message_content = "";
session_start();
session_regenerate_id();
$_SESSION['antiCSRFToken'] = bin2hex(random_bytes(35));


function showNotification($notificationMessage)
{
    $show_notification_message = true;
    $notification_message_content = $notificationMessage;
    include '../inc/notification.php';
}

$Infostmt = $connection->prepare("SELECT id, role FROM super_admin WHERE id=?");
$Infostmt->bind_param('i', $userId);
$Infostmt->execute();
$adminResult = $Infostmt->get_result();
$adminResult = $adminResult->fetch_array(MYSQLI_ASSOC);

if (!password_verify($adminResult['id'] . $adminResult['role'], $_SESSION['sessionHash'])) {
    Redirect("$base_url/dashboard/home.php");
    exit("Unauthorized!");
}

function registerSuperAdmin($connection)
{
    $fullNameInput = filter_var($_POST['fullName'], FILTER_SANITIZE_SPECIAL_CHARS);
    $fullNamePattern = "/^[a-zA-Z]+(([',. -][a-zA-Z ])?[a-zA-Z]*)*$/";


    $emailInput = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $emailPattern = "/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/";

    $passwordInput = filter_var($_POST['password'], FILTER_SANITIZE_EMAIL);
    $passwordPattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/";

    try {
        if (!preg_match($fullNamePattern, $fullNameInput) || strlen($fullNameInput) > 24) {
            showNotification("Invalid Name");
        }

        // check email
        elseif (!preg_match($emailPattern, $emailInput)) {
            showNotification("Invalid Email");
        }

        // check password
        elseif (!preg_match($passwordPattern, $passwordInput) || strlen($passwordInput) > 24) {
            showNotification("Invalid Password");
        } else {
            // check duplicate
            $stmt = $connection->prepare("SELECT email FROM admin WHERE email=?");
            $stmt->bind_param('s', $emailInput);
            $stmt->execute();
            $result = $stmt->get_result();

            // if email already exists
            if (
                $result->num_rows == 1
            ) {
                showNotification("Email already in use");
            } else {
                // hash password
                $passwordHash = password_hash($passwordInput, PASSWORD_BCRYPT);

                // create prepared statements
                $stmt = $connection->prepare("INSERT INTO admin (id, full_name, email,password,attemptCount) VALUES ('', ?, ?, ?,0)");
                $stmt->bind_param('sss', $fullNameInput, $emailInput, $passwordHash);
                $stmt->execute();
                $result = $stmt->get_result();
            }
        }
    } catch (Exception $e) {
        showNotification("Something Went Wrong");
    }
}

if ($_POST) {

    try {

        $secret = "6LdM_jMgAAAAAHomg-xBvg2IXJMljM-mJMEPAtU8";
        $response = $_POST['g-recaptcha-response'];
        $remoteip = $_SERVER['REMOTE_ADDR'];
        $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response&remoteip=$remoteip";
        $data = file_get_contents($url);
        $row = json_decode($data, true);
        if ($row['success'] == "true") {
            registerSuperAdmin($connection);
        } else {
            showNotification("Captcha Failed");
        }
    } catch (Exception $e) {
        showNotification("Something Went Wrong");
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Nav -->
    <?php include '../inc/navigation.php' ?>

    <!-- Body -->
    <div class="super_dash_container">

        <form action="./superdash.php" class="admin-reg-form" method="POST">
            <h1>Register Admin</h1>
            <input type="text" name="sessionToken" value="123456gotem" hidden>

            <input type="text" name="antiCSRFToken" value="<?= $_SESSION['antiCSRFToken'] ?? '' ?>" hidden>

            <div class="container-col">
                <label for="fullName">Full Name:</label>
                <input type="text" name="fullName">
            </div>

            <div class="spacer"></div>

            <div class="container-col">
                <label for="email">Email:</label>
                <input type="text" name="email">
            </div>

            <div class="spacer"></div>

            <div class="container-col">
                <label for="password">Password:</label>
                <input type="password" name="password">
            </div>

            <div class="spacer"></div>

            <!-- Captcha -->
            <div class="captcha_container">
                <div class="g-recaptcha" data-sitekey="6LdM_jMgAAAAAC7VXyp8sdSNulMdZa8s68zNDsWE"></div>
            </div>

            <!-- Create Button -->
            <button class="register_admin_button">Create Admin</button>

        </form>

    </div>
</body>


<!-- Google Captcha v2 -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

</html>