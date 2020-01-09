<?php
  require("../config.php");
  set_relpath(1);

  restricted("Admin");

  /** Ziel für Alerts */
  $target = RELPATH . "admin/nutzer-bearbeiten.php?id=";

  // Existiert der Nutzer?
  if(!filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    // id keine Nummer
    send_alert(RELPATH . "admin/nutzer.php", "warning", "ID ist keine Nummer");
  }
  $_GET["id"] = intval($_GET["id"]);
  $query = sprintf(
    "SELECT 1 FROM user WHERE id = %d",
    $_GET["id"]
  );
  $result = mysqli_query($db, $query);
  if(mysqli_num_rows($result) != 1) {
    // id kein Nutzer
    send_alert(RELPATH . "admin/nutzer.php", "warning", "ID ist kein Nutzer");
  }
  $user = get_user($_GET["id"]);

  // Alle Passwörter validieren
  // - Gegeben
  // - 8-200 Lang
  $passwoerter = [
    "Passwort (eigenes)" => "pw-admin",
    "Passwort (neu)" => "pw"
  ];
  foreach($passwoerter as $key => $value)
  $_POST[$value] = trim($_POST[$value] ?? "");
  if(empty($_POST[$value])) {
    // Nicht gegeben oder nur Leerzeichen
    send_alert($target . $user["id"], "warning", "Kein $key gegeben");
  }
  elseif(strlen($_POST[$value]) < 4) {
    // Zu kurz
    send_alert($target . $user["id"], "warning", "$key zu kurz (min 8 Zeichen)");
  }
  elseif(strlen($_POST[$value]) > 32) {
    // Zu lang
    send_alert($target . $user["id"], "warning", "$key zu lang (max 200 Zeichen)");
  }
  // Passwörter valid

  // Admin-Passwort kontrollieren
  $user_admin = get_user($_SESSION["USER"]);
  if(hash_password($user_admin["salt"], $_POST["pw-admin"]) !== $user_admin["pw_hash"]) {
    // Admin-Passwort falsch
    send_alert($target . $user["id"], "danger", "Passwort (eigenes) falsch");
  }

  // Passwort und pw_changed zurücksetzen
  $salt = random_str(32);
  $pw_hash = hash_password($salt, $_POST["pw"]);

  $query = sprintf(
    "UPDATE user SET salt = '%s', pw_hash = '%s', pw_changed = NOW(), pw_must_change = 1 WHERE id = %d",
    mysqli_real_escape_string($db, $salt),
    $pw_hash,
    $user["id"]
  );
  if(!mysqli_query($db, $query)) {
    // fehler beim Query
    send_alert($target . $user["id"], "danger", "Fehler: " . mysqli_error($db));
  }
  $_SESSION["USER_LOGINTIME"] = time() + 5;
  send_alert($target . $user["id"], "success", "Passwort geändert");
?>