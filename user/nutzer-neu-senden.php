<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Admin");

  /** Ziel für Alerts */
  $target = RELPATH . "admin/nutzer-neu.php";

  // Nutzername validieren
  // Groß- / Kleinschreibung egal
  // - Gegeben
  // - 4-32 lang
  // - noch nicht verwendet
  $_POST["username"] = strtolower(trim($_POST["username"] ?? ""));
  if(empty($_POST["username"])) {
    // Nicht gegeben oder nur Leerzeichen
    send_alert($target, "warning", "Kein Nutzername gegeben");
  }
  elseif(strlen($_POST["username"]) < 4) {
    // Zu kurz
    send_alert($target, "warning", "Nutzername zu kurz (min 4 Zeichen)");
  }
  elseif(strlen($_POST["username"]) > 32) {
    // Zu lang
    send_alert($target, "warning", "Nutzername zu lang (max 32 Zeichen)");
  }
  else {
    $query = sprintf(
      "SELECT 1 FROM user WHERE LOWER(username) = '%s'",
      mysqli_real_escape_string($db, $_POST["username"])
    );
    $result = mysqli_query($db, $query);

    if(mysqli_num_rows($result) != 0) {
      // Nutzername bereits benutzt
      send_alert($target, "info", "Nutzername bereits vergeben");
    }
    // Nutzername valid
  }

  // Name validieren
  // - nicht gegeben
  // ODER
  // - bis 50 lang
  $_POST["name"] = trim($_POST["name"] ?? "");
  if(empty($_POST["name"])) {
    // Nicht gegeben oder nur Leerzeichen
    unset($_POST["name"]);
  }
  elseif(strlen($_POST["name"]) > 50) {
    // Zu lang
    send_alert($target, "warning", "Anzeigename zu lang (max 50 Zeichen)");
  }
  // Name gegeben und valid

  // Passwort validieren
  // - Gegeben
  // - 8-200 lang
  $_POST["pw"] = trim($_POST["pw"] ?? "");
  if(empty($_POST["pw"])) {
    // Nicht gegeben oder nur Leerzeichen
    send_alert($target, "warning", "Kein Passwort gegeben");
  }
  elseif(strlen($_POST["pw"]) < 4) {
    // Zu kurz
    send_alert($target, "warning", "Passwort zu kurz (min 8 Zeichen)");
  }
  elseif(strlen($_POST["pw"]) > 32) {
    // Zu lang
    send_alert($target, "warning", "Passwort zu lang (max 200 Zeichen)");
  }
  // Passwort valid

  // Salt generieren, Passwort hashen
  $salt = random_str(32);
  $pw_hash = hash_password($salt, $_POST["pw"]);
  // Eintrag in die Datenbank tun
  $query = sprintf(
    "INSERT INTO user(username, name, salt, pw_hash_bin, ist_trainer, ist_admin) VALUE ('%s', %s, '%s', UNHEX('%s'), %d, %d)",
    mysqli_real_escape_string($db, $_POST["username"]),
    (isset($_POST["name"]) ? "'" . mysqli_real_escape_string($db, $_POST["name"]) . "'" : "NULL"), // Anzeigename bzw. NULL
    mysqli_real_escape_string($db, $salt),
    $pw_hash,
    isset($_POST["ist_trainer"]),
    isset($_POST["ist_admin"])
  );
  if(!mysqli_query($db, $query)) {
    // Fehler beim Query
    send_alert($target, "danger", "Fehler: " . mysqli_error($db));
  }
  send_alert($target, "success", "Benutzer hinzugefügt");
?>