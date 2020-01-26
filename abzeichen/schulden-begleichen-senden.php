<?php
  require_once("../config.php");
  set_relpath(1);
  $minify = False;

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
  $target = RELPATH . "abzeichen/teilnehmer-bearbeiten.php?id=";

  // Hat der Nutzer unbeglichene Tage?
  // Anwesenheits-Statistik
  $query = sprintf(
    "SELECT
      (
        SELECT COUNT(1) 
        FROM attendance AS att
        WHERE att.participant_id = %d
      ) AS num_attended,
      (
        SELECT COUNT(1) 
        FROM attendance AS att
        WHERE att.participant_id = %d
        AND att.paid = 'No'
      ) AS num_not_paid
    ",
    $_GET["id"],
    $_GET["id"]);
  $att_stats_result = mysqli_query($db, $query);
  $att_stats = mysqli_fetch_array($att_stats_result);
  if($att_stats["num_not_paid"] == 0) {
    send_alert($target . $_GET["id"], "warning", "Teilnehmer hat keine unbeglichenen Tage");
  }

  // Unbeglichene Tage als bezahlt markieren
  $query = "UPDATE attendance SET paid = 'Yes' WHERE paid = 'No' AND participant_id = " . $_GET["id"];
  // Senden
  if(!mysqli_query($db, $query)) {
    // Fehler beim Query
    send_alert($target . $_GET["id"], "danger", "Fehler: " . mysqli_error($db));
  }
  send_alert($target . $_GET["id"], "success", "Schulden beglichen");
?>