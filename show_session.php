<?php
  require_once("config.php");
  var_dump($_SESSION);

  if(isset($_SESSION["USER"])) {
    $user = get_user($_SESSION["USER"]);
    var_dump($user);
  }
?>