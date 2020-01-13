<?php
  require_once("../config.php");
  set_relpath(1);

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
  <div class="container">
    <table id="data" class="table table-striped"
      data-toggle="table"
      data-url="<?= RELPATH ?>ajax/abzeichen/teilnehmer.php"

      data-locale="de-DE"
      data-pagination="true"
      data-show-extended-pagination="true"
      data-show-fullscreen="true"
      data-search="true"
      data-sort-name="name"
      data-sort-order="asc"
      data-detail-view="false"
      data-detail-view-by-click="true"
      data-detail-formatter="detailFormatter">
      <thead class="thead-dark">
        <th data-field="name" data-sortable="true">Name</th>
        <th class="d-none d-md-table-cell" data-field="birthday" data-sortable="false">Geburtstag</th>
        <th data-field="age" data-sortable="true">Alter</th>
        <th class="d-none d-sm-table-cell" data-field="city" data-sortable="true">Ort</th>
        <th data-field="note" data-sortable="true">Notiz</th>
      </thead>
    </table>
    <div class="alert alert-info mdi mdi-information" role="alert">
      Klicke auf einen Teilnehmer, um diesen aufzurufen
    </div>
  </div>
  
  <?= get_foot(True) ?>
  <script>
    // Detail-Funktion leitet zur Bearbeiten-Seite weiter
    function detailFormatter(index, row) {
      window.location.href = "<?= RELPATH ?>abzeichen/teilnehmer-bearbeiten.php?id=" + row["id"];
    }
  </script>
</body>
</html>