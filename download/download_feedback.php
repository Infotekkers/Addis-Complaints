<?php

include "../config/db/user.php";
include "../inc/redirect.php";
session_start();
session_regenerate_id();
$base_url = "http://localhost:3000";


if (!isset($_SESSION['uid'])) {
    Redirect("$base_url/auth/login/login.php");
    exit;
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
                // if sus name
                if (str_contains($_SESSION['filePath'], "..") || str_contains($_SESSION['filePath'], "../") || str_contains($_SESSION['filePath'], "./")) {
                    showNotification("Malicious Attempt!");
                    session_destroy();
                    header("location:../auth/login/login.php");
                    exit;
                } else {

                    $file = "../uploads/" . $_SESSION['filePath'];
                    header('Content-Type: application/octet-stream');
                    header("Content-Transfer-Encoding: Binary");
                    header("Content-disposition: attachment; filename=\"" . basename($file) . "\"");
                    ob_clean();
                    flush();
                    readfile($file);
                    exit;
                }
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