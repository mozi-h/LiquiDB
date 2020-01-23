<?php
  require_once("../config.php");
  set_relpath(1);

  restricted();

  /** Ziel für Alerts */
  $target = RELPATH . "eintritt/anwesenheitsliste.php";

  // Datum validieren
  // - nicht gegeben = letztes Datum
  // ODER
  // - echtes Datum
  // - Termin, zu dem Personen als anwesend markiert sind
  if(empty($_GET["d"] ?? "")) {
    // Nicht gegeben oder nur Leerzeichen
    unset($_GET["d"]);
  }
  elseif(!preg_match("/^(0[1-9]|[12][0-9]|3[01])\.(0[1-9]|1[012])\.\d{4}$/", $_GET["d"])) {
    // kein TT.MM.JJJJ Datum
    send_alert($target, "warning", "Kein TT.MM.JJJJ Datum");
  }
  else {
    // Termin, zu dem Personen als anwesend markiert sind
    $_GET["d_formatted"] = substr($_GET["d"], 8, 2) . "." . substr($_GET["d"], 5, 2) . "." . substr($_GET["d"], 0, 4);
    $query = sprintf(
      "SELECT 1 FROM attendance WHERE `date` = '%s' LIMIT 1",
      $_GET["d_formatted"]
    );
    $result = mysqli_query($db, $query);
    if(mysqli_fetch_row($result)[0] != 1) {
      send_alert($target, "warning", "Am " . $_GET["d"] . " ist niemand als anwesend markiert");
    }
  }
  // Datum valid

  // Alle Tage, die in denen Teilnehmer als anwesend markiert sind
  $query = "SELECT DISTINCT `date` FROM attendance ORDER BY `date` DESC";
  $date_result = mysqli_query($db, $query);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?= get_head(True) ?>
  
  <title>Anwesenheit | LiquiDB</title>
</head>
<body>
  <?= get_nav("eintritt") ?>
  <div class="container">
    <h1 class="text-info display-4 text-center mdi mdi-account-outline">Anwesenheit</h1>
    <?= catch_alert() ?>
    <div class="card">
      <div class="mdi mdi-account-group-outline card-header">
        Tag auswählen
      </div>
      <div class="card-body align-content-between">
      <form method="post" action="<?= RELPATH ?>eintritt/anwesend-senden.php">
        <div class="form-row">
          <div class="form-group col-md-8">
            <label for="date">Tag</label>
            <select class="selectpicker form-control" data-style="custom-select" data-live-search="true" name="date" id="date" required>
              <?php
                while($row = mysqli_fetch_array($date_result)) {
                  $row["date_formatted"] = substr($row["date"], 8, 2) . "." . substr($row["date"], 5, 2) . "." . substr($row["date"], 0, 4);
                  ?>
                  <option value="<?= $row["date"] ?>" <?= ($row["date_formatted"] == ($_GET["d_formatted"] ?? "")) ? "selected" : "" ?>><?= $row["date_formatted"] ?></option>
                  <?php
                }
              ?>
            </select>
          </div>
          <div class="form-group col-md-4">
            <label class="d-none d-md-block">&nbsp;</label>
            <a class="d-none d-md-block btn btn-block btn-outline-secondary" href="<?= RELPATH ?>eintritt/">Anwesenheit</a>
            <a class="b-block d-md-none btn btn-outline-secondary" href="<?= RELPATH ?>eintritt/">Anwesenheit</a>
          </div>
        </div>
      </form>
      </div>
    </div>
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
        <th data-field="name" data-sortable="true" data-formatter="genderFormatter">Name</th>
        <th class="d-none d-md-table-cell" data-field="birthday" data-sortable="false">Geburtstag</th>
        <th data-field="age" data-sortable="true">Alter</th>
        <th class="d-none d-sm-table-cell" data-field="city" data-sortable="true">Ort</th>
        <th data-field="note" data-sortable="true">Notiz</th>
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