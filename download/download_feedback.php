<?php

include_once "../config/db.php";
session_start();
session_regenerate_id();

if (!isset($_SESSION['uid'])) {
    header("location:../auth/login/login.php");
}

$show_notification_message = false;
$notification_message_content = "";

function showNotification($notificationMessage)
{
    $show_notification_message = true;
    $notification_message_content = $notificationMessage;
    include '../inc/notification.php';
}


function downloadFile($connection)
{
    try {
        $stmt = $connection->prepare("SELECT user_id,filePath FROM feedbacks WHERE feedback_id=?");
        $stmt->bind_param('i', $_SESSION['commentId']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $result = $result->fetch_all(MYSQLI_ASSOC)[0];

            if ($result['user_id'] == $_SESSION['uid'] && $result['filePath'] == $_SESSION['filePath']) {
                $file = "../uploads/" . $_SESSION['filePath'];
                header('Content-Type: application/octet-stream');
                header("Content-Transfer-Encoding: Binary");
                header("Content-disposition: attachment; filename=\"" . basename($file) . "\"");
                ob_clean();
                flush();
                readfile($file);
                exit;
            } else {
                showNotification("Something Went Wrong");
            }
        } else {
            showNotification("Something Went Wrong");
        };
    } catch (Exception $e) {
        showNotification("Something Went Wrong");
    }
}
downloadFile($connection);