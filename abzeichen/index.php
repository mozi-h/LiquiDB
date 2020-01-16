<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Trainer");

  // STATISTIK
  // Anzahl der Teilnehmer
  $query = "SELECT COUNT(1) FROM participant;";
  $result = mysqli_query($db, $query);
  $anzahl_teilnehmer = mysqli_fetch_array($result)[0];

  // Anzahl der Abzeichen
  $query = "SELECT COUNT(1) FROM badge;";
  $result = mysqli_query($db, $query);
  $anzahl_abzeichen = mysqli_fetch_array($result)[0];
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
        <a class="btn btn-outline-primary mdi mdi-cards-outline" href="abzeichen.php">Auflisten</a>
        <a class="btn btn-outline-success mdi mdi-card-plus-outline" href="abzeichen-neu.php">Abzeichen anlegen</a>
      </div>
    </div>
  </div>
  
  <?= get_foot() ?>
</body>
</html>