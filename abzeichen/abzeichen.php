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
    <h1 class="text-info display-4 text-center mdi mdi-account-outline">Abzeichen</h1>
    <?= catch_alert() ?>
    <table id="data" class="table table-striped"
      data-toggle="table"
      data-url="<?= RELPATH ?>ajax/abzeichen/abzeichen.php"

      data-locale="de-DE"
      data-pagination="true"
      data-show-extended-pagination="true"
      data-show-fullscreen="true"
      data-show-refresh="true"
      data-search="true"
      data-sort-name="participant_name"
      data-sort-order="asc"
      data-detail-view="false"
      data-detail-view-by-click="true"
      data-detail-formatter="detailFormatter"
      data-row-style="rowStyle">
      <thead class="thead-dark">
        <th data-field="participant_name" data-sortable="true" data-formatter="genderFormatter">Teilnehmer</th>
        <th data-field="badge_name" data-sortable="true">Abzeichen</th>
        <th class="d-none d-lg-table-cell" data-field="issue_date_formatted" data-sortable="true">Ausstellungsdatum</th>
        <th class="d-none d-md-table-cell" data-field="status" data-sortable="true">Status</th>
      </thead>
    </table>
    <div class="alert alert-info mdi mdi-account-edit-outline mt-1" role="alert">
      Klicke auf ein Abzeichen, um dieses zu bearbeiten<br>
      Hintergrundfarben: <span style="background-color:lightgray">In Arbeit</span>  <span style="background-color:lightgreen">Gültig</span>  <span style="background-color:lightblue">Gültig (gesetzt)</span>  <span style="background-color:orange">Abgelaufen</span>
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

    // Hintergrundfarbe nach Status setzen
    var status_colors = {
      "i.A.": "lightgray",
      "OK": "lightgreen",
      "Alt": "orange"
    }
    function rowStyle(row) {
      console.log(row);
      
      var css = {
        "background-color": status_colors[row["status"]]
      }
      if(row["issue_forced"] == "1" & row["status"] == "OK") {
        css["background-color"] = "lightblue";
      }
      return {
        "css": css
      }
    }
  </script>
</body>
</html>