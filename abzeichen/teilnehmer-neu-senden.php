<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Trainer");

  //var_dump($_POST);
  //die();

  /** Ziel für Alerts */
  $target = RELPATH . "abzeichen/teilnehmer-neu.php";

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
  // Nutzername valid

  // Geburtsdatum validieren
  // - nicht gegeben
  // ODER
  // - echtes Datum
  // - 2 - 100 Jahre alt
  if(empty($_POST["birthday"] ?? "")) {
    // Nicht gegeben oder nur Leerzeichen
    unset($_POST["birthday"]);
  }
  elseif(!preg_match("/^\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$/", $_POST["birthday"])) {
    // kein YYYY-MM-DD Datum
    send_alert($target, "warning", "Kein YYYY-MM-DD Datum");
  }
  else {
    $age = get_age($_POST["birthday"]);

    if($age < 2) {
      // Zu jung
      send_alert($target, "warning", "Zu jung (min 2 Jahre alt)", False, $_POST);
    }
    elseif($age > 100) {
      // Zu alt
      send_alert($target, "warning", "Zu alt (max 100 Jahre alt)", False, $_POST);
    }
    // Geburtsdatum gegeben und valid
  }

  // Geburtsort validieren
  // - nicht gegeben
  // ODER
  // - echtes Datum
  // - 2 - 100 Jahre alt
  if(empty($_POST["birthplace"] ?? "")) {
    // Nicht gegeben oder nur Leerzeichen
    unset($_POST["birthplace"]);
  }
  elseif(strlen($_POST["name"]) > 50) {
    // Zu lang
    send_alert($target, "warning", "Geburtsort zu lang (max 50 Zeichen)", False, $_POST);
  }
  // Geburtsort valid

  // Adresse validieren
  // Nicht gegeben
  // ODER
  // 5 - 50 lang
  $_POST["address"] = trim($_POST["address"] ?? "");
  if(empty($_POST["address"])) {
    // Nicht gegeben oder nur Leerzeichen
    unset($_POST["address"]);
  }
  elseif(strlen($_POST["address"]) < 5) {
    // Zu kurz
    send_alert($target, "warning", "Adresse zu kurz (min 5 Zeichen)", False, $_POST);
  }
  elseif(strlen($_POST["address"]) > 50) {
    // Zu lang
    send_alert($target, "warning", "Adresse zu lang (max 50 Zeichen)", False, $_POST);
  }
  // Adresse valid

  // Postleitzahl validieren
  // Nicht gegeben
  // ODER
  // - 5 lang
  // - nur Nummern
  if(empty($_POST["post_code"] ?? "")) {
    // Nicht gegeben oder nur Leerzeichen
    unset($_POST["post_code"]);
  }
  elseif(!preg_match("/[0-9]{5}/", $_POST["post_code"])) {
    // Nicht 5 lang
    send_alert($target, "warning", "Postleitzahl ungültig (nur 5 Ziffern erlaubt)", False, $_POST);
  }
  // Postleitzahl valid

  // Ort validieren
  // Nicht gegeben
  // ODER
  // bis 50 lang
  $_POST["city"] = trim($_POST["city"] ?? "");
  if(empty($_POST["city"])) {
    // Nicht gegeben oder nur Leerzeichen
    unset($_POST["city"]);
  }
  elseif(strlen($_POST["city"]) > 50) {
    // Zu lang
    send_alert($target, "warning", "Ort zu lang (max 50 Zeichen)", False, $_POST);
  }
  // Ort valid

  // Notiz validieren
  // Nicht gegeben
  // ODER
  // bis 500 lang
  $_POST["note"] = trim($_POST["note"] ?? "");
  if(empty($_POST["note"])) {
    // Nicht gegeben oder nur Leerzeichen
    unset($_POST["note"]);
  }
  elseif(strlen($_POST["note"]) > 500) {
    // Zu lang
    send_alert($target, "warning", "Notiz zu lang (max 500 Zeichen)", False, $_POST);
  }
  // Notiz valid

  // WORK IN PROGRESS - WIP
  // Eintrag in die Datenbank tun
  $query = sprintf(
    "INSERT INTO participant(name, birthday, birthplace, address, post_code, city, note) VALUE ('%s', %s, %s, %s, %s, %s, %s)",
    mysqli_real_escape_string($db, $_POST["name"]),
    mysql_escape_or_null($_POST["birthday"] ?? NULL),
    mysql_escape_or_null($_POST["birthplace"] ?? NULL),
    mysql_escape_or_null($_POST["address"] ?? NULL),
    mysql_escape_or_null($_POST["post_code"] ?? NULL),
    mysql_escape_or_null($_POST["city"] ?? NULL),
    mysql_escape_or_null($_POST["note"] ?? NULL)
  );
  if(!mysqli_query($db, $query)) {
    // Fehler beim Query
    send_alert($target, "danger", "Fehler: " . mysqli_error($db));
  }
  send_alert($target, "success", "Teilnehmer hinzugefügt");
?>