<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Trainer");

  /** Ziel für Alerts */
  $target = RELPATH . "abzeichen/teilnehmer.php";

  // Existiert das Abzeichen?
  if(!filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    // id keine Nummer
    send_alert($target, "warning", "ID ist keine Nummer");
  }
  $_GET["id"] = intval($_GET["id"]);
  $query = sprintf(
    "SELECT 1 FROM badge WHERE id = %d",
    $_GET["id"]
  );
  $result = mysqli_query($db, $query);
  if(mysqli_num_rows($result) != 1) {
    // id kein Abzeichen
    send_alert($target, "warning", "ID ist kein Abzeichen");
  }
  $badge = get_badge($_GET["id"]);
  $participant = get_participant($badge["participant_id"]);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?= get_head() ?>
  
  <title>Abzeichen bearbeiten | LiquiDB</title>
</head>
<body>
  <?= get_nav("abzeichen") ?>
  <div class="container">
    <h1 class="text-info display-4 text-center mdi mdi-card-outline">Abzeichen bearbeiten</h1>
    <?= catch_alert() ?>
    <div class="card mb-3">
      <form class="m-4" method="post" action="<?= RELPATH ?>abzeichen/abzeichen-bearbeiten-senden.php?id=<?= $_GET["id"] ?>">
        <div class="form-row">
          <div class="form-group col-md-12">
            <?php
              $badge_styles = [
                "WIP" => "card-outline",
                "OK" => "card-bulleted",
                "OLD" => "card-bulleted-off-outline"
              ];
            ?>
            <label class="mdi mdi-<?= $badge_styles[$badge["status"]] ?>">Abzeichen</label>
            <input class="form-control" type="text" readonly value="<?= $badge["name_esc"] ?>">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-5">
            <label>Teilnehmer</label>
            <input class="form-control" type="text" readonly value="<?= $participant["name_esc"] ?>">
          </div>
          <div class="form-group col-md-2">
            <?php
              $status_lookup = [
                "WIP" => "i.A.",
                "OK" => "OK",
                "OLD" => "Alt"
              ];
              $status_colors = [
                "WIP" => "lightgray",
                "OK" => "lightgreen",
                "OLD" => "orange"
              ];
              $color = $status_colors[$badge["status"]];
              if($badge["issue_forced"] == "1" & $badge["status"] == "OK") {
                $color = "lightblue";
              }
            ?>
            <label>Status</label>
            <input class="form-control text-center" type="text" style="background-color:<?= $color ?>" readonly value="<?= $status_lookup[$badge["status"]] ?>">
          </div>
          <?php
            if($badge["status"] != "OLD" & ($badge["issue_forced"] ?? 1) == 1) {
              // Nicht ausgestellt oder gesetzt: Ausstelldatum veränderbar
              ?>
              <div id="datepicker-badge-container" class="form-group col-md-5">
                <label for="issue-date">Ausstelldatum (setzen)</label>
                <div class="input-group date">
                  <input type="text" class="form-control" name="issue-date" title="Datum im TT.MM.JJJJ Format" maxlen=10 placeholder="TT.MM.JJJJ" value="<?= $badge["issue_date_formatted"] ?>" oninput="show_force_info(this.value)">
                  <div class="input-group-append input-group-addon">
                    <button class="btn btn-secondary mdi mdi-calendar" type="button"></button>
                  </div>
                </div>
              </div>
              <?php
            }
            else {
              // Natürlich durchlaufen: Ausstelldatum unveränderbar
              ?>
              <div class="form-group col-md-5">
                <label>Ausstelldatum</label>
                <div class="input-group">
                  <input type="text" class="form-control" readonly value="<?= $badge["issue_date_formatted"] ?>">
                </div>
              </div>
              <?php
            }
          ?>
        </div>
        <div class="alert alert-info alert-dismissible mdi mdi-information-outline d-none" role="alert" id="force-info">
          <?php
            if($badge["issue_forced"] == "1") {
              echo "Das Abzeichen wird aktualisiert. Deine ID wird hinterlegt.";
            }
            else {
              echo "Das Abzeichen wird als absolviert gesetzt. Deine ID wird hinterlegt.";
            }
          ?>
        </div>
        <button type="submit" class="btn btn-primary">Ändern</button>
      </form>
    </div>
  </div>
  
  <?= get_foot() ?>
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
