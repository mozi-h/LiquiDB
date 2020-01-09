<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Admin");

  // STATISTIK
  // Anzahl der Benutzer
  $query = "SELECT COUNT(1) FROM user;";
  $result = mysqli_query($db, $query);
  $anzahl_benutzer = mysqli_fetch_array($result)[0];
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?= get_head() ?>
  
  <title>Admin | LiquiDB</title>
</head>
<body>
  <?= get_nav("admin") ?>
  <div class="container">
    <h1 class="text-info display-4 text-center">Benutzer Verwalten</h1>

    <?= catch_alert() ?>
    <div class="card">
      
      <!--div class="card-header">
        Benutzer Verwalten
      </div-->
      <div class="card-body align-content-between">
        <p class="mdi mdi-account"> <?= number_format($anzahl_benutzer, 0, ",", ".") ?> Benutzer</p>
        <a class="btn btn-outline-primary mdi mdi-account-badge" href="nutzer.php"> Auflisten</a>
        <a class="btn btn-outline-success mdi mdi-account-plus" href="nutzer-neu.php"> Hinzufügen</a>
      </div>
    </div>
  </div>
  
  <?= get_foot() ?>
</body>
</html>