<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Trainer");

  
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?= get_head() ?>
  
  <title>Teilnehmer | LiquiDB</title>
</head>
<body>
  <?= get_nav("abzeichen") ?>
  <div class="container">
    <h1 class="text-info display-4 text-center mdi mdi-account-outline"> Teilnehmer</h1>
    <?= catch_alert() ?>
    <table class="table table-striped text-center" id="datatable">
      <thead>
        <th></th>
        <th data-priority=1>Name</th>
        <th data-priority=700>Geburtstag</th>
        <th data-priority=600>Alter</th>
        <th data-priority=870>Ort</th>
        <th data-priority=10000>Notiz</th>
        <th data-priority=10000>Eingepflegt von</th>
      </thead>
      <tbody>
        <!--AJAX-->
      </tbody>
    </table>
  </div>
  
  <?= get_foot() ?>
</body>
</html>