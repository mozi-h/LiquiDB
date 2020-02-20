<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Trainer");

  /** Ziel für Alerts */
  $target = RELPATH . "abzeichen/abzeichen.php";

  // Existiert das Abzeichen?
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

  $participant = get_participant($badge["participant_id"]);

  /** Ziel für Alerts */
  $target = RELPATH . "abzeichen/abzeichen-bearbeiten.php?id=" . $_GET["id"];

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

  // Disziplinen löschen
  $query = sprintf(
    "DELETE FROM discipline WHERE badge_id = %d",
    $_GET["id"]
  );
  if(!mysqli_query($db, $query)) {
    // Fehler beim Query
    send_alert($target, "danger", "Fehler beim löschen der Disziplinen: " . mysqli_error($db));
  }

  // Abzeichen setzen
  $query = sprintf(
    "UPDATE badge
    SET issue_date = '%s', issue_forced = 1, issue_user_id = %d
    WHERE badge_id = %d",
    $_POST["issue-date"],
    $_SESSION["USER"],
    $_GET["id"]
  );
  if(!mysqli_query($db, $query)) {
    // Fehler beim Query
    send_alert($target, "danger", "Fehler beim setzen des Abzeichens: " . mysqli_error($db));
  }
  send_alert($target, "success", "Abzeichen gesetzt");
?>