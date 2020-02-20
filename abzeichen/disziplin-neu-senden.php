<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Trainer");

  /** Ziel für Alerts */
  $target = RELPATH . "abzeichen/abzeichen.php";

  // Existiert das Abzeichen und ist es in Arbeit?
  if(!filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    // id keine Nummer
    send_alert($target, "warning", "ID ist keine Nummer");
  }
  $_GET["id"] = intval($_GET["id"]);
  $query = sprintf(
    "SELECT 1 FROM badge WHERE id = %d",
    $_GET["id"]
  );
  $result = mysqli_query($db, $query);
  if(mysqli_num_rows($result) != 1) {
    // id kein Abzeichen
    send_alert($target, "warning", "ID ist kein Abzeichen");
  }
  $badge = get_badge($_GET["id"]);
  // ist das Abzeichen noch in Arbeit?
  if($badge["status"] != "WIP") {
    // Abzeichen gesetzt / ausgestellt
    send_alert($target, "warning", "Abzeichen ist bereits ausgestellt");
  }

  /** Ziel für Alerts */
  $target = RELPATH . "abzeichen/abzeichen-bearbeiten.php?id=" . $_GET["id"] . "#disziplinen";

  // Disziplin validieren
  // - gegeben
  // - int
  // - nicht AGE oder BADGE
  // - noch nicht vorhanden
  $_POST["discipline"] = trim($_POST["discipline"] ?? "");
  if(empty($_POST["discipline"])) {
    // Nicht gegeben oder nur Leerzeichen
    send_alert($target, "warning", "Keine Disziplin gegeben");
  }
  elseif(!filter_var($_POST["discipline"], FILTER_VALIDATE_INT)) {
    // keine Nummer
    send_alert($target, "warning", "Disziplin ist keine Nummer");
  }
  else {
    // Disziplin noch nicht vorhanden
    $query = sprintf(
      "SELECT 1
      FROM discipline
      WHERE badge_id = %d
      AND discipline_list_id = %d",
      $_GET["id"],
      $_POST["discipline"]
    );
    if(mysqli_fetch_array(mysqli_query($db, $query))[0] == 1) {
      // Disziplin bereits eingetragen
      send_alert($target, "warning", "Disziplin bereits eingetragen");
    }
    // Disziplin gehört zum Abzeichentyp und ist nicht vollautomatisiert
    $query = sprintf(
      "SELECT auto_type, auto_info
      FROM discipline_list
      WHERE auto_type != 'AGE'
      AND auto_type != 'BADGE'
      AND badge_name_internal = '%s'
      AND `id` = %d",
      $badge["name_internal"],
      $_POST["discipline"]
    );
    $discipline_result = mysqli_query($db, $query);
    if(mysqli_num_rows($discipline_result) !== 1) {
      send_alert($target, "warning", "Disziplin ungültig");
    }
    $discipline = mysqli_fetch_array($discipline_result);
  }
  // Disziplin valide

  // Datum
  // - gegeben
  // - echtes Datum
  // - heute oder früher
  if(empty($_POST["issue-date"] ?? "")) {
    // Nicht gegeben oder nur Leerzeichen
    send_alert($target, "warning", "Kein Datum gegeben");
  }
  elseif(!preg_match("/^(0[1-9]|[12][0-9]|3[01])\.(0[1-9]|1[012])\.\d{4}$/", $_POST["issue-date"])) {
    // kein TT.MM.JJJJ Datum
    send_alert($target, "warning", "Kein TT.MM.JJJJ Datum");
  }
  else {
    $_POST["issue-date"] = substr($_POST["issue-date"], 6, 4) . "-" . substr($_POST["issue-date"], 3, 2) . "-" . substr($_POST["issue-date"], 0, 2);
    $timezone = new DateTimeZone(TIMEZONE);
    $issue_date = new DateTime($_POST["issue-date"], $timezone);
    $current_date = new DateTime("now", $timezone);
    if($issue_date > $current_date) {
      // Datum in der Zukunft
      send_alert($target, "warning", "Datum liegt in der Zukunft");
    }
  }
  // Datum valid

  // Zeit
  // - nicht gegeben
  // ODER
  // - auto_type der discipline_list ist TIME
  // - time <= auto_info der discipline_list
  if(empty($_POST["time"] ?? "")) {
    // Nicht gegeben oder nur Leerzeichen
    unset($_POST["time"]);
  }
  elseif(!preg_match("/^[0-5]\d:[0-5]\d$/", $_POST["time"])) {
    // id keine Nummer
    send_alert($target, "warning", "Time ist kein MM:SS Format");
  }
  else {
    $_POST["time"] = substr($_POST["time"], 0, 2) * 60 + substr($_POST["time"], 3, 2);
    if($discipline["auto_type"] != "TIME") {
      // time findet keine Anwendung
      send_alert($target, "warning", "Disziplin nimmt kein time");
    }
    elseif($_POST["time"] > $discipline["auto_info"]) {
      // time zu hoch
      send_alert($target, "warning", "Disziplin zeitlich nicht bestanden");
    }
  }

  $query = sprintf(
    "INSERT INTO discipline(badge_id, discipline_list_id, `date`, `time`) VALUES (%d, %d, '%s', %s)",
    $_GET["id"],
    $_POST["discipline"],
    $_POST["issue-date"],
    mysql_escape_or_null($_POST["time"] ?? NULL)
  );
  if(!mysqli_query($db, $query)) {
    // fehler beim Query
    send_alert($target, "danger", "Fehler: " . mysqli_error($db));
  }
  send_alert($target, "success", "Disziplin eingetragen")
?>
