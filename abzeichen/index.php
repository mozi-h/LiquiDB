<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Trainer");

  // STATISTIK
  // Anzahl der Teilnehmer
  $query = "SELECT COUNT(1) FROM participant;";
  $result = mysqli_query($db, $query);
  $anzahl_teilnehmer = mysqli_fetch_array($result)[0];
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
    <h1 class="text-info display-4 text-center">Teilnehmer Verwalten</h1>

    <?= catch_alert() ?>
    <div class="card">
      
      <!--div class="card-header">
        Benutzer Verwalten
      </div-->
      <div class="card-body align-content-between">
        <p class="mdi mdi-account-outline"> <?= number_format($anzahl_teilnehmer, 0, ",", ".") ?> Teilnehmer</p>
        <a class="btn btn-outline-primary mdi mdi-account-multiple-outline" href="teilnehmer.php"> Auflisten</a>
        <a class="btn btn-outline-success mdi mdi-account-plus-outline" href="teilnehmer-neu.php"> Hinzufügen</a>
      </div>
    </div>
  </div>
  
  <?= get_foot() ?>
</body>
</html>