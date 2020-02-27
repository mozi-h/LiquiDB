<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Admin");

  // Existiert die Gruppe?
  if(!filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    // id keine Nummer
    send_alert(RELPATH . "admin/gruppe.php", "warning", "ID ist keine Nummer");
  }
  $_GET["id"] = intval($_GET["id"]);
  $query = sprintf(
    "SELECT 1 FROM `group` WHERE id = %d",
    $_GET["id"]
  );
  $result = mysqli_query($db, $query);
  if(mysqli_num_rows($result) != 1) {
    // id keine Gruppe
    send_alert(RELPATH . "admin/gruppe.php", "warning", "ID ist keine Gruppe");
  }
  $group = get_group($_GET["id"]);

  /** Ziel für Alerts */
  $target = RELPATH . "admin/gruppe-bearbeiten.php?id=" . $_GET["id"];

  // Name validieren
  // - Gegeben
  // - 5-50 lang
  $_POST["name"] = trim($_POST["name"] ?? "");
  if(empty($_POST["name"])) {
    // Nicht gegeben oder nur Leerzeichen
    send_alert($target, "warning", "Kein Name gegeben");
  }
  elseif(strlen($_POST["name"]) < 5) {
    // Zu kurz
    send_alert($target, "warning", "Name zu kurz (min 5 Zeichen)");
  }
  elseif(strlen($_POST["name"]) > 50) {
    // Zu lang
    send_alert($target, "warning", "Name zu lang (max 50 Zeichen)");
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
    send_alert($target, "warning", "Beschreibung zu lang (max 500 Zeichen)");
  }
  // Notiz valid

  // Eintrag in die Datenbank tun
  $query = sprintf(
    "UPDATE `group` SET `name` = '%s', `description` = %s WHERE id = %d",
    mysqli_real_escape_string($db, $_POST["name"]),
    mysql_escape_or_null($_POST["description"] ?? NULL),
    $_GET["id"]
  );
  if(!mysqli_query($db, $query)) {
    // Fehler beim Query
    send_alert($target, "danger", "Fehler: " . mysqli_error($db));
  }
  send_alert($target, "success", "Gruppe aktualisiert");
