<?php
session_start();
include('jtv_client.inc.php');
if(JtvClient::recieve_user_authorization() == true) {
    header('Location: /');
} else {
    print('Login error.');
}

?>
