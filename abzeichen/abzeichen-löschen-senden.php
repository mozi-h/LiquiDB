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
  $target = RELPATH . "abzeichen/abzeichen-bearbeiten.php?id=" . $_GET["id"];

  // Disziplinen löschen
  $query = sprintf(
    "DELETE FROM discipline WHERE badge_id = %d",
    $_GET["id"]
  );
  if(!mysqli_query($db, $query)) {
    // Fehler beim Query
    send_alert($target, "danger", "Fehler beim löschen der Disziplinen: " . mysqli_error($db));
  }

  // Abzeichen löschen
  // Disziplinen löschen
  $query = sprintf(
    "DELETE FROM badge WHERE id = %d",
    $_GET["id"]
  );
  if(!mysqli_query($db, $query)) {
    // Fehler beim Query
    send_alert($target, "danger", "Fehler beim löschen des Abzeichens (Disziplinen gelöscht): " . mysqli_error($db));
  }

  /** Ziel für Alerts */
  $target = RELPATH . "abzeichen/teilnehmer-bearbeiten.php?id=" . $badge["participant_id"];

  // Statistik behalten, wenn abzeichen ausgestellt ist
  if($badge["status"] != "WIP") {
    if(isset($_POST["abzeichen_keine_statistik"])) {
      // Übertragen in die Statistik nicht erwünscht (z.B. versehentlich ausgestellt)
      send_alert($target, "success", "Abzeichen gelöscht und nicht in die Statistik übernommen");
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
      send_alert($target, "danger", "Fehler beim eintragen in die Statistik (Abzeichen gelöscht): " . escape(mysqli_error($db)) . "<br>Umbedingt manuell nachtragen: " . $badge["name"] . " in $issue_year ausgestellt.", True);
    }
    send_alert($target, "success", "Abzeichen gelöscht und in die Statistik übernommen (anonym)");
  }
  send_alert($target, "success", "Abzeichen gelöscht");
?>