<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Trainer");

  // Datum
  $current_year = date("Y");

  // STATISTIK
  // Anzahl der Teilnehmer
  $query = "SELECT COUNT(1) FROM participant;";
  $result = mysqli_query($db, $query);
  $anzahl_teilnehmer = mysqli_fetch_array($result)[0];

  // Anzahl der Abzeichen
  $query = "SELECT COUNT(1) FROM badge;";
  $result = mysqli_query($db, $query);
  $anzahl_abzeichen = mysqli_fetch_array($result)[0];

  // Ausgestellte Abzeichen im derzeitigen Jahr
  $query = sprintf(
    "SELECT IF((SELECT SUM(amount) FROM statistics WHERE `year` = %d) IS NULL, 0, (SELECT SUM(amount) FROM statistics WHERE `year` = %d))
    + IF((SELECT COUNT(1) FROM badge WHERE `status` != 'WIP' AND YEAR(issue_date) = %d) IS NULL, 0, (SELECT COUNT(1) FROM badge WHERE `status` != 'WIP' AND YEAR(issue_date) = %d))
    AS total_year_badges",
    $current_year,
    $current_year,
    $current_year,
    $current_year
    );
  $result = mysqli_query($db, $query);
  $anzahl_abzeichen_current_year = mysqli_fetch_array($result)[0];
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?= get_head() ?>
  
  <title>Abzeichen | LiquiDB</title>
</head>
<body>
  <?= get_nav("abzeichen") ?>
  <div class="container">
    <h1 class="text-info display-4 text-center mdi mdi-account-star">Trainer Dashboard</h1>

    <?= catch_alert() ?>
    <div class="card mb-3">
      
      <div class="mdi mdi-account-outline card-header">
        Teilnehmer Verwalten
      </div>
      <div class="card-body align-content-between">
        <p><?= number_format($anzahl_teilnehmer, 0, ",", ".") ?> Teilnehmer</p>
        <a class="btn btn-outline-primary mdi mdi-account-multiple-outline" href="teilnehmer.php">Auflisten</a>
        <a class="btn btn-outline-success mdi mdi-account-plus-outline" href="teilnehmer-neu.php">Hinzufügen</a>
      </div>
    </div>
    <div class="card">
      
      <div class="mdi mdi-card-outline card-header">
        Abzeichen Verwalten
      </div>
      <div class="card-body align-content-between">
        <p><?= number_format($anzahl_abzeichen, 0, ",", ".") ?> Abzeichen</p>
        <p><?= number_format($anzahl_abzeichen_current_year, 0, ",", ".") ?> Abzeichen in <?= $current_year ?> Ausgestellt</p>
        <a class="btn btn-outline-primary mdi mdi-cards-outline" href="abzeichen.php">Auflisten</a>
      </div>
    </div>
  </div>
  
  <?= get_foot() ?>
</body>
</html>