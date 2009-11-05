<?php
    session_start();
    include('jtv_client.inc.php');
    JtvClient::start_user_authorization('http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/login_leg3.php');
?>
