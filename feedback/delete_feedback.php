<?php

include_once "../config/db.php";
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
    include '../inc/notification.php';
}


function deleteComment($connection)
{

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
        } else {
            showNotification("Malicious Attempt!");
        }
    } else {
        showNotification("Something Went Wrong");
    }

    // redirect to home
    header("location:../dashboard/home.php");
    // check uid

    // delete

    // redirect
}



try {
    deleteComment($connection);
} catch (Exception $e) {
    $x = 1;
};