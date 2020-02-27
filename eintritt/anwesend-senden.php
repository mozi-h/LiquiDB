<?php
  require_once("../config.php");
  set_relpath(1);

  restricted();

  /** Ziel für Alerts */
  $target = RELPATH . "eintritt/index.php";

  var_dump($_POST);
  // Teilnehmer validieren
  // - Gegeben
  // - 1-20 Teilnehmer
  // - Teilnehmer existieren und sind noch nicht für Heute eingetragen
  $_POST["participant"] = $_POST["participant"] ?? NULL;
  if(($_POST["participant"] == NULL) | (count($_POST["participant"]) < 1)) {
    // Nicht gegeben oder leeres Array
    send_alert($target, "warning", "Kein(e) Teilnehmer gegeben");
  }
  elseif(count($_POST["participant"]) > 20) {
    // zu viele Teilnehmer
    send_alert($target, "warning", "Zu viele Teilnehmer gegeben (max 20)");
  }
  else {
    // Alle noch nicht für heute eingetragen?
    $_POST["participant_sanitized"] = [];
    foreach($_POST["participant"] as $participant) {
      if(!filter_var($participant, FILTER_VALIDATE_INT)) {
        send_alert($target, "danger", "Eine Teilnehmer-ID war kein Int");
      }
      $_POST["participant_sanitized"][] = intval($participant);

      $query = "SELECT 1 FROM attendance_today_not WHERE id = " . $participant;
      $result = mysqli_query($db, $query);
      if(mysqli_num_rows($result) != 1) {
        send_alert($target, "warning", "Ein Teilnehmer ist bereits als anwesend markiert");
      }
    }
  }
  // Teilnehmer validiert

  // Eintrittstyp validieren
  // - Gegeben
  // - 'Yes', 'No' oder 'Other'
  $_POST["paid"] = strtolower(trim($_POST["paid"] ?? ""));
  $_POST["paid"] = ucfirst($_POST["paid"]);
  $paid_types = [
    "Yes",
    "No",
    "Other"
  ];
  if(empty($_POST["paid"])) {
    // Nicht gegeben oder nur Leerzeichen
    send_alert($target, "warning", "Kein Eintrittstypen gegeben");
  }
  elseif(!in_array($_POST["paid"], $paid_types)) {
    // Nicht erlaubter Wert
    send_alert($target, "danger", "Nicht erlaubter Eintrittstyp");
  }
  // Eintrittstyp validiert

  // Einträge senden
  // Zusammenstellen
  $query = "INSERT INTO attendance(participant_id, paid, author_user_id) VALUES ";
  foreach($_POST["participant_sanitized"] as $participant) {
    $query .= "($participant, '" . $_POST["paid"] . "', " . $_SESSION["USER"] . "),";
  }
  $query = substr($query, 0, -1);
  // Senden
  if(!mysqli_query($db, $query)) {
    // Fehler beim Query
    send_alert($target, "danger", "Fehler: " . mysqli_error($db));
  }
  send_alert($target, "success", "Teilnehmer eingetragen");

?>