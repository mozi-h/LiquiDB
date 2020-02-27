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

  // Alle WIP Abzeichen löschen (keine Statistik nötig)
  $query = sprintf(
    "DELETE FROM badge WHERE participant_id = %d AND `status` = 'WIP'",
    $_GET["id"]
  );
  if(!mysqli_query($db, $query)) {
    // Fehler beim Query
    send_alert($target, "danger", "Fehler beim löschen der Abreichen i.A.: " . mysqli_error($db));
  }

  // Liste der verbleibenden Abzeichen
  $query = sprintf(
    "SELECT b.id, b.badge_name_internal, name_short, b.issue_date, b.`status`
    FROM badge AS b
    LEFT JOIN badge_list AS b_l ON b_l.name_internal = b.badge_name_internal
    WHERE participant_id = %d",
    $_GET["id"]
  );
  $badges_response = mysqli_query($db, $query);
  while($badge = mysqli_fetch_array($badges_response)) {
    // Disziplinen löschen
    $query = sprintf(
      "DELETE FROM discipline WHERE badge_id = %d",
      $_GET["id"]
    );
    if(!mysqli_query($db, $query)) {
      // Fehler beim Query
      send_alert($target, "danger", "Fehler beim löschen der Disziplinen von Abzeichen " . $badge["name_short"] . " (" . $badge["id"] . "): " . mysqli_error($db));
    }

    // Abzeichen löschen
    $query = sprintf(
      "DELETE FROM badge WHERE id = %d",
      $badge["id"]
    );
    if(!mysqli_query($db, $query)) {
      // Fehler beim Query
      send_alert($target, "danger", "Fehler beim löschen des Abzeichens " . $badge["name_short"] . " (" . $badge["id"] . ") (Disziplinen gelöscht): " . mysqli_error($db));
    }
    if(isset($_POST["abzeichen_keine_statistik"])) {
      // Übertragen in die Statistik nicht erwünscht (z.B. versehentlich ausgestellt)
      continue;
    }
    // existiert Abzeichentyp + Jahr bereits?
    $issue_year = intval(substr($badge["issue_date"], 0, 4));
    $query = sprintf(
      "SELECT 1 FROM `statistics` WHERE badge_name_internal = '%s' AND `year` = %d",
      $badge["badge_name_internal"],
      $issue_year
    );
    if(mysqli_fetch_array(mysqli_query($db, $query))[0] != 1) {
      // Neue Zeile in statistics
      $query = sprintf(
        "INSERT INTO `statistics`(badge_name_internal, `year`, amount) VALUES ('%s', %d, 1)",
        $badge["badge_name_internal"],
        $issue_year
      );
    }
    else {
      $query = sprintf(
        "UPDATE `statistics` SET amount = amount + 1 WHERE badge_name_internal = '%s' AND `year` = %d",
        $badge["badge_name_internal"],
        $issue_year
      );
    }
    // Senden
    if(!mysqli_query($db, $query)) {
      // Fehler beim Query
      send_alert($target, "danger", "Fehler beim eintragen in die Statistik (Abzeichen " . $badge["name_short"] . " (" . $badge["id"] . ") gelöscht): " . escape(mysqli_error($db)) . "<br>Umbedingt manuell nachtragen: " . $badge["name_short"] . " in $issue_year ausgestellt.", True);
    }
  }

  // Alle Abzeichen gelöscht. Teilnehmer löschen
  $query = sprintf(
    "DELETE FROM participant WHERE id = %d",
    $_GET["id"]
  );
  if(!mysqli_query($db, $query)) {
    // Fehler beim Query
    send_alert($target, "danger", "Fehler beim löschen des Teilnehmers: " . mysqli_error($db));
  }
  send_alert(RELPATH . "abzeichen/teilnehmer.php", "success", "Teilnehmer gelöscht");
?>