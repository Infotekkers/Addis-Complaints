<?php
session_start();

if (!isset($_SESSION['uid'])) {
    header("location:../auth/login/login.php");
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
            <div class="add-feedback-button-container">
                <img src="../assets/img/add-button-svgrepo-com.svg" alt="">
                <input type="submit" value="Add New Feedback" class="add-feedback-button">
            </div>
        </div>

        <!-- Right -->
        <div class="dash_main_right_container">
            <div class="feedback_card">
                <div class="feedback_card_pdf_indicator"></div>
                <div class="feedback_card_body_container">
                    <div class="feedback_card_title">Title Goes Here</div>
                    <div class="feedback_card_body">Lorem ipsum dolor sit amet consectetur adipisicing elit.
                        Accusantium fugit, atque nam beatae consequuntur delectus repudiandae architecto recusandae
                        natus porro ipsam. Corporis, voluptatibus. Enim quasi quod quos dicta unde incidunt? Lorem ipsum
                        dolor sit, amet consectetur adipisicing elit. A corporis praesentium cum expedita quibusdam
                        voluptatem eum, perferendis impedit. Vel sint, temporibus eligendi consequatur repudiandae
                        consectetur! Ex explicabo et provident architecto! Lorem ipsum dolor sit amet consectetur
                        adipisicing elit. Tempore nemo, sed est magni voluptatum reiciendis quaerat alias fugit, quia ex
                        officia quis recusandae iste, ad quasi aliquid totam harum quod?</div>
                </div>
            </div>


            <div class="feedback_card">
                <div class="feedback_card_pdf_indicator"></div>
                <div class="feedback_card_body_container">
                    <div class="feedback_card_title">Title Goes Here</div>
                    <div class="feedback_card_body">Lorem ipsum dolor sit amet consectetur adipisicing elit.
                        Accusantium fugit, atque nam beatae consequuntur delectus repudiandae architecto recusandae
                        natus porro ipsam. Corporis, voluptatibus. Enim quasi quod quos dicta unde incidunt? Lorem ipsum
                        dolor sit, amet consectetur adipisicing elit. A corporis praesentium cum expedita quibusdam
                        voluptatem eum, perferendis impedit. Vel sint, temporibus eligendi consequatur repudiandae
                        consectetur! Ex explicabo et provident architecto! Lorem ipsum dolor sit amet consectetur
                        adipisicing elit. Tempore nemo, sed est magni voluptatum reiciendis quaerat alias fugit, quia ex
                        officia quis recusandae iste, ad quasi aliquid totam harum quod?</div>
                </div>
            </div>

            <div class="feedback_card">
                <div class="feedback_card_pdf_indicator"></div>
                <div class="feedback_card_body_container">
                    <div class="feedback_card_title">Title Goes Here</div>
                    <div class="feedback_card_body">Lorem ipsum dolor sit amet consectetur adipisicing elit.
                        Accusantium fugit, atque nam beatae consequuntur delectus repudiandae architecto recusandae
                        natus porro ipsam. Corporis, voluptatibus. Enim quasi quod quos dicta unde incidunt? Lorem ipsum
                        dolor sit, amet consectetur adipisicing elit. A corporis praesentium cum expedita quibusdam
                        voluptatem eum, perferendis impedit. Vel sint, temporibus eligendi consequatur repudiandae
                        consectetur! Ex explicabo et provident architecto! Lorem ipsum dolor sit amet consectetur
                        adipisicing elit. Tempore nemo, sed est magni voluptatum reiciendis quaerat alias fugit, quia ex
                        officia quis recusandae iste, ad quasi aliquid totam harum quod?</div>
                </div>
            </div>

            <div class="feedback_card">
                <div class="feedback_card_pdf_indicator"></div>
                <div class="feedback_card_body_container">
                    <div class="feedback_card_title">Title Goes Here</div>
                    <div class="feedback_card_body">Lorem ipsum dolor sit amet consectetur adipisicing elit.
                        Accusantium fugit, atque nam beatae consequuntur delectus repudiandae architecto recusandae
                        natus porro ipsam. Corporis, voluptatibus. Enim quasi quod quos dicta unde incidunt? Lorem ipsum
                        dolor sit, amet consectetur adipisicing elit. A corporis praesentium cum expedita quibusdam
                        voluptatem eum, perferendis impedit. Vel sint, temporibus eligendi consequatur repudiandae
                        consectetur! Ex explicabo et provident architecto! Lorem ipsum dolor sit amet consectetur
                        adipisicing elit. Tempore nemo, sed est magni voluptatum reiciendis quaerat alias fugit, quia ex
                        officia quis recusandae iste, ad quasi aliquid totam harum quod?</div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>