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
  /** Ziel für Alerts */
  $target = RELPATH . "abzeichen/abzeichen-bearbeiten.php?id=" . $badge["id"];
  // ist das Abzeichen noch in Arbeit?
  if($badge["status"] != "WIP") {
    // Abzeichen gesetzt / ausgestellt
    send_alert($target, "warning", "Abzeichen ist bereits ausgestellt");
  }
  $participant = get_participant($badge["participant_id"]);

  // INFORMATION HOLEN
  // Heutiger Tag (Formatiert)
  $timezone = new DateTimeZone(TIMEZONE);
  $current_date = new DateTime("now", $timezone);
  $current_date_formatted = $current_date->format("d.m.Y");

  // INFORMATION HOLEN
  // Heutiger Tag (Formatiert)
  $timezone = new DateTimeZone(TIMEZONE);
  $current_date = new DateTime("now", $timezone);
  $current_date_formatted = $current_date->format("d.m.Y");

  // Disziplin Liste mit derzeitigem Status
  $query = sprintf(
    "SELECT *, DATE_FORMAT(`date`, '%%d.%%m.%%Y') AS date_formatted
    FROM discipline_list AS d_l
    LEFT JOIN (
      SELECT id AS discipline_id, discipline_list_id, `date`, `time`, CONCAT(FLOOR(`time`/60), ':', IF(LENGTH(MOD(`time`, 60)) = 1, CONCAT('0', MOD(`time`, 60)), MOD(`time`, 60))) AS time_formatted
      FROM discipline
      WHERE badge_id = %d
    ) AS d ON d_l.id = d.discipline_list_id
    WHERE d_l.badge_name_internal = '%s'
    ORDER BY d_l.`type`, d_l.`count`",
    $_GET["id"],
    $badge["name_internal"]
  );
  $disciplines = mysqli_query($db, $query);

  // Alle Disziplinen erfüllt (da dies "ausstellen", nicht "setzen" ist)
  while($row = mysqli_fetch_array($disciplines)) {
    // Ist die Disziplin gülig?
    switch ($row["auto_type"]) {
      case "NORMAL":
        // Darf nicht älter als 3 Monate sein
      case "TIME":
        // Darf nicht älter als 3 Monate sein
        // UND time nicht größer als auto_info (hier nicht überprüft; da eintragen nicht möglich)
        // (deswegen teilen sich NORMAl und TIME einen Case-Body)
        if($row["date"] == NULL) {
          send_alert($target, "warning", $row["name"] . " ungültig");
        }
        $discipline_date = new DateTime($row["date"], $timezone);
        $discipline_date->add(new DateInterval("P1D"));
        $max_discipline_date = new DateTime("now", $timezone);;
        $max_discipline_date->sub(new DateInterval("P3M"));
        if($discipline_date <= $max_discipline_date) {
          send_alert($target, "warning", $row["name"] . " ungültig");
        };
        break;
      case "AGE":
        // Teilnehmer muss mindestend auto_info Jahre alt sein
        // Blockiert Ausstellung
        if($participant["age"] < $row["auto_info"]) {
          send_alert($target, "warning", $row["name"] . " ungültig");
        }
        break;
      case "BADGE":
        // Teilnehmer muss ein gültiges auto_info Abzeichen haben
        // Blockiert Ausstellung
        $query = sprintf(
          "SELECT issue_date FROM badge WHERE participant_id = %d AND badge_name_internal = '%s' AND `status` = 'OK'",
          $participant["id"],
          $row["auto_info"]
        );
        $result = mysqli_query($db, $query);
        if(mysqli_num_rows($result) != 1) {
          // Kein benötigtes Abzeichen vorhanden
          send_alert($target, "warning", $row["name"] . " ungültig");
        }
        break;
      case "DOCUMENT":
        // Das Dokument darf maximal auto_info Jahre in der Vergangenheit ausgestellt worden sein
        if($row["date"] == NULL) {
          send_alert($target, "warning", $row["name"] . " ungültig");
        }
        $document_date = new DateTime($row["date"], $timezone);
        $document_date->add(new DateInterval("P1D"));
        $max_document_date = new DateTime("now", $timezone);;
        $max_document_date->sub(new DateInterval("P" . $row["auto_info"] . "Y"));
        if($document_date <= $max_document_date) {
          send_alert($target, "warning", $row["name"] . " ungültig");
        };
        break;
      default:
        die("Kein bekannter auto_type");
    }
  }

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

  $query = sprintf(
    "UPDATE badge SET issue_date = '%s', issue_user_id = %d, issue_forced = 0 WHERE id = %d",
    $_POST["issue-date"],
    $_SESSION["USER"],
    $badge["id"]
  );
  if(!mysqli_query($db, $query)) {
    // Fehler beim Query
    send_alert($target, "danger", "Fehler beim ausstellen des Abzeichens: " . mysqli_error($db));
  }
  send_alert($target, "success", "Abzeichen ausgestellt");
?>