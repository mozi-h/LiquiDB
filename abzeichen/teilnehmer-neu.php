<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Trainer");

  // Gruppen
  $query = "SELECT id, name FROM `group`";
  $group_result = mysqli_query($db, $query);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?= get_head() ?>
  
  <title>Neuer Teilnehmer | LiquiDB</title>
</head>
<body>
  <?= get_nav("abzeichen") ?>
  <div class="container">
    <h1 class="text-info display-4 text-center mdi mdi-account-plus">Neuer Teilnehmer</h1>

    <?= catch_alert() ?>
    <div class="card">
      <form class="m-4" method="post" action="<?= RELPATH ?>abzeichen/teilnehmer-neu-senden.php">
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="name">Name</label>
            <input type="text" class="form-control" name="name" id="name" required minlength=5 maxlength=50 placeholder="Max Muster (Pflichtfeld)" <?= get_fill_form("name") ?>>
          </div>
          <div class="form-group col-md-2 ">
            <label for="gender">Geschlecht</label>
            <select class="selectpicker form-control" data-style="custom-select" name="gender" id="gender" title="Auswählen...">
              <?php
                $fill_form_gender = get_fill_form("gender", True);
                $fill_form_gender = [$fill_form_gender => "selected"];
              ?>
              <option class="mdi mdi-gender-male" value="m" <?= $fill_form_gender["m"] ?? "" ?>>Männlich</option>
              <option class="mdi mdi-gender-female" value="w" <?= $fill_form_gender["w"] ?? "" ?>>Weiblich</option>
              <option class="mdi mdi-gender-non-binary" value="d" <?= $fill_form_gender["d"] ?? "" ?>>Divers</option>
            </select>
          </div>
          <div id="datepicker-container" class="form-group col-md-3">
            <label for="birthday">Geburtsdatum</label>
            <div class="input-group date">
              <input type="text" class="form-control" name="birthday" title="Datum im TT.MM.JJJJ Format" maxlen=10 placeholder="TT.MM.JJJJ" <?= get_fill_form("birthday") ?>>
              <div class="input-group-append input-group-addon">
                <button class="btn btn-secondary mdi mdi-calendar" type="button"></button>
              </div>
            </div>
          </div>
          <div class="form-group col-md-3">
            <label for="birthplace">Geburtsort</label>
            <input type="type" class="form-control" name="birthplace" id="birthplace" maxlength=50 placeholder="Samplecity" <?= get_fill_form("birthplace") ?>>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-2 ">
            <label for="group">Gruppe</label>
            <select class="selectpicker form-control" data-style="custom-select" name="group" id="group" title="Auswählen...">
              <?php
                while($row = mysqli_fetch_array($group_result)) {
                  ?>
                  <option value="<?= $row["id"] ?>"><?= $row["name"] ?></option>
                  <?php
                }
              ?>
            </select>
          </div>
          <div class="form-group col-md-4">
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
  <script>
    <?php
      $timezone = new DateTimeZone(TIMEZONE);
      $current_date = new DateTime("now", $timezone);
    ?>
    $('#datepicker-container .input-group.date').datepicker({
      format: "dd.mm.yyyy",
      weekStart: 1,
      startDate: "<?= $current_date->sub(new DateInterval("P100Y"))->format("d.m.Y") ?>",
      endDate: "<?= $current_date->add(new DateInterval("P98Y"))->format("d.m.Y") ?>",
      startView: 2,
      maxViewMode: 3,
      autoclose: true
    });
  </script>
</body>
</html>