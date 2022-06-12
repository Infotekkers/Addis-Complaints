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
    include '../../inc/notification.php';
}

function activateUser($connection)
{
    try {
        echo "Activating User";
        $userId = filter_var($_POST['userId'], FILTER_SANITIZE_SPECIAL_CHARS);
        $stmt = $connection->prepare("UPDATE users SET attemptCount = 0 WHERE id=?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        header("location:./moderator_home.php");
    } catch (Exception $e) {
        showNotification("Something Went Wrong");
    }
}

activateUser($connection);