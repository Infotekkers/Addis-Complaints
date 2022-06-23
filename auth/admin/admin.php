<?php


include "../../config/db.php";

session_start();
session_regenerate_id();

$show_notification_message = false;
$notification_message_content = "";

function showNotification($notificationMessage)
{
    $show_notification_message = true;
    $notification_message_content = $notificationMessage;
    include '../../inc/notification.php';
}

function loginUser($connection)
{

    try {
        $emailInput = filter_var($_POST['email'], FILTER_SANITIZE_SPECIAL_CHARS);
        $passwordInput = filter_var($_POST['password'], FILTER_SANITIZE_EMAIL);
        $session = filter_var($_POST['session'], FILTER_SANITIZE_SPECIAL_CHARS);

        $stmt = $connection->prepare("SELECT password FROM admin WHERE email=?");
        $stmt->bind_param('s', $emailInput);
        $stmt->execute();
        $result = $stmt->get_result();
        $result = $result->fetch_array(MYSQLI_ASSOC);



        if ($session != "123456gotem") {
            showNotification("Malicious Attempt.");
        } else {
            // no account
            if (empty($result)) {
                showNotification("Invalid email/password combination.");
            } else {
                $possiblePassword = $result['password'];
                // account locked
                if (password_verify($passwordInput,  $possiblePassword)) {
                    // if deactivated

                    //   get user info and redirect
                    $stmt = $connection->prepare("SELECT id,full_name FROM admin WHERE email=?");
                    $stmt->bind_param('s', $emailInput);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $result = $result->fetch_array(MYSQLI_ASSOC);

                    $_SESSION['uid'] = $result['id'];
                    $_SESSION['name'] = $result['full_name'];



                    header("location:../../moderator/moderator_home.php");
                    exit;
                } else if ($result['password']) {

                    // increase attempt count
                    $newAttemptCount = $result['attemptCount'] + 1;
                    $remainingAttempts = 5 - $newAttemptCount;
                    $stmt = $connection->prepare("UPDATE users SET attemptCount=? WHERE email=?");
                    $stmt->bind_param('is', $newAttemptCount, $emailInput);
                    $stmt->execute();
                    showNotification("Invalid email/password combination. " . $remainingAttempts . " tries remaining!");
                }
            }
        }
    }

    //catch exception
    catch (Exception $e) {
        showNotification("Something went wrong");
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

            loginUser($connection);
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
    <title>Login</title>
    <link rel="stylesheet" href="style.css">

</head>

<body class='login_page_body'>
    <div id="notification_container"></div>

    <!-- Left -->
    <section class="login_page_left_container">

        <p class="login_page_left_title">Addis Complaints </div>

        <form action="" class="login_page_form_container" method="POST" autocomplete="off">
            <!-- Title -->
            <h1>Log in</h1>


            <!-- Sub Text -->
            <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Sequi quaerat quod maxime ex rerum.
                Consequatur
                sint vero odit suscipit, dicta maiores dolores cum ipsam id?</p>


            <!-- Form Inputs-->
            <div>
                <input type="text" name="session" value="123456gotem" hidden>
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

                <!-- Captcha -->
                <div class="captcha_container">
                    <div class="g-recaptcha" data-sitekey="6LdM_jMgAAAAAC7VXyp8sdSNulMdZa8s68zNDsWE"></div>
                </div>


                <!-- Create Button -->
                <input type="submit" value="Log in" class="login_page_button">
            </div>


        </form>
    </section>

    <!-- Right -->
    <section class='login_page_right_container'>


        <div class="login_page_right_text">
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