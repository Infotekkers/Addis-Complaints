<?php

include "../config/db.php";
session_start();


if (!isset($_SESSION['uid'])) {
    header("location:../auth/login/login.php");
}




$userId = $_SESSION['uid'];
$stmt = $connection->prepare("SELECT feedbacks.feedback_id,feedbacks.title, feedbacks.comment, feedbacks.date, feedbacks.status,feedbacks.user_id,users.full_name,users.email  FROM feedbacks INNER JOIN users ON feedbacks.user_id=users.id  WHERE user_id =?");
$stmt->bind_param('s', $userId);
$stmt->execute();
$result = $stmt->get_result();
$result = $result->fetch_all(MYSQLI_ASSOC);

if (!empty($result)) {
    $userIdd =  $result[0]['user_id'];
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
            <form action="../feedback/feedback_modal_add.php" method="POST" class="add-feedback-button-container"
                id="open-modal">

                <input type="text" name="full_name" value="" hidden>
                <input type="text" name="email" value="" hidden>
                <input type="text" name="title" value="" hidden>
                <input type="text" name="comment" value="" hidden>
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
                        <form action="../feedback/feedback_modal_edit.php" method="GET">

                            <input type="text" name="title"
                                value="<?php echo filter_var($complaint['title'], FILTER_SANITIZE_SPECIAL_CHARS) ?>"
                                hidden>
                            <input type="text" name="comment"
                                value="<?php echo filter_var($complaint['comment'], FILTER_SANITIZE_SPECIAL_CHARS) ?>"
                                hidden>
                            <input type="text" name="commentId"
                                value="<?php echo filter_var($complaint['feedback_id'], FILTER_SANITIZE_SPECIAL_CHARS) ?>"
                                hidden>

                            <input type="text" name="full_name"
                                value="<?php echo filter_var($complaint['full_name'], FILTER_SANITIZE_SPECIAL_CHARS) ?>"
                                hidden>
                            <input type="text" name="email"
                                value="<?php echo filter_var($complaint['email'], FILTER_SANITIZE_EMAIL) ?>" hidden>

                            <input type="submit" value="Edit">
                        </form>
                    </div>

                    <!-- title -->
                    <div class="feedback_card_title">
                        <?php echo filter_var($complaint['title'], FILTER_SANITIZE_SPECIAL_CHARS)  ?></div>

                    <!-- body -->
                    <div class="feedback_card_body">
                        <?php echo  filter_var($complaint['comment'], FILTER_SANITIZE_SPECIAL_CHARS) ?>

                    </div>

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