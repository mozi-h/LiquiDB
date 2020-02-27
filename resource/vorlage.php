<?php
  require_once("../config.php");
  set_relpath(1);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?= get_head() ?>
  
  <title>Vorlage | LiquiDB</title>
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
          <li class="mdi mdi-account">Benutzer</li>
          <ul>
            <li class="mdi mdi-shield-account">Admin</li>
            <li class="mdi mdi-account-star">Trainer</li>
            <li class="mdi mdi-pencil">Bearbeiten</li>
            <li class="mdi mdi-login">Login</li>
            <li class="mdi mdi-logout">Logout</li>
          </ul>
          <li class="mdi mdi-account-outline">Teilnehmer</li>
          <li class="mdi mdi-card-outline">Abzeichen</li>
          <ul>
            <li class="mdi mdi-cards-outline">Abzeichen auflisten</li>
            <li class="mdi mdi-card-plus-outline">Abzeichen neu</li>
            <li class="mdi mdi-card-outline">Abzeichen (in Arbeit)</li>
            <li class="mdi mdi-card-bulleted">Abzeichen (fertig)</li>
            <li class="mdi mdi-card-bulleted-off-outline">Abzeichen (abgelaufen)</li>
          </ul>
          <li class="mdi mdi-clipboard-outline">Disziplin</li>
          <ul>
            <li class="mdi mdi-clipboard-multiple-outline">Disziplinen auflisten</li>
            <li class="mdi mdi-clipboard-plus-outline">Disziplin neu</li>
            <li class="mdi mdi-clipboard-outline">Disziplin (in Arbeit)</li>
            <li class="mdi mdi-clipboard-check">Disziplin (fertig)</li>
            <li class="mdi mdi-clipboard-alert-outline">Disziplin (abgelaufen)</li>
          </ul>
          <li class="mdi mdi-cash-register">Eintritt</li>
          <ul>
            <li class="mdi mdi-cash-multiple">Bezahlt</li>
            <li class="mdi mdi-cash-remove">Nicht Bezahlt</li>
            <li class="mdi mdi-cash-plus">Bezahlen</li>
            <li class="mdi mdi-cash-minus">Bezahlen rückgängig</li>
            <hr>
            <li class="mdi mdi-account-group-outline">Anwesend</li>
          </ul>
        </ul>
      </div>
    </div>
  </div>
  
  <?= get_foot() ?>
</body>
</html>