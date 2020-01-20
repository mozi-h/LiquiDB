<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Trainer");

  // Liste der Abzeichenarten
  $query = "SELECT name_internal, name_short
            FROM badge_list
            WHERE regulation = (SELECT * FROM regulation_current);";
  $badge_list_result = mysqli_query($db, $query);
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
            <label for="name">Teilnehmer</label>
            <input type="text" class="form-control" name="name" id="name" required minlength=5 maxlength=50 placeholder="Max Muster (Pflichtfeld)" <?= get_fill_form("name") ?>>
          </div>
          <div class="form-group col-md-3">
            <label for="badge">Abzeichen</label>
            <select class="selectpicker" data-style="custom-select" data-live-search="true" name="badge" id="badge" title="Auswählen">
              <?php
                // Alle Abzeichenarten ausgeben
                foreach($badge_list_result as $badge) {
                  ?>
                  <option value="<?= escape($badge["name_internal"]) ?>"><?= escape($badge["name_short"]) ?></option>
                  <?php
                }
              ?>
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