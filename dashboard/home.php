<?php

include "../config/db.php";
session_start();


if (!isset($_SESSION['uid'])) {
    header("location:../auth/login/login.php");
}



$userId = $_SESSION['uid'];
$stmt = $connection->prepare("SELECT feedback_id,user_id,title,comment,date,status FROM feedbacks WHERE user_id=?");
$stmt->bind_param('s', $userId);
$stmt->execute();
$result = $stmt->get_result();
$result = $result->fetch_all(MYSQLI_ASSOC);

if (!empty($result)) {
    $userIdd =  $result[0]['user_id'];

    // $stmtt = $connection->prepare("SELECT fullName,email FROM users WHERE id=?");
    // echo $stmtt;
    // if ($stmtt != false) {

    //     $stmtt->bind_param('s', $userIdd);
    //     $stmtt->execute();
    //     $user = $stmtt->get_result();
    //     $user = $result->fetch_array(MYSQLI_ASSOC);
    // }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Nav -->
    <?php include '../inc/navigation.php' ?>

    <!-- Body -->
    <div class="dash_main_container">

        <!-- Left -->
        <div class="dash_main_left_container">

            <h1>Addis Ababa is our home</h1>
            <h1>&</h1>
            <h1>We take it seriously !!!</h1>

            <p>
                Lorem ipsum dolor sit amet consectetur, adipisicing elit. Non vero dolorum quas minus, nemo repudiandae!
                Cumque temporibus voluptatem mollitia repellendus expedita. Quam at in consequuntur.
            </p>

            <p>
                Lorem ipsum dolor sit amet consectetur, adipisicing elit. Non vero dolorum quas minus, nemo repudiandae!
                Cumque temporibus voluptatem mollitia repellendus expedita. Quam at in consequuntur.
            </p>

            <!-- Add Button -->
            <form action="../feedback/feedback_modal.php" method="POST" class="add-feedback-button-container"
                id="open-modal">

                <input type="text" name="fullName" value="" hidden>
                <input type="text" name="email" value="" hidden>
                <input type="text" name="title" value="" hidden>
                <input type="text" name="comment" value="" hidden>
                <input type="text" name="commentId" value="" hidden>
                <input type="text" name="isAdd" value="1" hidden>
                <input type="text" name="isEdit" value="0" hidden>
                <input type="text" name="isSubmit" value="0" hidden>


                <img src="../assets/img/add-button-svgrepo-com.svg" alt="">
                <input type="submit" value="Add New Feedback" class="add-feedback-button">
            </form>
        </div>

        <!-- Right -->
        <div class="dash_main_right_container">

            <?php
            if (empty($result)) { ?>
            <div class="no-comment-yet-container">
                <h1>No Comments yet.</h1>
            </div>

            <?php } else {
                foreach ($result as $complaint) { ?>
            <div class="feedback_card">
                <!-- Status Indicator -->
                <div class="feedback_card_status_indicator"></div>
                <div class="feedback_card_body_container">
                    <!-- Controls -->
                    <div class="feedback_card_controls_container">
                        <!-- edit -->
                        <div class="edit-control" id="edit-control">
                            <img src="../assets//icons/edit-button-svgrepo-com.svg" alt="">
                        </div>

                        <!-- delete -->
                        <form action="../feedback/feedback_modal.php" method="POST">
                            <input type="text" name="isEdit" value="1" hidden>
                            <input type="text" name="isSubmit" value="0" hidden>
                            <input type="text" name="isAdd" value="0" hidden>
                            <input type="text" name="fullName" value="" hidden>
                            <input type="text" name="email" value="" hidden>
                            <input type="text" name="title"
                                value="<?php echo filter_var($complaint['title'], FILTER_SANITIZE_SPECIAL_CHARS) ?>"
                                hidden>
                            <input type="text" name="comment"
                                value="<?php echo filter_var($complaint['comment'], FILTER_SANITIZE_SPECIAL_CHARS) ?>"
                                hidden>
                            <input type="text" name="commentId"
                                value="<?php echo filter_var($complaint['feedback_id'], FILTER_SANITIZE_SPECIAL_CHARS) ?>"
                                hidden>


                            <!-- <div class="delete-control" id="delete-control">

                                <img src="../assets/icons/delete.svg" alt="">
                            </div> -->

                            <!-- feedback id -->
                            <input type="postId"
                                value="<?php echo filter_var($complaint['feedback_id'], FILTER_SANITIZE_SPECIAL_CHARS) ?>"
                                hidden>
                            <input type="function" value="delete">

                            <input type="submit" value="delete">
                        </form>
                    </div>

                    <!-- title -->
                    <div class="feedback_card_title">
                        <?php echo filter_var($complaint['title'], FILTER_SANITIZE_SPECIAL_CHARS)  ?></div>

                    <!-- body -->
                    <div class="feedback_card_body">
                        <?php echo  filter_var($complaint['comment'], FILTER_SANITIZE_SPECIAL_CHARS) ?></div>

                    <!-- more info -->
                    <div class="feedback_card_date">
                        <p>Pdf uploaded</p>
                        <p> <?php echo  filter_var($complaint['date'], FILTER_SANITIZE_SPECIAL_CHARS)  ?>
                        </p>
                    </div>
                </div>

            </div>
            <?php  }
            }
            ?>

        </div>
    </div>
</body>

</html>