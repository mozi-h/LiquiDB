<?php
  require_once("../config.php");
  set_relpath(1);

  /** Ziel für Alerts */
  $target = RELPATH . "index.php";

  if(isset($_SESSION["USER"])) {
    send_alert($target, "info", "Sie sind bereits angemeldet");
  }

  // Nutzername validieren
  // Groß- / Kleinschreibung egal
  // - Gegeben
  // - 4-32 Lang
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
  // Nutzername valid

  // Passwort validieren
  // - Gegeben
  // - 8-200 Lang
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

  // Nutzer-Passwort-Bindung korrekt?
  // Nutzer vorhanden?
  $query = sprintf(
    "SELECT id, username, salt, HEX(pw_hash_bin) AS pw_hash FROM user WHERE LOWER(username) = '%s'",
    mysqli_real_escape_string($db, $_POST["username"])
  );
  $result = mysqli_query($db, $query);
  
  if(mysqli_num_rows($result) < 1) {
    // Kein Nutzer vorhanden
    send_alert($target, "danger", "Nutzername oder Passwort falsch");
  }
  elseif(mysqli_num_rows($result) > 1) {
    send_alert($target, "danger", "Kritischer Fehler: Mehrere Nutzer vorhanden - Admin kontaktieren!");
  }
  else {
    // Nutzer gefunden, Passwort kontrollieren
    $user = mysqli_fetch_array($result);
    $mixed_pw = substr($user["salt"], 0, 16) . $_POST["pw"] . substr($user["salt"], 16);
    if(strtoupper(hash("sha256", $mixed_pw)) !== $user["pw_hash"]) {
      // Passwort falsch
      send_alert($target, "danger", "Nutzername oder Passwort falsch");
    }
    else {
      // Passwort korrekt
      $_SESSION["USER"] = intval($user["id"]);
      $_SESSION["USER_LOGINTIME"] = time();
      send_alert($target, "success", "Angemeldet als " . $user["username"]);
    }
    
    
  }
?>