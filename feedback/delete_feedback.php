<?php

include "../config/db/user.php";
include "../inc/redirect.php";
$base_url = "http://localhost:3000";

session_start();
session_regenerate_id();

if (!isset($_SESSION['uid'])) {
    Redirect("$base_url/auth/login/login.php");
    exit("Unauthenticated!");
}

$show_notification_message = false;
$notification_message_content = "";




function showNotification($notificationMessage)
{
    $show_notification_message = true;
    $notification_message_content = $notificationMessage;
    include '../inc/notification.php';
}


function deleteComment($connection)
{
    global $base_url;
    try {
        // check token
        $token = filter_input(INPUT_POST, 'antiCSRFToken', FILTER_SANITIZE_SPECIAL_CHARS);

        if (!$token || $token !== $_SESSION['antiCSRFToken']) {
            showNotification("Malicious Attempt!");
            session_destroy();
            Redirect("$base_url/auth/login/login.php");
            exit("Wasted!");
        } else {
            // get comment
            $stmt = $connection->prepare("SELECT user_id FROM feedbacks WHERE feedback_id=?");
            $stmt->bind_param('i', $_POST['commentId']);
            $stmt->execute();
            $result = $stmt->get_result();


            // if email already exists
            if ($result->num_rows == 1) {
                $result = $result->fetch_all(MYSQLI_ASSOC);


                if ($result[0]['user_id'] === $_SESSION['uid']) {
                    $deleteStmt = $connection->prepare("DELETE FROM feedbacks WHERE feedback_id=?");
                    $deleteStmt->bind_param('i', $_POST['commentId']);
                    $deleteStmt->execute();
                    $result = $deleteStmt->get_result();

                    // redirect to home
                    Redirect("$base_url/dashboard/home.php");
                } else {
                    Redirect("$base_url/auth/login/login.php");
                    showNotification("Malicious Attempt!");
                    exit("Wasted!");
                }
            } else {
                showNotification("Something Went Wrong");
            };
        }
    } catch (Exception $e) {
        showNotification("Something Went Wrong");
    }
}



try {
    deleteComment($connection);
} catch (Exception $e) {
    $x = 1;
};