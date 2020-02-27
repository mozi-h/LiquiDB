<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Admin");

  /** Ziel für Alerts */
  $target = RELPATH . "admin/gruppe-neu.php";

  // Name validieren
  // - Gegeben
  // - 5-50 lang
  $_POST["name"] = trim($_POST["name"] ?? "");
  if(empty($_POST["name"])) {
    // Nicht gegeben oder nur Leerzeichen
    send_alert($target, "warning", "Kein Name gegeben", False, $_POST);
  }
  elseif(strlen($_POST["name"]) < 5) {
    // Zu kurz
    send_alert($target, "warning", "Name zu kurz (min 5 Zeichen)", False, $_POST);
  }
  elseif(strlen($_POST["name"]) > 50) {
    // Zu lang
    send_alert($target, "warning", "Name zu lang (max 50 Zeichen)", False, $_POST);
  }
  // Name valid

  // Beschreibung validieren
  // Nicht gegeben
  // ODER
  // bis 500 lang
  $_POST["description"] = trim($_POST["description"] ?? "");
  if(empty($_POST["description"])) {
    // Nicht gegeben oder nur Leerzeichen
    unset($_POST["description"]);
  }
  elseif(strlen($_POST["description"]) > 500) {
    // Zu lang
    send_alert($target, "warning", "Beschreibung zu lang (max 500 Zeichen)", False, $_POST);
  }
  // Notiz valid

  // Eintrag in die Datenbank tun
  $query = sprintf(
    "INSERT INTO `group`(name, description) VALUE ('%s', %s)",
    mysqli_real_escape_string($db, $_POST["name"]),
    mysql_escape_or_null($_POST["description"] ?? NULL)
  );
  if(!mysqli_query($db, $query)) {
    // Fehler beim Query
    send_alert($target, "danger", "Fehler: " . mysqli_error($db), False, $_POST);
  }
  send_alert($target, "success", "Gruppe hinzugefügt");
?>