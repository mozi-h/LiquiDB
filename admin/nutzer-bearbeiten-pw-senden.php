<?php
  $relative_offset = "../";
  require_once($relative_offset . "config.php");

  restricted("Admin");

  // Existiert der Nutzer?
  if(!filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    // id keine Nummer
    send_alert($relative_offset . "admin/nutzer.php", "warning", "ID ist keine Nummer");
  }
  $_GET["id"] = intval($_GET["id"]);
  $query = sprintf(
    "SELECT 1 FROM user WHERE id = %d",
    $_GET["id"]
  );
  $result = mysqli_query($db, $query);
  if(mysqli_num_rows($result) != 1) {
    // id kein Nutzer
    send_alert($relative_offset . "admin/nutzer.php", "warning", "ID ist kein Nutzer");
  }
  $user = get_user($_GET["id"]);

  // Passwort validieren
  // - Gegeben
  // - 8-200 lang
  $_POST["pw"] = trim($_POST["pw"] ?? "");
  if(empty($_POST["pw"])) {
    // Nicht gegeben oder nur Leerzeichen
    send_alert($relative_offset . "admin/nutzer-bearbeiten.php?id=" . $user["id"], "warning", "Kein Passwort gegeben");
  }
  elseif(strlen($_POST["pw"]) < 4) {
    // Zu kurz
    send_alert($relative_offset . "admin/nutzer-bearbeiten.php?id=" . $user["id"], "warning", "Passwort zu kurz (min 8 Zeichen)");
  }
  elseif(strlen($_POST["pw"]) > 32) {
    // Zu lang
    send_alert($relative_offset . "admin/nutzer-bearbeiten.php?id=" . $user["id"], "warning", "Passwort zu lang (max 200 Zeichen)");
  }
  // Passwort valid

  // Passwort und pw_changed zurücksetzen
  $salt = random_str(32);
  $mixed_pw_neu = substr($salt, 0, 16) . $_POST["pw"] . substr($salt, 16);
  $pw_hash = strtoupper(hash("sha256", $mixed_pw_neu));

  $query = sprintf(
    "UPDATE user SET salt = '%s', pw_hash_bin = UNHEX('%s'), pw_changed = NOW() WHERE id = %d",
    mysqli_real_escape_string($db, $salt),
    $pw_hash,
    $user["id"]
  );
  if(!mysqli_query($db, $query)) {
    // fehler beim Query
    send_alert($relative_offset . "admin/nutzer-bearbeiten.php?id=" . $user["id"], "danger", "Fehler: " . mysqli_error($db));
  }
  $_SESSION["USER_LOGINTIME"] = time() + 5;
  send_alert($relative_offset . "admin/nutzer-bearbeiten.php?id=" . $user["id"], "success", "Passwort geändert");
?>