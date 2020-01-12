<?php
  require_once("config.php");
  set_relpath(0);

  restricted("Trainer");
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?= get_head(True) ?>
  
  <title>Teilnehmer | LiquiDB</title>
</head>
<body>
  <h1><i class="far fa-user"></i>Heya</h1>
  <div class="container" style="max-width: 1200px">

    <table class="table table-striped text-center"
      data-toggle="table"
      data-locale="de-DE"
      data-url="<?= RELPATH ?>ajax/abzeichen/teilnehmer.php"
      data-pagination="true"
      data-show-fullscreen="true"
      data-search="true">
      <thead>
        <th data-field="name" data-sortable="true" data-formatter="button_formatter">Name</th>
        <th data-field="birthday" data-sortable="true">Geburtstag</th>
        <th data-field="age" data-sortable="true">Alter</th>
        <th data-field="city" data-sortable="true">Ort</th>
        <th data-field="note" data-sortable="true">Notiz</th>
      </thead>
    </table>
  </div>
  
  <?= get_foot(True) ?>
  <script>
    function button_formatter(value, row, index) {
      console.log(row);
      return "<a class='mdi mdi-account-edit-outline' href='<?= RELPATH ?>abzeichen/teilnehmer-bearbeiten.php?id=" + row["id"] + "' title='Bearbeiten'></a> " + value;
    }
  </script>
</body>
</html>