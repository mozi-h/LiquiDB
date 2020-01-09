<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Admin");

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
    send_alert(RELPATH . "admin/nutzer-bearbeiten.php?id=" . $user["id"], "warning", "Kein Nutzername gegeben");
  }
  elseif(strlen($_POST["username"]) < 4) {
    // Zu kurz
    send_alert(RELPATH . "admin/nutzer-bearbeiten.php?id=" . $user["id"], "warning", "Nutzername zu kurz (min 4 Zeichen)");
  }
  elseif(strlen($_POST["username"]) > 32) {
    // Zu lang
    send_alert(RELPATH . "admin/nutzer-bearbeiten.php?id=" . $user["id"], "warning", "Nutzername zu lang (max 32 Zeichen)");
  }
  else {
    $query = sprintf(
      "SELECT 1 FROM user WHERE LOWER(username) = '%s'",
      mysqli_real_escape_string($db, $_POST["username"])
    );
    $result = mysqli_query($db, $query);

    if(mysqli_num_rows($result) != 0) {
      // Nutzername bereits benutzt
      send_alert(RELPATH . "admin/nutzer-bearbeiten.php?id=" . $user["id"], "info", "Nutzername bereits vergeben");
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
    send_alert(RELPATH . "admin/nutzer-bearbeiten.php?id=" . $user["id"], "warning", "Anzeigename zu lang (max 50 Zeichen)");
  }
  // Name gegeben und valid
  
  // Eintrag in der Datenbank aktualisieren, wenn etwas geändert wurde
  if($skip_username & $skip_name) {
    // Nichts geändert
    send_alert(RELPATH . "admin/nutzer-bearbeiten.php?id=" . $user["id"], "warning", "Nichts geändert");
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
      send_alert(RELPATH . "admin/nutzer-bearbeiten.php?id=" . $user["id"], "danger", "Fehler: " . mysqli_error($db));
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
      send_alert(RELPATH . "admin/nutzer-bearbeiten.php?id=" . $user["id"], "danger", "Fehler: " . mysqli_error($db));
    }
  }
  if(!$skip_username & $skip_name) {
    send_alert(RELPATH . "admin/nutzer-bearbeiten.php?id=" . $user["id"], "success", "Nutzername aktualisiert");
  }
  if($skip_username & !$skip_name) {
    send_alert(RELPATH . "admin/nutzer-bearbeiten.php?id=" . $user["id"], "success", "Anzeigename aktualisiert");
  }
  send_alert(RELPATH . "admin/nutzer-bearbeiten.php?id=" . $user["id"], "success", "Nutzer- und Anzeigename aktualisiert");
?>