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
    <h1 class="text-info display-4 text-center mdi mdi-card-outline">Abzeichen</h1>
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
        <th data-field="participant_name" data-sortable="true" data-formatter="genderLinkFormatter">Teilnehmer</th>
        <th data-field="badge_name" data-sortable="true" data-formatter="badgeFormatter">Abzeichen</th>
        <th class="d-none d-lg-table-cell" data-field="issue_date_formatted" data-sortable="true">Ausstellungsdatum</th>
        <th class="d-none d-md-table-cell" data-field="status" data-sortable="true">Status</th>
      </thead>
    </table>
    <div class="alert alert-info mdi mdi-information-outline mt-1" role="alert">
      Kodierung: <span class="mdi mdi-card-outline" style="background-color:lightgray">In Arbeit</span>  <span class="mdi mdi-card-bulleted" style="background-color:lightgreen">Gültig</span>  <span class="mdi mdi-card-bulleted" style="background-color:lightblue">Gültig (gesetzt)</span>  <span class="mdi mdi-card-bulleted-off-outline" style="background-color:orange">Abgelaufen</span>
    </div>
    <div class="alert alert-info mdi mdi-information-outline" role="alert">
      Pro Teilnehmer und Abzeichen wird nur das Aktuellste angezeigt. Eine vollständige Liste ist auf der Teilnehmerseite.
    </div>
    <div class="alert alert-info mdi mdi-pencil" role="alert">
      Klicke auf ein Abzeichen, um dieses zu bearbeiten
    </div>
  </div>
  
  <?= get_foot(True) ?>
  <script>
    // Detail-Funktion leitet zur Bearbeiten-Seite weiter
    function detailFormatter(index, row) {
      window.location.href = "<?= RELPATH ?>abzeichen/abzeichen-bearbeiten.php?id=" + row["id"];
    }

    // Zeigt Gender-Symbol vor Namen an, wenn gegeben
    // Setzt Link zur Teilnehmer-bearbeiten Seite
    var gender_styles = {
      m: "gender-male",
      w: "gender-female",
      d: "gender-non-binary"
    };
    function genderLinkFormatter(value, row) {
      if(row["gender"] != null) {
        return "<span class='mdi mdi-" + gender_styles[row["gender"]] + "'></span><a href='<?= RELPATH ?>abzeichen/teilnehmer-bearbeiten.php?id=" + row["participant_id"] + "'>" + value + "</a>"
      }
      return value;
    }

    // Zeigt passendes Abzeichen-Symbol an
    var badge_styles = {
      "i.A.": "card-outline",
      "OK": "card-bulleted",
      "Alt": "card-bulleted-off-outline"
    };
    function badgeFormatter(value, row) {
      return "<span class='mdi mdi-" + badge_styles[row["status"]] + "'></span>" + value
    }

    // Hintergrundfarbe nach Status setzen
    var status_colors = {
      "i.A.": "lightgray",
      "OK": "lightgreen",
      "Alt": "orange"
    }
    function rowStyle(row) {
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