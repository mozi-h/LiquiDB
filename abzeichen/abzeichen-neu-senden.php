<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Trainer");

  /** Ziel für Alerts */
  $target = RELPATH . "abzeichen/teilnehmer.php";

  // Existiert der Nutzer?
  if(!filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    // id keine Nummer
    send_alert($target, "warning", "ID ist keine Nummer");
  }
  $_GET["id"] = intval($_GET["id"]);
  $query = sprintf(
    "SELECT 1 FROM participant WHERE id = %d",
    $_GET["id"]
  );
  $result = mysqli_query($db, $query);
  if(mysqli_num_rows($result) != 1) {
    // id kein Nutzer
    send_alert($target, "warning", "ID ist kein Teilnehmer");
  }
  $participant = get_participant($_GET["id"]);

  /** Ziel für Alerts */
  $target = RELPATH . "abzeichen/teilnehmer-bearbeiten.php?id=" . $_GET["id"];

  // Abzeichen validieren
  // - Gegeben
  // - Existent
  $_POST["badge"] = trim($_POST["badge"] ?? "");
  if(empty($_POST["badge"])) {
    // Nicht gegeben oder nur Leerzeichen
    send_alert($target, "warning", "Kein Abzeichen gegeben");
  }
  else {
    // Existiert das Abzeichen?
    $query = sprintf(
      "SELECT COUNT(1) FROM badge_list WHERE name_internal = '%s'",
      mysqli_real_escape_string($db, $_POST["badge"])
    );
    if(mysqli_fetch_array(mysqli_query($db, $query))[0] != 1) {
      send_alert($target, "warning", "Abzeichen existiert nicht");
    }
  }
  // Abzeichen gegeben und valid

  // Ausstellungsdatum validieren
  // - nicht gegeben
  // - noch kein "in Arbeit" vorhanden
  // ODER
  // - echtes Datum
  // - heute oder früher
  $_POST["issue-date"] = trim($_POST["issue-date"] ?? "");
  if(empty($_POST["issue-date"])) {
    // Nicht gegeben oder nur Leerzeichen
    unset($_POST["issue-date"]);
    $issue_forced = $issue_user_id = "NULL";

    // Gibt es bereits ein solches Abzeichen, das in arbeit ist?
    $query = sprintf(
      "SELECT 1 FROM badge WHERE participant_id = %d AND badge_name_internal = '%s' AND issue_date IS NULL LIMIT 1",
      $_GET["id"],
      $_POST["badge"]
    );
    if(mysqli_fetch_array(mysqli_query($db, $query))[0] == 1) {
      send_alert($target, "info", "Ein solches Abzeichen ist bereits in Arbeit");
    }
  }
  elseif(!preg_match("/^(0[1-9]|[12][0-9]|3[01])\.(0[1-9]|1[012])\.\d{4}$/", $_POST["issue-date"])) {
    // kein TT.MM.JJJJ Datum
    send_alert($target, "warning", "Kein TT.MM.JJJJ Datum");
  }
  else {
    $_POST["issue-date"] = substr($_POST["issue-date"], 6, 4) . "-" . substr($_POST["issue-date"], 3, 2) . "-" . substr($_POST["issue-date"], 0, 2);

    $timezone = new DateTimeZone(TIMEZONE);
    $issue_date_dt = new DateTime($_POST["issue-date"], $timezone);
    $current_date = new DateTime("now", $timezone);
    if($issue_date_dt > $current_date) {
      send_alert($target, "warning", "Das Datum liegt in der Zukunft");
    }

    $issue_forced = "1";
    $issue_user_id = $_SESSION["USER"];
  }

  // Eintrag in die Datenbank tun
  $query = sprintf(
    "INSERT INTO badge(participant_id, badge_name_internal, issue_date, issue_forced, issue_user_id) VALUE (%d, '%s', %s, %s, %s)",
    $_GET["id"],
    $_POST["badge"],
    mysql_escape_or_null($_POST["issue-date"] ?? NULL),
    $issue_forced,
    $issue_user_id
  );
  if(!mysqli_query($db, $query)) {
    // Fehler beim Query
    send_alert($target, "danger", "Fehler: " . mysqli_error($db));
  }
  send_alert($target, "success", "Abzeichen angelegt");
?>