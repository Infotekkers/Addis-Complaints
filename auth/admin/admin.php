<?php


include "../../config/db/admin.php";
include "../../inc/redirect.php";

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
            }

            // if account
            else {
                $possiblePassword = $result['password'];
                // account locked
                if (password_verify($passwordInput,  $possiblePassword)) {
                    // if deactivated
                    echo "Login";

                    //   get user info and redirect
                    $Infostmt = $connection->prepare("SELECT id,full_name,attemptCount,role FROM admin WHERE email=?");
                    $Infostmt->bind_param('s', $emailInput);
                    $Infostmt->execute();
                    $newResult = $Infostmt->get_result();
                    $newResult = $newResult->fetch_array(MYSQLI_ASSOC);
                    $sessionHash = password_hash($newResult['id'] . $newResult['role'], PASSWORD_BCRYPT);

                    $_SESSION['uid'] = $newResult['id'];
                    $_SESSION['name'] = $newResult['full_name'];
                    // showNotification($result['full_name']);
                    $_SESSION['sessionHash'] = $sessionHash;

                    // create prepared statements
                    $updateStmt = $connection->prepare("UPDATE admin SET sessionHash=? WHERE email=?");
                    $updateStmt->bind_param("ss", $sessionHash, $emailInput);
                    $status = $updateStmt->execute();
                    $result = $stmt->get_result();
                    $base_url = "http://localhost:3000";
                    // showNotification($_SESSION['sessionHash']);

                    if ($status === false) {
                        showNotification("Error! Log in again.");
                        exit("Unauthorized!");
                        return;
                    }

                    Redirect("$base_url/moderator/moderator_home.php");
                } else {

                    echo "Minus";
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
            showNotification("Captcha failed");
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
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css">

</head>

<body class='login_page_body'>
    <div id="notification_container"></div>

    <!-- Left -->
    <section class="login_page_left_container">

        <p class="login_page_left_title">Addis Complaints - Admin </div>

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
                <div class="input_container password_input">
                    <label for="password">Password : </label>
                    <input type="password" name="password" required>

                    <svg id="Icon_view_solid" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <g id="Icon_view_solid-2" data-name="Icon_view_solid" transform="translate(2 5)">
                            <path id="Shape" d="M10,14a11.408,11.408,0,0,1-4.3-.793A9.772,9.772,0,0,1,2.7,11.3,9.574,9.574,0,0,1,0,7C.576,4.579,3.408,0,10,0s9.424,4.579,10,7a8.833,8.833,0,0,1-2.708,4.448A9.526,9.526,0,0,1,14.3,13.262,12.058,12.058,0,0,1,10,14ZM10,3a4,4,0,1,0,4,4c0-.08,0-.166-.009-.263a2,2,0,0,1-2.011-.016A2.053,2.053,0,0,1,12,7a2,2,0,1,1-.98-1.722A2.051,2.051,0,0,1,11,5a1.987,1.987,0,0,1,.779-1.584A3.958,3.958,0,0,0,10,3Z" />
                        </g>
                    </svg>
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
<script>
    const password_input = document.querySelector("input[name='password']");
    const showOrHide = document.querySelector("#Icon_view_solid");

    showOrHide.addEventListener("click", handleClick);

    function handleClick() {
        password_input.type = password_input.type === "password" ? "text" : "password";
    }
</script>

</html>