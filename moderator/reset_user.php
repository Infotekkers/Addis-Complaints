<?php


include "../config/db.php";
session_start();
session_regenerate_id();


if (!isset($_SESSION['uid'])) {
    header("location:../auth/login/login.php");
}

$show_notification_message = false;
$notification_message_content = "";

$Infostmt = $connection->prepare("SELECT id,full_name,attemptCount,role FROM admin WHERE id=?");
$Infostmt->bind_param('i', $userId);
$Infostmt->execute();
$adminResult = $Infostmt->get_result();
$adminResult = $adminResult->fetch_array(MYSQLI_ASSOC);

if (!password_verify($adminResult['id'] . $adminResult['role'], $_SESSION['sessionHash'])) {
    header("location:../dashboard/home.php");
}

$feedBackstmt = $connection->prepare("SELECT feedbacks.feedback_id,feedbacks.title, feedbacks.comment, feedbacks.date, feedbacks.status,feedbacks.user_id,users.full_name,users.email  FROM feedbacks INNER JOIN users ON feedbacks.user_id=users.id");
$feedBackstmt->execute();
$feedbackResult = $feedBackstmt->get_result();
$feedbackResult = $feedbackResult->fetch_all(MYSQLI_ASSOC);

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
