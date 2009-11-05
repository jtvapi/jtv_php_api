<?php session_start(); ?>
<html>
  <?php
    include('jtv_client.inc.php');
  ?>
  <head>
    <title>jtv test</title>
  </head>
  <body>
    <?php
        if (JtvClient::is_authorized()) {
            $user_info = JtvClient::get('/account/whoami');
    ?>
      Hello, <?php print($user_info['login']); ?>!<br/>
      <img src="<?php print($user_info['image_url_medium']); ?>"/>
    <?php
        } else {
    ?>
      <a href="/login.php">Log in</a>
    <?php
        }
    ?>
  </body>
</html>
