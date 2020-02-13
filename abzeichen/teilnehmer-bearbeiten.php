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

  // Anwesenheits-Statistik
  $query = sprintf(
    "SELECT
      (
        SELECT COUNT(1) 
        FROM attendance AS att
        WHERE att.participant_id = %d
      ) AS num_attended,
      (
        SELECT COUNT(1) 
        FROM attendance AS att
        WHERE att.participant_id = %d
        AND att.paid = 'No'
      ) AS num_not_paid
    ",
    $_GET["id"],
    $_GET["id"]);
  $att_stats_result = mysqli_query($db, $query);
  $att_stats = mysqli_fetch_array($att_stats_result);


?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?= get_head(True) ?>
  
  <title><?= $participant["name"] ?> | LiquiDB</title>
</head>
<body>
  <?= get_nav("abzeichen") ?>
  <div class="container">
    <h1 class="text-info display-4 text-center mdi mdi-account-edit-outline">Teilnehmer bearbeiten</h1>

    <?= catch_alert() ?>
    <?php if($att_stats["num_not_paid"] > 0) { ?>
      <div class="alert alert-danger alert-dismissible " role="alert">
        Teilnehmer hat an <?= $att_stats["num_not_paid"] ?> <?= get_quantity($att_stats["num_not_paid"], "Tag", "Tagen") ?> nicht bezahlt (<?= number_format($att_stats["num_not_paid"] * ENTRANCE_FEE, 2, ",", ".") ?> € Schulden)
        <!-- Trigger modal -->
        <button type="button" class="btn btn-sm btn-warning ml-1" data-toggle="modal" data-target="#clearDebt">
          Schulden begleichen
        </button>
      </div>
    <?php } ?>
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
    <div class="card mb-3">
      <div class="card-header mdi mdi-cards-outline">
        Abzeichen
      </div>
      <div class="card-body">
        <a class="btn btn-outline-success" data-toggle="collapse" href="#abzeichen-neu">
          Abzeichen anlegen<span class="mdi mdi-menu-down"></span>
        </a>
        <div class="collapse mt-2" id="abzeichen-neu">
          <form method="post" action="<?= RELPATH ?>abzeichen/abzeichen-neu-senden.php?id=<?= $_GET["id"] ?>">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="badge">Abzeichen</label>
                <select class="form-control selectpicker" data-style="custom-select" data-live-search="true" name="badge" id="badge" title="Auswählen" required>
                  <?php get_badge_options() ?>
                </select>
              </div>
              <div id="datepicker-badge-container" class="form-group col-md-6">
                <label for="issue-date">Ausstelldatum (setzen)</label>
                <div class="input-group date">
                  <input type="text" class="form-control" name="issue-date" title="Datum im TT.MM.JJJJ Format" maxlen=10 placeholder="TT.MM.JJJJ" oninput="show_force_info(this.value)">
                  <div class="input-group-append input-group-addon">
                    <button class="btn btn-secondary mdi mdi-calendar" type="button"></button>
                  </div>
                </div>
              </div>
            </div>
            <div class="alert alert-info alert-dismissible mdi mdi-card-bulleted d-none" role="alert" id="force-info">
              Das Abzeichen wird direkt als absolviert eingetragen. Deine ID wird hinterlegt.
            </div>
            <button type="submit" class="btn btn-success mdi mdi-card-plus-outline">Anlegen</button>
          </form>
        </div>
        <hr>
        <table id="data" class="table table-striped"
          data-toggle="table"
          data-url="<?= RELPATH ?>ajax/abzeichen/abzeichen-teilnehmer.php?id=<?= $_GET["id"] ?>"

          data-locale="de-DE"
          data-pagination="true"
          data-show-extended-pagination="true"
          data-show-fullscreen="true"
          data-show-refresh="true"
          data-search="true"
          data-detail-view="false"
          data-detail-view-by-click="true"
          data-detail-formatter="detailFormatter"
          data-row-style="rowStyle">
          <thead class="thead-dark">
            <th data-field="badge_name" data-sortable="true" data-formatter="badgeFormatter">Abzeichen</th>
            <th class="d-none d-lg-table-cell" data-field="issue_date_formatted" data-sortable="true">Ausstellungsdatum</th>
            <th class="d-none d-md-table-cell" data-field="status" data-sortable="true">Status</th>
          </thead>
        </table>
        <div class="alert alert-info mdi mdi-information-outline mt-1" role="alert">
          Kodierung: <span class="mdi mdi-card-outline" style="background-color:lightgray">In Arbeit</span>  <span class="mdi mdi-card-bulleted" style="background-color:lightgreen">Gültig</span>  <span class="mdi mdi-card-bulleted" style="background-color:lightblue">Gültig (gesetzt)</span>  <span class="mdi mdi-card-bulleted-off-outline" style="background-color:orange">Abgelaufen</span>
        </div>
        <div class="alert alert-info mdi mdi-pencil" role="alert">
          Klicke auf ein Abzeichen, um dieses zu bearbeiten
        </div>
      </div>
    </div>
    <div class="card mb-2">
      <a class="card-header mdi mdi-account-group-outline text-reset no-decoration" data-toggle="collapse" href="#anwesenheit">
        Anwesenheit<span class="mdi mdi-menu-down"></span>
      </a>
      <div class="card-body collapse" id="anwesenheit">
        <table id="data" class="table table-striped"
          data-toggle="table"
          data-url="<?= RELPATH ?>ajax/abzeichen/anwesenheitsliste_teilnehmer.php?id=<?= $_GET["id"] ?? "" ?>"

          data-locale="de-DE"
          data-pagination="true"
          data-show-extended-pagination="true"
          data-show-fullscreen="true"
          data-show-refresh="true"
          data-search="true"
          data-sort-name="group"
          data-sort-order="asc">
          <thead class="thead-dark">
            <th data-field="date_formatted" data-sortable="true">Datum</th>
            <th data-field="status" data-sortable="true">Status</th>
          </thead>
        </table>
      </div>
    </div>
  </div>

  <!-- Schulden-begleichen modal -->
  <div class="modal fade" id="clearDebt" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Schulden begleichen</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Dies markiert alle nicht bezahlten Termine des Teilnehmers als Bezahlt. Dies kann nicht rückgängig gemacht werden.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
          <a type="button" class="btn btn-warning" href="<?= RELPATH ?>abzeichen/schulden-begleichen-senden.php?id=<?= $_GET["id"] ?>">Fortfahren</a>
        </div>
      </div>
    </div>
  </div>
  
  <?= get_foot(True) ?>
  <script>
    <?php
      $timezone = new DateTimeZone(TIMEZONE);
      $current_date = new DateTime("now", $timezone);
    ?>
    // Abzeichen neu
    $('#datepicker-badge-container .input-group.date').datepicker({
      format: "dd.mm.yyyy",
      weekStart: 1,
      endDate: "<?= $current_date->format("d.m.Y") ?>",
      startView: 2,
      maxViewMode: 3,
      autoclose: true
    });
    // Teilnehmer
    $('#datepicker-container .input-group.date').datepicker({
      format: "dd.mm.yyyy",
      weekStart: 1,
      startDate: "<?= $current_date->sub(new DateInterval("P100Y"))->format("d.m.Y") ?>",
      endDate: "<?= $current_date->add(new DateInterval("P98Y"))->format("d.m.Y") ?>",
      startView: 2,
      maxViewMode: 3,
      autoclose: true
    });

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

    // Zeigt / versteckt den "Abzeichen wird bestätigt" Hinweis
    function show_force_info(value) {
      if(value) {
        $("#force-info").removeClass("d-none");
      }
      else {
        $("#force-info").addClass("d-none");
      }
    }
  </script>
</body>
</html>