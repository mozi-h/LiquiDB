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
  var_dump($participant);
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
    <h1 class="text-info display-4 text-center mdi mdi-account-edit-outline"> Teilnehmer bearbeiten</h1>

    <?= catch_alert() ?>
    <div class="card">
      <form class="m-4" method="post" action="<?= RELPATH ?>abzeichen/teilnehmer-neu-senden.php">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="name">Name</label>
            <input type="text" class="form-control" name="name" id="name" required minlength=5 maxlength=50 placeholder="Max Muster (Pflichtfeld)" value="<?= $participant["name_esc"] ?>">
          </div>
          <div class="form-group col-md-3">
            <label for="birthday">Geburtsdatum</label>
            <input type="date" class="form-control" name="birthday" id="birthday" <?= get_fill_form("birthday") ?>>
          </div>
          <div class="form-group col-md-3">
            <label for="birthplace">Geburtsort</label>
            <input type="type" class="form-control" name="birthplace" id="birthplace" maxlength=50 placeholder="Samplecity" value="<?= $participant["birthplace_esc"] ?? "" ?>">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="address">Straße, Nr</label>
            <input type="text" class="form-control" name="address" id="address" minlength=5 maxlength=50 placeholder="Am Beispielweg 14" value="<?= $participant["address_esc"] ?? "" ?>">
          </div>
          <div class="form-group col-md-3">
            <label for="post_code">Postleitzahl</label>
            <input type="text" class="form-control" name="post_code" id="post_code" pattern="[0-9]{5}" title="Fünfstellige Ziffernfolge" maxlength=5 placeholder="32174" value="<?= $participant["post_code"] ?? "" ?>">
          </div>
          <div class="form-group col-md-3">
            <label for="city">Ort</label>
            <input type="text" class="form-control" name="city" id="city" maxlength=50 placeholder="Musterhausen" value="<?= $participant["city_esc"] ?? "" ?>">
          </div>
          <div class="form-group col">
            <label for="note">Notiz</label>
            <textarea class="form-control" name="note" id="note" rows="4" maxlength=500 placeholder="Eltern / Telefon"><?= $participant["note_esc"] ?? "" ?></textarea>
          </div>
        </div>
        <button type="submit" class="btn btn-success">Ändern</button>
      </form>
    </div>
  </div>
  
  <?= get_foot(True) ?>
  <script>
    // Detail-Funktion leitet zur Bearbeiten-Seite weiter
    function detailFormatter(index, row) {
      window.location.href = "<?= RELPATH ?>abzeichen/teilnehmer-bearbeiten.php?id=" + row["id"];
    }
  </script>
</body>
</html>