<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Trainer");

  /** Ziel für Alerts */
  $target = RELPATH . "abzeichen/abzeichen.php";

  // Existiert die Disziplin?
  if(!filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    // id keine Nummer
    send_alert($target, "warning", "ID ist keine Nummer");
  }
  $_GET["id"] = intval($_GET["id"]);
  $query = sprintf(
    "SELECT badge_id FROM discipline WHERE id = %d",
    $_GET["id"]
  );
  $result = mysqli_query($db, $query);
  if(mysqli_num_rows($result) != 1) {
    // id keine Disziplin
    send_alert($target, "warning", "ID ist keine Disziplin");
  }

  // Disziplin löschen
  $query = sprintf(
    "DELETE FROM discipline WHERE id = %d",
    $_GET["id"]
  );
  if(!mysqli_query($db, $query)) {
    // Fehler beim Query
    send_alert($target, "danger", "Fehler: " . mysqli_error($db));
  }
  send_alert(RELPATH . "abzeichen/abzeichen-bearbeiten.php?id=" . mysqli_fetch_array($result)["badge_id"] . "#disziplinen", "success", "Disziplin gelöscht");
?>
