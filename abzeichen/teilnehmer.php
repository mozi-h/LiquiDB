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
  <?= get_nav("abzeichen") ?>
  <div class="container">
    <h1 class="text-info display-4 text-center mdi mdi-account-outline">Teilnehmer</h1>
    <?= catch_alert() ?>
    <table id="data" class="table table-striped"
      data-toggle="table"
      data-url="<?= RELPATH ?>ajax/abzeichen/teilnehmer.php"

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
        <th data-field="name" data-sortable="true" data-formatter="genderFormatter">Name</th>
        <th data-field="group" data-sortable="true">Gruppe</th>
        <th class="d-none d-lg-table-cell" data-field="birthday" data-sortable="false">Geburtstag</th>
        <th data-field="age" data-sortable="true">Alter</th>
        <th class="d-none d-md-table-cell" data-field="city" data-sortable="true">Ort</th>
        <th class="d-none d-sm-table-cell" data-field="note" data-sortable="true">Notiz</th>
      </thead>
    </table>
    <div class="alert alert-info mdi mdi-account-edit-outline mt-1" role="alert">
      Klicke auf einen Teilnehmer, um diesen zu bearbeiten
    </div>
  </div>
  
  <?= get_foot(True) ?>
  <script>
    // Detail-Funktion leitet zur Bearbeiten-Seite weiter
    function detailFormatter(index, row) {
      window.location.href = "<?= RELPATH ?>abzeichen/teilnehmer-bearbeiten.php?id=" + row["id"];
    }

    // Zeigt Gender-Symbol vor Namen an, wenn gegeben
    var gender_styles = {
      m: "gender-male",
      w: "gender-female",
      d: "gender-non-binary"
    };
    function genderFormatter(value, row) {
      if(row["gender"] != null) {
        return "<span class='mdi mdi-" + gender_styles[row["gender"]] + "'></span>" + value
      }
      return value;
    }
  </script>
</body>
</html>