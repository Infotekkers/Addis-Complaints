<?php 
session_start();

if(!isset($_SESSION['uid'])){
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

    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <?php include '../inc/navigation.php' ?>
    <h1>Home!!!!! <?php echo $_SESSION['name']?> </h1>
</body>

</html>