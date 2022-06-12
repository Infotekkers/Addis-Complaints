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

        $userId = filter_var($_POST['userId'], FILTER_SANITIZE_SPECIAL_CHARS);
        $status = filter_var($_POST['status'], FILTER_SANITIZE_SPECIAL_CHARS);
        $stmt = $connection->prepare("UPDATE users SET isActive = ? WHERE id=?");
        $stmt->bind_param('ii', $status, $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        header("location:./moderator_home.php");
        // echo $status;
    } catch (Exception $e) {
        showNotification("Something Went Wrong");
    }
}

activateUser($connection);