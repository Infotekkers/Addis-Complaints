<?php

include "../../config/db/user.php";
include "../../config/constants.php";
include "../../inc/redirect.php";
$base_url = "http://localhost:3000";



$show_notification_message = false;
$notification_message_content = "";


function showNotification($notificationMessage)
{
    $show_notification_message = true;
    $notification_message_content = $notificationMessage;
    include '../../inc/notification.php';
}

function registerUser($connection, $salt)
{
    global $base_url;
    try {
        $fullNameInput = filter_var($_POST['fullName'], FILTER_SANITIZE_SPECIAL_CHARS);
        $fullNamePattern = "/^[a-zA-Z]+(([',. -][a-zA-Z ])?[a-zA-Z]*)*$/";


        $emailInput = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $emailPattern = "/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/";

        $passwordInput = filter_var($_POST['password'], FILTER_SANITIZE_EMAIL);
        $passwordPattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/";
        $confirmPasswordInput = $_POST['confirmPassword'];


        // check full name
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
        }

        // check password match
        elseif ($confirmPasswordInput != $passwordInput) {
            showNotification("Passwords do not match");
        } else if (preg_match($fullNamePattern, $fullNameInput)) {

            $stmt = $connection->prepare("SELECT email FROM users WHERE email=?");
            $stmt->bind_param('s', $emailInput);
            $stmt->execute();
            $result = $stmt->get_result();

            // if email already exists
            if ($result->num_rows == 1) {
                showNotification("Email already in use");
            }
            // if all good
            else {
                // hash password
                $passwordHash = password_hash($passwordInput, PASSWORD_BCRYPT);

                // create prepared statements
                $stmt = $connection->prepare("INSERT INTO users (id, full_name, email,password,isActive,attemptCount) VALUES ('', ?, ?, ?, 1 , 0)");
                $stmt->bind_param('sss', $fullNameInput, $emailInput, $passwordHash);
                $stmt->execute();
                $result = $stmt->get_result();
                // header("location:../login/login.php");
                Redirect("$base_url/auth/login/login.php");
                exit;
            }
        }
    } catch (Exception $e) {
        showNotification("Something Went Wrong");
    }
}

// on post
if ($_POST) {

    try {
        $secret = "6LdM_jMgAAAAAHomg-xBvg2IXJMljM-mJMEPAtU8";
        $response = $_POST['g-recaptcha-response'];
        $remoteip = $_SERVER['REMOTE_ADDR'];
        $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response&remoteip=$remoteip";
        $data = file_get_contents($url);
        $row = json_decode($data, true);
        if ($row['success'] == "true") {
            registerUser($connection, $salt);
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
    <title>Register</title>
    <link rel="stylesheet" href="style.css">

</head>

<body class='register_page_body'>
    <!-- Left -->
    <section class="register_page_left_container">
        <p class="register_page_left_title">Addis Complaints </div>

        <form class="register_page_form_container" method="POST" autocomplete="off">

            <!-- Title -->
            <h1>Create Account</h1>


            <!-- Sub Text -->
            <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Sequi quaerat quod maxime ex rerum.
                Consequatur
                sint vero odit suscipit, dicta maiores dolores cum ipsam id?</p>


            <!-- Form Inputs-->
            <div>
                <!-- Full Name -->
                <div class="input_container">
                    <label for="email">Full Name : </label>
                    <input type="text" name="fullName" required>
                </div>

                <!-- Email -->
                <div class="input_container">
                    <label for="email">Email : </label>
                    <input type="text" name="email" required>
                </div>

                <!-- Password -->
                <div class="input_container">
                    <label for="password">Password : </label>
                    <input type="password" name="password" required>
                </div>

                <!-- Confirm Password -->
                <div class="input_container">
                    <label for="confirm-password">Confirm Password : </label>
                    <input type="password" name="confirmPassword" required>
                </div>

                <!-- Captcha -->
                <div class="captcha_container">
                    <div class="g-recaptcha" data-sitekey="6LdM_jMgAAAAAC7VXyp8sdSNulMdZa8s68zNDsWE"></div>
                </div>


                <!-- Create Button -->
                <button class="register_page_button">Create my account</button>

                <!-- Login redirect -->
                <div class="register_page_redirect">Already have an account? <a href="../login/login.php">Sign in</a>
                </div>


            </div>


        </form>
    </section>

    <!-- Right -->
    <section class='register_page_right_container'>


        <div class="register_page_right_text">
            <h1>Addis Ababa, Ethiopia</h1>
            <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Non vero dolorum
                quas minus, nemo repudiandae! Cumque temporibus voluptatem mollitia repellendus expedita. Quam at in
                consequuntur.</p>
        </div>
    </section>
</body>

<!-- Google Captcha v2 -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

</html>