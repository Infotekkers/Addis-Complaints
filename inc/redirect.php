<?php function Redirect($url, $statusCode=303){
    $urlPattern = "#^http://localhost:3000/#";

    if (!preg_match($urlPattern,$url)){
     showNotification("erroneous url!!");
     die("erroneous url!!");
    }else{
    header('Location: ' .$url, true, $statusCode);
    die();
    }
    
} 
?>