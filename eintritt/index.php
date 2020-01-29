<?php
  require_once("../config.php");
  set_relpath(1);

  restricted();

  // Alle Teilnehmer, die heute noch nicht als anwesend markiert sind
  $query = "SELECT * FROM attendance_today_not";
  $participant_result = mysqli_query($db, $query);

  // test
  $query = "SELECT
    (
      SELECT COUNT(1)
      FROM attendance
      WHERE `date` = (SELECT `date` FROM attendance ORDER BY `date` DESC LIMIT 1)
    ) AS attending,
    (
      SELECT COUNT(1)
      FROM attendance
      WHERE `date` = (SELECT `date` FROM attendance ORDER BY `date` DESC LIMIT 1)
      AND paid = 'Other'
    ) AS other,
    (
      SELECT COUNT(1)
      FROM attendance
      WHERE `date` = (SELECT `date` FROM attendance ORDER BY `date` DESC LIMIT 1)
      AND paid = 'No'
    ) AS not_paid";
    $attendance_stats = mysqli_fetch_array(mysqli_query($db, $query));
    $attendance_stats["paid"] = $attendance_stats["attending"] - $attendance_stats["other"] - $attendance_stats["not_paid"];
    $attendance_stats["must_pay"] = $attendance_stats["attending"] - $attendance_stats["other"];
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
    <div class="card mb-3">
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
    <div class="card">
      <div class="mdi mdi-cash-register card-header">
        Abrechnen
      </div>
      <div class="card-body align-content-between">
      <table id="data" class="table table-striped">
        <thead class="thead-dark text-center">
          <th></th>
          <th>Anzahl</th>
          <th>Wert</th>
        </thead>
        <tbody>
          <tr>
            <td class="mdi mdi-account-group-outline"> Anwesend</td>
            <td class="text-center"><?= $attendance_stats["attending"] ?? "-" ?></td>
            <td></td>
          </tr>
          <tr>
            <td class="mdi mdi-cash-multiple"> Zahlungspflichtig</td>
            <td class="text-center"><?= $attendance_stats["must_pay"] ?? "-" ?></td>
            <td class="text-center"><?= number_format(($attendance_stats["must_pay"] * ENTRANCE_FEE) ?? 0, 2, ",", ".") ?> €</td>
          </tr>
          <tr>
            <td class="mdi mdi-cash-remove"> Ausstehende Zahlungen</td>
            <td class="text-center"><?= $attendance_stats["not_paid"] ?? "-" ?></td>
            <td class="text-center"><?= number_format(($attendance_stats["not_paid"] * ENTRANCE_FEE) ?? 0, 2, ",", ".") ?> €</td>
          </tr>
          <tr>
            <td class="mdi mdi-account-badge-outline"> Jahreskarte / Anders</td>
            <td class="text-center"><?= $attendance_stats["other"] ?? "-" ?></td>
            <td></td>
          </tr>
        </tbody>
      </table>
      </div>
    </div>
  </div>
  
  <?= get_foot() ?>
</body>
</html>