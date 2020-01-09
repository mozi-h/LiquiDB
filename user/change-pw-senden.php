<?php
  require_once("../config.php");
  set_relpath(1);

  // Normalerweise restricted(); außnahme, da Sonderseite
  if(!isset($_SESSION["USER"])) {
    send_alert("../index.php", "info", "Sie müssen sich zuerst anmelden");
  }

  /** Ziel für Alerts */
  $target = RELPATH . "user/change-data.php";

  // Alle Passwörter validieren
  // - Gegeben
  // - 8-200 Lang
  $passwoerter = [
    "Passwort (alt)" => "pw-alt",
    "Passwort (neu)" => "pw-neu",
    "Passwort (Wiederholung)" => "pw-neu-wdh"
  ];
  foreach($passwoerter as $key => $value)
  $_POST[$value] = trim($_POST[$value] ?? "");
  if(empty($_POST[$value])) {
    // Nicht gegeben oder nur Leerzeichen
    send_alert($target, "warning", "Kein $key gegeben");
  }
  elseif(strlen($_POST[$value]) < 4) {
    // Zu kurz
    send_alert($target, "warning", "$key zu kurz (min 8 Zeichen)");
  }
  elseif(strlen($_POST[$value]) > 32) {
    // Zu lang
    send_alert($target, "warning", "$key zu lang (max 200 Zeichen)");
  }
  // Passwörter valid
  
  // Stimmen pw-neu und pw-neu-wdh überein?
  if($_POST["pw-neu"] !== $_POST["pw-neu-wdh"]) {
    send_alert($target, "warning", "Die Passwörter stimmen nicht überein");
  }

  // Nutzer holen, Passwort kontrollieren
  $user = get_user($_SESSION["USER"]);
  $mixed_pw_alt = substr($user["salt"], 0, 16) . $_POST["pw-alt"] . substr($user["salt"], 16);
  if(strtoupper(hash("sha256", $mixed_pw_alt)) !== $user["pw_hash"]) {
    // Passwort (alt) falsch
    send_alert($target, "danger", "Passwort (alt) falsch");
  }
  else {
    // Passwort (alt) korrekt - neues setzen
    $salt = random_str(32);
    $mixed_pw_neu = substr($salt, 0, 16) . $_POST["pw-neu"] . substr($salt, 16);
    $pw_hash = strtoupper(hash("sha256", $mixed_pw_neu));

    $query = sprintf(
      "UPDATE user SET salt = '%s', pw_hash_bin = UNHEX('%s'), pw_changed = NOW(), pw_must_change = 0 WHERE id = %d",
      mysqli_real_escape_string($db, $salt),
      $pw_hash,
      $user["id"]
    );
    if(!mysqli_query($db, $query)) {
      // fehler beim Query
      send_alert($target, "danger", "Fehler: " . mysqli_error($db));
    }
    $_SESSION["USER_LOGINTIME"] = time() + 5;
    send_alert($target, "success", "Passwort geändert");
  }
?>