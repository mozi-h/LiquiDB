<?php
  require_once("../config.php");

  if(!isset($_SESSION["USER"])) {
    send_alert("../index.php", "info", "Sie sind bereits abgemeldet");
  }

  unset($_SESSION["USER"]);
  unset($_SESSION["USER_LOGINTIME"]);

  send_alert("../index.php", "success", "Abgemeldet");
?>