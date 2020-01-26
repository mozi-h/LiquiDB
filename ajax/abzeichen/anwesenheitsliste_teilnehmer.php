<?php
  require_once("../../config.php");
  set_relpath(2);
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

  // TEILNEHMERDATEN
  $query = sprintf(
    "SELECT att.date, att.paid
      FROM attendance AS att
      WHERE att.participant_id = %d
      ORDER BY att.date DESC",
    $_GET["id"]);
  $result = mysqli_query($db, $query);

  $paid_format = [
    "Yes" => "Bezahlt",
    "No" => "Ausstehend",
    "Other" => "Anders"
  ];
  $output = [];
  while($row = mysqli_fetch_array($result)) {
    $tmp = [];
    $tmp["date_formatted"] = substr($row["date"], 8, 2) . "." . substr($row["date"], 5, 2) . "." . substr($row["date"], 0, 4);
    $tmp["status"] = $paid_format[$row["paid"]];

    $output[] = $tmp;
  }

  if($minify) {
    echo json_encode($output);
  }
  else {
    echo json_encode($output, JSON_PRETTY_PRINT);
  }
?>