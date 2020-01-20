<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Admin");

  // Existiert der Nutzer?
  if(!filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    // id keine Nummer
    send_alert(RELPATH . "admin/nutzer.php", "warning", "ID ist keine Nummer");
  }
  $_GET["id"] = intval($_GET["id"]);
  $query = sprintf(
    "SELECT 1 FROM user WHERE id = %d",
    $_GET["id"]
  );
  $result = mysqli_query($db, $query);
  if(mysqli_num_rows($result) != 1) {
    // id kein Nutzer
    send_alert(RELPATH . "admin/nutzer.php", "warning", "ID ist kein Nutzer");
  }
  $user = get_user($_GET["id"]);

  /** Ziel für Alerts */
  $target = RELPATH . "admin/nutzer-bearbeiten.php?id=" . $user["id"];

  if($user["id"] == 1) {
    // System kann nicht bearbeitet werden
    send_alert($target, "danger", "System kann nicht bearbeitet werden");
  }

  // Eintrag in der Datenbank aktualisieren, wenn etwas geändert wurde
  if((isset($_POST["ist_trainer"]) == $user["ist_trainer"]) & (isset($_POST["ist_admin"]) == $user["ist_admin"])) {
    // Nichts geändert
    send_alert($target, "warning", "Nichts geändert");
  }

  $query = sprintf(
    "UPDATE user SET ist_trainer = %d, ist_admin = %d WHERE id = %d",
    isset($_POST["ist_trainer"]),
    isset($_POST["ist_admin"]),
    $user["id"]
  );
  if(!mysqli_query($db, $query)) {
    // Fehler beim Query
    send_alert($target, "danger", "Fehler: " . mysqli_error($db));
  }
  send_alert($target, "success", "Rollen aktualisiert");
?>