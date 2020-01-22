<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Trainer");

  /** Ziel für Alerts */
  $target = RELPATH . "abzeichen/teilnehmer.php";

  // Existiert der Nutzer?
  if(!filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    // id keine Nummer
    send_alert($target, "warning", "ID ist keine Nummer");
  }
  $_GET["id"] = intval($_GET["id"]);
  $query = sprintf(
    "SELECT 1 FROM participant WHERE id = %d",
    $_GET["id"]
  );
  $result = mysqli_query($db, $query);
  if(mysqli_num_rows($result) != 1) {
    // id kein Nutzer
    send_alert($target, "warning", "ID ist kein Teilnehmer");
  }
  $participant = get_participant($_GET["id"]);

  // Gruppen
  $query = "SELECT id, name FROM `group`";
  $group_result = mysqli_query($db, $query);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?= get_head(True) ?>
  
  <title>Teilnehmer bearbeiten | LiquiDB</title>
</head>
<body>
  <?= get_nav("abzeichen") ?>
  <div class="container">
    <h1 class="text-info display-4 text-center mdi mdi-account-edit-outline">Teilnehmer bearbeiten</h1>

    <?= catch_alert() ?>
    <div class="card mb-3">
      <form class="m-4" method="post" action="<?= RELPATH ?>abzeichen/teilnehmer-bearbeiten-senden.php?id=<?= $participant["id"] ?>">
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="name">Name</label>
            <input type="text" class="form-control" name="name" id="name" required minlength=5 maxlength=50 placeholder="Max Muster (Pflichtfeld)" value="<?= $participant["name_esc"] ?>">
          </div>
          <div class="form-group col-md-2 ">
            <label for="gender">Geschlecht</label>
            <select class="selectpicker form-control" data-style="custom-select" name="gender" id="gender" title="Auswählen...">
              <?php
                $form_gender = [$participant["gender"] => "selected"];
              ?>
              <option class="mdi mdi-gender-male" value="m" <?= $form_gender["m"] ?? "" ?>>Männlich</option>
              <option class="mdi mdi-gender-female" value="w" <?= $form_gender["w"] ?? "" ?>>Weiblich</option>
              <option class="mdi mdi-gender-non-binary" value="d" <?= $form_gender["d"] ?? "" ?>>Divers</option>
              <?php if(!empty($participant["gender"])) { ?>
                <option data-divider="true"></option>
                <option class="mdi mdi-trash-can-outline" value="remove">Entfernen</option>
              <?php } ?>
            </select>
          </div>
          <div id="datepicker-container" class="form-group col-md-3">
            <label for="birthday">Geburtsdatum</label>
            <div class="input-group date">
              <input type="text" class="form-control" name="birthday" title="Datum im TT.MM.JJJJ Format" maxlen=10 placeholder="TT.MM.JJJJ" value="<?= $participant["birthday_formatted"] ?>">
              <div class="input-group-append input-group-addon">
                <button class="btn btn-secondary mdi mdi-calendar" type="button"></button>
              </div>
            </div>
          </div>
          <div class="form-group col-md-3">
            <label for="birthplace">Geburtsort</label>
            <input type="type" class="form-control" name="birthplace" id="birthplace" maxlength=50 placeholder="Samplecity" value="<?= $participant["birthplace_esc"] ?>">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-2 ">
            <label for="group">Gruppe</label>
            <select class="selectpicker form-control" data-style="custom-select" name="group" id="group" title="Auswählen...">
              <?php
                while($row = mysqli_fetch_array($group_result)) {
                  $form_group = [$participant["group_id"] => "selected"];
                  ?>
                  <option value="<?= $row["id"] ?>" <?= $form_group[$row["id"]] ?? "" ?>><?= $row["name"] ?></option>
                  <?php 
                }
                if(!empty($participant["group_id"])) { ?>
                  <option data-divider="true"></option>
                  <option class="mdi mdi-trash-can-outline" value="remove">Entfernen</option>
                <?php }
              ?>
            </select>
          </div>
          <div class="form-group col-md-4">
            <label for="address">Straße, Nr</label>
            <input type="text" class="form-control" name="address" id="address" minlength=5 maxlength=50 placeholder="Am Beispielweg 14" value="<?= $participant["address_esc"] ?>">
          </div>
          <div class="form-group col-md-3">
            <label for="post_code">Postleitzahl</label>
            <input type="text" class="form-control" name="post_code" id="post_code" pattern="[0-9]{5}" title="Fünfstellige Ziffernfolge" maxlength=5 placeholder="32174" value="<?= $participant["post_code"] ?>">
          </div>
          <div class="form-group col-md-3">
            <label for="city">Ort</label>
            <input type="text" class="form-control" name="city" id="city" maxlength=50 placeholder="Musterhausen" value="<?= $participant["city_esc"] ?>">
          </div>
          <div class="form-group col">
            <label for="note">Notiz</label>
            <textarea class="form-control" name="note" id="note" rows="4" maxlength=500 placeholder="Eltern / Telefon"><?= get_fill_form("note", True) ?><?= $participant["note_esc"] ?></textarea>
          </div>
        </div>
        <button type="submit" class="btn btn-primary">Ändern</button>
      </form>
    </div>
    <div class="card">
      <div class="card-header">
      Abzeichen
      </div>
      <div class="card-body">
        Liste der Abzeichen in arbeit
      </div>
    </div>
  </div>
  
  <?= get_foot(True) ?>
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