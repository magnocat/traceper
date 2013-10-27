<?php

session_start();

if(isset($_POST['width']) && isset($_POST['height'])) 
{
    $_SESSION['screen_width'] = $_POST['width'];
    $_SESSION['screen_height'] = $_POST['height'];

    echo json_encode(array('outcome'=>'success'));
} 
else 
{
    echo json_encode(array('outcome'=>'error','error'=>"Couldn't save dimension info"));
}

?>