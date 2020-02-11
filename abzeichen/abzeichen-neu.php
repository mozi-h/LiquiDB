<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Trainer");

  // Alle Teilnehmer
  $query = "SELECT p.id, p.name
            FROM participant AS p
            LEFT JOIN attendance AS att ON att.participant_id = p.id
            ORDER BY att.date DESC, p.name ASC";
  $participant_result = mysqli_query($db, $query);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?= get_head() ?>
  
  <title>Abzeichen anlegen | LiquiDB</title>
</head>
<body>
  <?= get_nav("abzeichen") ?>
  <div class="container">
    <h1 class="text-info display-4 text-center mdi mdi-card-plus-outline">Abzeichen anlegen</h1>

    <?= catch_alert() ?>
    <div class="card">
      <form class="m-4" method="post" action="<?= RELPATH ?>abzeichen/abzeichen-neu-senden.php">
        <div class="form-row">
          <div class="form-group col-md-6">
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
            <label for="badge">Abzeichen</label>
            <select class="selectpicker" data-style="custom-select" data-live-search="true" name="badge" id="badge" title="Auswählen">
              <?php require(RELPATH . "resource/abzeichen-options.html") ?>
            </select>
          </div>
          <div class="form-group col-md-3">
            <label for="birthplace">Geburtsort</label>
            <input type="type" class="form-control" name="birthplace" id="birthplace" maxlength=50 placeholder="Samplecity" <?= get_fill_form("birthplace") ?>>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="address">Straße, Nr</label>
            <input type="text" class="form-control" name="address" id="address" minlength=5 maxlength=50 placeholder="Am Beispielweg 14" <?= get_fill_form("address") ?>>
          </div>
          <div class="form-group col-md-3">
            <label for="post_code">Postleitzahl</label>
            <input type="text" class="form-control" name="post_code" id="post_code" pattern="[0-9]{5}" title="Fünfstellige Ziffernfolge" maxlength=5 placeholder="32174" <?= get_fill_form("post_code") ?>>
          </div>
          <div class="form-group col-md-3">
            <label for="city">Ort</label>
            <input type="text" class="form-control" name="city" id="city" maxlength=50 placeholder="Musterhausen" <?= get_fill_form("city") ?>>
          </div>
          <div class="form-group col">
            <label for="note">Notiz</label>
            <textarea class="form-control" name="note" id="note" rows="4" maxlength=500 placeholder="Eltern / Telefon"><?= get_fill_form("note", True) ?></textarea>
          </div>
        </div>
        <button type="submit" class="btn btn-success">Hinzufügen</button>
      </form>
    </div>
  </div>
  
  <?= get_foot() ?>
</body>
</html>