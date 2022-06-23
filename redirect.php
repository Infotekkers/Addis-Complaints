<?php function Redirect($url, $statusCode=303){
    $redirect_url = parse_url($url);
    $urlPattern = "#^http://localhost:3000/#";

    if (!preg_match($urlPattern,$url)){
     showNotification($url);
    die();

    }else{
    header('Location: ' .$url, true, $statusCode);
    }
} 
?>