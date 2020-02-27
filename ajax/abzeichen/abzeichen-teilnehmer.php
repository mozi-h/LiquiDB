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

  // ABZEICHENDATEN
  $query = sprintf("SELECT b.id, b_l.name_short, DATE_FORMAT(b.issue_date, '%%d.%%m.%%Y') AS issue_date, b.issue_date AS issue_date_raw, b.status, b.issue_forced
            FROM badge AS b
            LEFT JOIN badge_list AS b_l ON b.badge_name_internal = b_l.name_internal
            WHERE participant_id = %d
            ORDER BY b.issue_date DESC", $_GET["id"]);
            $result = mysqli_query($db, $query);

  $output = [];
  $status_lookup = [
    "WIP" => "i.A.",
    "OK" => "OK",
    "OLD" => "Alt"
  ];
  while($row = mysqli_fetch_array($result)) {
    $tmp = [];
    $tmp["id"] = $row["id"];
    $tmp["badge_name"] = escape($row["name_short"]) ?? "";
    $tmp["status"] = $status_lookup[$row["status"]];
    $tmp["issue_date_formatted"] = escape($row["issue_date"]) ?? "";
    $tmp["issue_date"] = escape($row["issue_date_raw"]) ?? "";
    $tmp["issue_forced"] = $row["issue_forced"];

    $output[] = $tmp;
  }

  if($minify) {
    echo json_encode($output);
  }
  else {
    echo json_encode($output, JSON_PRETTY_PRINT);
  }
?>