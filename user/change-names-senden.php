<?php
  $relative_offset = "../";
  require_once($relative_offset . "config.php");

  // Normalerweise restricted(); außnahme, da Sonderseite
  if(!isset($_SESSION["USER"])) {
    send_alert("../index.php", "info", "Sie müssen sich zuerst anmelden");
  }
  $user = get_user($_SESSION["USER"]);

  // Nutzername validieren, WENN
  // - Nutzername geändert
  // Groß- / Kleinschreibung egal
  // - Gegeben
  // - 4-32 lang
  // - noch nicht verwendet
  $skip_username = False;
  $_POST["username"] = strtolower(trim($_POST["username"] ?? ""));
  if($_POST["username"] == strtolower($user["username"])) {
    // Nutzername nicht geändert
    $skip_username = True;
    unset($_POST["username"]);
  }
  elseif(empty($_POST["username"])) {
    // Nicht gegeben oder nur Leerzeichen
    send_alert($relative_offset . "user/change-data.php", "warning", "Kein Nutzername gegeben");
  }
  elseif(strlen($_POST["username"]) < 4) {
    // Zu kurz
    send_alert($relative_offset . "user/change-data.php", "warning", "Nutzername zu kurz (min 4 Zeichen)");
  }
  elseif(strlen($_POST["username"]) > 32) {
    // Zu lang
    send_alert($relative_offset . "user/change-data.php", "warning", "Nutzername zu lang (max 32 Zeichen)");
  }
  else {
    $query = sprintf(
      "SELECT 1 FROM user WHERE LOWER(username) = '%s'",
      mysqli_real_escape_string($db, $_POST["username"])
    );
    $result = mysqli_query($db, $query);

    if(mysqli_num_rows($result) != 0) {
      // Nutzername bereits benutzt
      send_alert($relative_offset . "user/change-data.php", "info", "Nutzername bereits vergeben");
    }
    // Nutzername valid
  }

  // Name validieren, WENN
  // - Name geändert
  // - nicht gegeben
  // ODER
  // - bis 50 lang
  $skip_name = False;
  $_POST["name"] = trim($_POST["name"] ?? "");
  if($_POST["name"] == ($user["name"] ?? "")) {
    // Name nicht geändert
    $skip_name = True;
    unset($_POST["name"]);
  }
  elseif(empty($_POST["name"])) {
    // Nicht gegeben oder nur Leerzeichen
    unset($_POST["name"]);
  }
  elseif(strlen($_POST["name"]) > 50) {
    // Zu lang
    send_alert($relative_offset . "user/change-data.php", "warning", "Anzeigename zu lang (max 50 Zeichen)");
  }
  // Name gegeben und valid
  
  // Eintrag in der Datenbank aktualisieren, wenn etwas geändert wurde
  if($skip_username & $skip_name) {
    // Nichts geändert
    send_alert($relative_offset . "user/change-data.php", "warning", "Nichts geändert");
  }
  if(!$skip_username) {
    // Nutzername aktualisieren
    $query = sprintf(
      "UPDATE user SET username = '%s' WHERE id = %d",
      mysqli_real_escape_string($db, $_POST["username"]),
      $user["id"]
    );
    if(!mysqli_query($db, $query)) {
      // Fehler beim Query
      send_alert($relative_offset . "user/change-data.php", "danger", "Fehler: " . mysqli_error($db));
    }
  }
  if(!$skip_name) {
    // Anzeigenamen aktualisieren
    $query = sprintf(
      "UPDATE user SET name = %s WHERE id = %d",
      (isset($_POST["name"]) ? "'" . mysqli_real_escape_string($db, $_POST["name"]) . "'" : "NULL"), // Anzeigename bzw. NULL
      $user["id"]
    );
    if(!mysqli_query($db, $query)) {
      // Fehler beim Query
      send_alert($relative_offset . "user/change-data.php", "danger", "Fehler: " . mysqli_error($db));
    }
  }
  if(!$skip_username & $skip_name) {
    send_alert($relative_offset . "user/change-data.php", "success", "Nutzername aktualisiert");
  }
  if($skip_username & !$skip_name) {
    send_alert($relative_offset . "user/change-data.php", "success", "Anzeigename aktualisiert");
  }
  send_alert($relative_offset . "user/change-data.php", "success", "Nutzer- und Anzeigename aktualisiert");
?>