<?php
  require_once("../config.php");
  set_relpath(1);

  restricted();

  // Alle Teilnehmer, die heute noch nicht als anwesend markiert sind
  $query = "SELECT * FROM attendance_today_not";
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
    <h1 class="text-info display-4 text-center mdi mdi-account">Helfer Dashboard</h1>

    <?= catch_alert() ?>
    <div class="card">
      <div class="mdi mdi-account-group-outline card-header">
        Teilnehmer anwesend
      </div>
      <div class="card-body align-content-between">
      <form method="post" action="<?= RELPATH ?>eintritt/anwesend-senden.php">
        <div class="form-row">
          <div class="form-group col-md-9">
            <label for="participant">Teilnehmer</label>
            <select class="selectpicker form-control" data-style="custom-select" data-live-search="true" name="participant[]" id="participant" title="Auswählen..." data-selected-text-format="count > 5" multiple required data-max-options="20">
              <?php
                while($row = mysqli_fetch_array($participant_result)) {
                  ?>
                  <option value="<?= $row["id"] ?>"><?= $row["name"] ?></option>
                  <?php
                }
              ?>
            </select>
          </div>
          <div class="form-group col-md-3">
            <label class="mdi mdi-cash-register" for="paid">Eintritt</label>
            <select class="selectpicker form-control" data-style="custom-select" name="paid" id="paid" required>
              <option class="mdi mdi-cash-multiple" value="Yes" selected>Bezahlt</option>
              <option class="mdi mdi-account-badge-outline" value="Other">Jahreskarte / Anders</option>
              <option class="mdi mdi-cash-remove" value="No">Nicht Bezahlt</option>
            </select>
          </div>
        </div>
        <button type="submit" class="btn btn-success">Anwesend</button>
        <p class="d-md-none mb-2"></p> <!--Platz zwischen den Knöfen auf small-->
        <a class="btn btn-outline-primary mdi mdi-account-details" href="<?= RELPATH ?>eintritt/anwesenheitsliste.php">Anwesenheitsliste</a>
      </form>
      </div>
    </div>
  </div>
  
  <?= get_foot() ?>
</body>
</html>