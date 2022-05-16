<?php

include "../../config/db.php";

$show_notification_message = false;
$notification_message_content = "";


function showNotification($notificationMessage){
    $show_notification_message = true;
    $notification_message_content = $notificationMessage;
    include '../../inc/notification.php';
}

function registerUser($connection) {
    $fullNameInput = filter_var( $_POST['fullName'], FILTER_SANITIZE_STRING);
    $fullNamePattern ="/^[a-zA-Z]+(([',. -][a-zA-Z ])?[a-zA-Z]*)*$/";

    echo $fullNameInput;
    
    $emailInput = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
    $emailPattern = "/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/";
    
    $passwordInput = filter_var($_POST['password'],FILTER_SANITIZE_EMAIL);
    $passwordPattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/";
    $confirmPasswordInput = $_POST['confirmPassword'];

    
    // check full name
    if(!preg_match($fullNamePattern,$fullNameInput) || strlen($fullNameInput) > 24){
        showNotification("Invalid Name");
    }
    
    // check email
    if(!preg_match($emailPattern,$emailInput)){
        showNotification("Invalid Email");
    }
    
    // check password
    if(!preg_match($passwordPattern,$passwordInput) || strlen($passwordInput) > 24){
        showNotification("Invalid Password");
    }
    
    // check password match
    if($confirmPasswordInput != $passwordInput){
        showNotification("Passwords do not match");
    }
    
    else if(preg_match($fullNamePattern,$fullNameInput)){
  
        $stmt = $connection->prepare("SELECT email FROM users WHERE email=?");
        $stmt->bind_param('s',$emailInput);
        $stmt->execute();
        $result = $stmt->get_result();

        // if email already exists
        if($result->num_rows == 1){
            showNotification("Email already in use");
        }
        // if all good
        else{
            // hash password
            $passwordHash = password_hash($passwordInput, PASSWORD_BCRYPT);

            // create prepared statements
            $stmt = $connection->prepare("INSERT INTO users (id, full_name, email,password,isActive,attemptCount) VALUES ('', ?, ?, ?, 1 , 0)");
            $stmt->bind_param('sss',$fullNameInput,$emailInput,$passwordHash);
            $stmt->execute();
            $result = $stmt->get_result();

            header("location:../login/login.php");
        }
    }   
}

// on post
if ($_POST) {
    registerUser($connection);
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

        <form class="register_page_form_container" method="POST">

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
                    <input type="text" name="password" required>
                </div>

                <!-- Confirm Password -->
                <div class="input_container">
                    <label for="confirm-password">Confirm Password : </label>
                    <input type="text" name="confirmPassword" required>
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

</html>