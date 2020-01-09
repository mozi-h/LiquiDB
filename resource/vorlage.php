<?php
  require_once("../config.php");
  $relative_offset = "../";
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?= get_head() ?>
  
  <title>LiquiDB</title>
</head>
<body>
  <?= get_nav("vorlage") ?>
  <div class="container">
    <h1 class="text-info display-4 text-center">Vorlage</h1>

    <?= catch_alert() ?>
    <div class="card">
      <div class="card-header">
        Karte
      </div>
      <div class="card-body">
        Hallo Welt!
      </div>
    </div>
  </div>
  
  <?= get_foot() ?>
</body>
</html>