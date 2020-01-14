<?php
  require_once("../config.php");
  set_relpath(1);
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
    <div class="card mb-3">
      <div class="card-header">
        Karte
      </div>
      <div class="card-body">
        Hallo Welt!
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        Material Design Icons
      </div>
      <div class="card-body">
        <ul>
          <li class="mdi mdi-account"> Benutzer</li>
          <ul>
            <li class="mdi mdi-shield-account"> Admin</li>
            <li class="mdi mdi-account-star"> Trainer</li>
            <li class="mdi mdi-pencil"> Bearbeiten</li>
            <li class="mdi mdi-login"> Login</li>
            <li class="mdi mdi-logout"> Logout</li>
          </ul>
          <li class="mdi mdi-account-outline"> Teilnehmer</li>
        </ul>
      </div>
    </div>
  </div>
  
  <?= get_foot() ?>
</body>
</html>