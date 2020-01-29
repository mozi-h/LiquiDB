<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Admin");
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?= get_head(true) ?>
  
  <title>Benutzer | LiquiDB</title>
</head>
<body>
  <?= get_nav("admin") ?>
  <div class="container">
    <h1 class="text-info display-4 text-center mdi mdi-account">Benutzer</h1>

    <?= catch_alert() ?>
    <table id="data" class="table table-striped"
      data-toggle="table"
      data-url="<?= RELPATH ?>ajax/admin/nutzer.php"

      data-locale="de-DE"
      data-pagination="true"
      data-show-extended-pagination="true"
      data-show-fullscreen="true"
      data-search="true"
      data-sort-name="username"
      data-sort-order="asc"
      data-detail-view="false"
      data-detail-view-by-click="true"
      data-detail-formatter="detailFormatter">
      <thead class="thead-dark">
        <th data-field="username" data-sortable="true">Username</th>
        <th class="d-none d-md-table-cell" data-field="name" data-sortable="false">Name</th>
        <th class ="text-center" data-field="ist_trainer" data-sortable="true"><span class="mdi mdi-account-star"></span>Trainer</th>
        <th class="d-none d-sm-table-cell text-center" data-field="ist_admin" data-sortable="true"><span class="mdi mdi-shield-account"></span>Admin</th>
      </thead>
    </table>
    <div class="alert alert-info mdi mdi-account-edit-outline mt-1" role="alert">
      Klicke auf einen Nutzer, um diesen zu bearbeiten
    </div>
  </div>
  
  <?= get_foot(True) ?>
  <script>
    // Detail-Funktion leitet zur Bearbeiten-Seite weiter
    function detailFormatter(index, row) {
      window.location.href = "<?= RELPATH ?>admin/nutzer-bearbeiten.php?id=" + row["id"];
    }
  </script>
</body>
</html>