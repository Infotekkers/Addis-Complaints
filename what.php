<?php 
session_start();
date_default_timezone_set("Africa/Addis_Ababa");
if (isset($_SESSION['lastrequest'])){
    $last = strtotime($_SESSION['lastrequest']);
    $current = strtotime(date("Y-m-d h:i:s"));
    $sec = abs($last - $current);

    if($sec <= 10){
        $data = 'rate limit exceeded';
        header('Content-Type: application/json');
        die(json_encode($data));
    }  
}
$_SESSION['lastrequest'] = date("Y-m-d h:i:s");
?>

<!DOCTYPE html>
<html>
<body>
    <?php
    $data = "data returned";
    header('Content-Type: application/json');
    die(json_encode($data));
    ?>
</body>
</html>