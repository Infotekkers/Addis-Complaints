<?php

include "../config/db/admin.php";
$base_url = "http://localhost:3000";
include "../inc/redirect.php";

session_start();
session_regenerate_id();

function showNotification($notificationMessage)
{
    $show_notification_message = true;
    $notification_message_content = $notificationMessage;
    include '../inc/notification.php';
}

if (!isset($_SESSION['uid'])) {
    Redirect("$base_url/auth/login/login.php");
    exit("Unauthenticated");
}

$userId = $_SESSION['uid'];


$userStmt = $connection->prepare("SELECT id,full_name,email,isActive,attemptCount from users");
$userStmt->execute();
$userResult = $userStmt->get_result();
$userResult = $userResult->fetch_all(MYSQLI_ASSOC);

$Infostmt = $connection->prepare("SELECT id,full_name,attemptCount,role FROM admin WHERE id=?");
$Infostmt->bind_param('i', $userId);
$Infostmt->execute();
$adminResult = $Infostmt->get_result();
$adminResult = $adminResult->fetch_array(MYSQLI_ASSOC);

if (!password_verify($adminResult['id'] . $adminResult['role'], $_SESSION['sessionHash'])) {
    Redirect("$base_url/dashboard/home.php");
    exit("Unauthorized!");
}

$feedBackstmt = $connection->prepare("SELECT feedbacks.feedback_id,feedbacks.title, feedbacks.comment, feedbacks.date, feedbacks.status,feedbacks.user_id,feedbacks.filePath,users.full_name,users.email  FROM feedbacks INNER JOIN users ON feedbacks.user_id=users.id");
$feedBackstmt->execute();
$feedbackResult = $feedBackstmt->get_result();
$feedbackResult = $feedbackResult->fetch_all(MYSQLI_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderator</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Nav -->
    <?php include '../inc/navigation.php' ?>

    <!-- Body -->
    <div class="admin_dash_container">

        <!-- User List -->
        <div class="admin_dash_userlist_container">

            <?php
            if (empty($userResult)) { ?>
            <div class="no-data-center-container">
                <h1 class="no-data-text-header">No Users Yet.</h1>
            </div>

            <?php } else {
                foreach ($userResult as $user) { ?>
            <div class="user_card">
                <div class="user_card_info">

                    <!-- Name & Email -->
                    <div class="user_card_text_info">
                        <p><?php echo $user['full_name'] ?></p>

                        <p><?php echo $user['email'] ?></p>


                    </div>

                    <!-- Account Status -->
                    <div class="user_card_account_info">
                        <?php echo $user['isActive'] ?>
                        <?php echo $user['attemptCount'] ?>
                    </div>
                </div>

                <div class="user_card_controls">
                    <form action="./reset_user.php" method="POST" autocomplete="off">
                        <input type="text" name="userId" value="<?php echo $user['id'] ?>" hidden>
                        <input type="number" value="1" name="status" hidden>
                        <input type="submit" value="Activate">
                    </form>
                    <form action="./reset_user.php" method="POST" autocomplete="off">
                        <input type="text" name="userId" value="<?php echo $user['id'] ?>" hidden>
                        <input type="number" value="0" name="status" hidden>
                        <input type="submit" value="Deactivate">
                    </form>
                    <form action="./reset_user.php" method="POST" autocomplete="off">
                        <input type="text" name="userId" value="<?php echo $user['id'] ?>" hidden>
                        <input type="submit" value="Reset">
                    </form>
                </div>
            </div>

            <?php }
            }
            ?>


        </div>


        <!-- Feedback List  -->
        <div class="admin_dash_feedback_container">


            <?php
            if (empty($feedbackResult)) {
            ?>

            <div class="no-data-center-container">
                <h1 class="no-data-text-header">No Feedbacks Yet.</h1>
            </div>
            <?php } else {

                foreach ($feedbackResult as $feedback) {  ?>

            <div class="feedback_card">
                <!-- Status Indicator -->
                <?php
                        if ($feedback['filePath'] == '') { ?>
                <div class="feedback_card_status_indicator red-indicator">
                    ❌
                </div>
                <?php } else { ?>
                <div class="feedback_card_status_indicator green-indicator">
                    ✅
                </div>
                <?php   }
                        ?>
                <div class="feedback_card_body_container">
                    <!-- Controls -->
                    <div class="feedback_card_controls_container">

                    </div>

                    <!-- title -->
                    <div class="feedback_card_title">
                        <?php echo $feedback['title'] ?></div>

                    <!-- body -->
                    <div class="feedback_card_body">
                        <?php echo $feedback['comment'] ?>
                    </div>

                    <!-- user Info -->
                    <div class="feedback_card_date">
                        <p><?php echo $feedback['full_name'] ?></p>
                        <p> <?php echo $feedback['email'] ?></p>
                    </div>

                    <!-- more info -->
                    <div class="feedback_card_date">
                        <p>Pdf uploaded</p>
                        <p> <?php echo $feedback['date'] ?></p>
                    </div>
                </div>

            </div>

            <?php }
            }
            ?>
        </div>


    </div>
</body>

</html>