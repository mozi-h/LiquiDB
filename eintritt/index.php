<?php
  require_once("../config.php");
  set_relpath(1);

  restricted();

  // Alle Teilnehmer, die heute noch nicht als anwesend markiert sind
  $query = "SELECT p.id, p.name
            FROM participant AS p
            WHERE p.id NOT IN (
              SELECT p.id
              FROM participant AS p
              LEFT JOIN attendance AS a ON p.id = a.participant_id
              WHERE a.date = DATE(NOW())
            )";
  $participant_result = mysqli_query($db, $query);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?= get_head() ?>
  
  <title>Eintritt | LiquiDB</title>
</head>
<body>
  <?= get_nav("eintritt") ?>
  <div class="container">
    <h1 class="text-info display-4 text-center mdi mdi-account">Benutzer Dashboard</h1>

    <?= catch_alert() ?>
    <div class="card">
      
      <div class="mdi mdi-account-group-outline card-header">
        Teilnehmer anwesend
      </div>
      <div class="card-body align-content-between">
      <form class="m-4" method="post" action="<?= RELPATH ?>abzeichen/teilnehmer-neu-senden.php">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="participant">Teilnehmer</label>
            <select class="selectpicker form-control" data-style="custom-select" data-live-search="true" name="participant" id="participant" title="Auswählen...">
              <option value="">Platzhalter</option>
              <?php
                while($row = mysqli_fetch_array($participant_result)) {
                  ?>
                  <option value="<?= $row["id"] ?>"><?= $row["name"] ?></option>
                  <?php
                }
              ?>
            </select>
          </div>
          <div class="form-group col-md-6">
            <label class="mdi mdi-cash-multiple" for="paid">Bezahlt</label>
            <select class="selectpicker form-control" data-style="custom-select" name="paid" id="paid" required>
              <option class="mdi mdi-cash-multiple" value="Yes" selected>Bezahlt</option>
              <option class="mdi mdi-account-badge-outline" value="Other">Jahreskarte</option>
              <option class="mdi mdi-cash-multiple" value="No">Nicht Bezahlt / nötig</option>
            </select>
          </div>
        </div>
        <button type="submit" class="btn btn-success">Hinzufügen</button>
      </form>
      </div>
    </div>
  </div>
  
  <?= get_foot() ?>
</body>
</html>