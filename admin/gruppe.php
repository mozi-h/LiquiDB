<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Admin");
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?= get_head(True) ?>
  
  <title>Gruppen | LiquiDB</title>
</head>
<body>
  <?= get_nav("admin") ?>
  <div class="container">
    <h1 class="text-info display-4 text-center mdi mdi-account-outline">Teilnehmer</h1>
    <?= catch_alert() ?>
    <table id="data" class="table table-striped"
      data-toggle="table"
      data-url="<?= RELPATH ?>ajax/admin/gruppe.php"

      data-locale="de-DE"
      data-pagination="true"
      data-show-extended-pagination="true"
      data-show-fullscreen="true"
      data-show-refresh="true"
      data-search="true"
      data-sort-name="name"
      data-sort-order="asc"
      data-detail-view="false"
      data-detail-view-by-click="true"
      data-detail-formatter="detailFormatter">
      <thead class="thead-dark">
        <th data-field="name" data-sortable="true">Name</th>
        <th data-field="description" data-sortable="true">Beschreibung</th>
      </thead>
    </table>
    <div class="alert alert-info mdi mdi-pencil mt-1" role="alert">
      Klicke auf eine Gruppe, um diese zu bearbeiten
    </div>
  </div>
  
  <?= get_foot(True) ?>
  <script>
    // Detail-Funktion leitet zur Bearbeiten-Seite weiter
    function detailFormatter(index, row) {
      window.location.href = "<?= RELPATH ?>admin/gruppe-bearbeiten.php?id=" + row["id"];
    }
  </script>
</body>
</html>