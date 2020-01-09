<?php
  require_once("../config.php");
  set_relpath(1);

  /** Ziel für Alerts */
  $target = RELPATH . "index.php";

  if(!isset($_SESSION["USER"])) {
    send_alert($target, "info", "Sie sind bereits abgemeldet");
  }

  unset($_SESSION["USER"]);
  unset($_SESSION["USER_LOGINTIME"]);

  send_alert($target, "success", "Abgemeldet");
?>