<?php
  session_start();
  require_once('oauth.php');
  $auth_code = $_GET['code'];

  $tokens = oAuthService::getTokenFromAuthCode($auth_code, 'http://localhost/outlook/authorize.php');

  if ($tokens['access_token']) {
    $_SESSION['access_token'] = $tokens['access_token'];

    // Redirect back to home page
    header("Location: http://localhost/outlook/home.php");
  }
  else
  {
    echo "<p>ERROR: ".$tokens['error']."</p>";
  }
?>