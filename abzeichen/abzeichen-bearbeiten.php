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

  // INFORMATION HOLEN
  // Heutiger Tag (Formatiert)
  $timezone = new DateTimeZone(TIMEZONE);
  $current_date = new DateTime("now", $timezone);
  $current_date = $current_date->format("d.m.Y");

  // Liste der Disziplinen, die noch nicht eingeragen sind
  $query = sprintf(
    "SELECT id, `name`, `type`
    FROM discipline_list
    WHERE badge_name_internal = '%s'
    AND `id` NOT IN (
      SELECT `discipline_list_id`
      FROM discipline
      WHERE badge_id = %d
    )
    ORDER BY `type`, `count`",
    $badge["name_internal"],
    $_GET["id"]
  );
  $disciplines_list = mysqli_query($db, $query);

  // Disziplin Liste mit derzeitigem Status
  $query = sprintf(
    "SELECT *, DATE_FORMAT(`date`, '%%d.%%m.%%Y') AS date_formatted
    FROM discipline_list AS d_l
    LEFT JOIN (
      SELECT id AS discipline_id, discipline_list_id, `date`, `time`, TIME_FORMAT(`time`, '%%i:%%s') AS time_formatted
      FROM discipline
      WHERE badge_id = %d
    ) AS d ON d_l.id = d.discipline_list_id
    WHERE d_l.badge_name_internal = '%s'
    ORDER BY d_l.`type`, d_l.`count`",
    $_GET["id"],
    $badge["name_internal"]
  );
  $disciplines = mysqli_query($db, $query);
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
    <div class="card mb-3">
      <div class="card-header mdi mdi-clipboard-outline">
        Disziplinen
      </div>
      <div class="card-body">
        <form method="post" action="<?= RELPATH ?>abzeichen/disziplin-neu-senden.php?id=<?= $_GET["id"] ?>">
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="discipline">Disziplin</label>
              <select class="form-control selectpicker" data-style="custom-select" data-live-search="true" name="discipline" id="discipline" title="Auswählen" required>
                <?php
                  $optgroup_rows = [];
                  while($row = mysqli_fetch_array($disciplines_list)) {
                    if(!array_key_exists($row["type"], $optgroup_rows)) {
                      // Option-Abschnitts-Überschrift einfügen
                      $optgroup_rows[$row["type"]] = "";
                      ?>
                      <optgroup label="<?= $row["type"] ?>">
                      <?php
                    }
                    ?>
                    <option value="<?= $row["id"] ?>"><?= $row["name"] ?></option>
                    <?php
                  }
                ?>
              </select>
            </div>
            <div id="datepicker-container" class="form-group col-md-6">
              <label for="issue-date">Ausstelldatum</label>
              <div class="input-group date">
                <input type="text" class="form-control" name="issue-date" title="Datum im TT.MM.JJJJ Format" maxlen=10 placeholder="TT.MM.JJJJ" value="<?= $current_date ?>">
                <div class="input-group-append input-group-addon">
                  <button class="btn btn-secondary mdi mdi-calendar" type="button"></button>
                </div>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-success mdi mdi-clipboard-plus-outline">Absolviert</button>
        </form>
        <hr>
        <table class="table text-center">
            <thead class="thead-dark">
              <th></th>
              <th>Disziplin</th>
              <th>Absolviert</th>
              <th>Zusatz</th>
            </thead>
            <tbody>
              <?php
                $header_rows = $discipline_list_descriptions = [];
                while($row = mysqli_fetch_array($disciplines)) {
                  if(!array_key_exists($row["type"], $header_rows)) {
                    // Abschnitts-Überschrift einfügen
                    $header_rows[$row["type"]] = "";
                    ?>
                    <tr class="h5 bg-dark text-white text-monospace">
                      <td class="py-1" colspan=4><?= $row["type"] ?></td>
                    </tr>
                    <?php
                  }
                  // Beschreibung für JS-Block unten hinterlegen
                  $discipline_list_descriptions[$row["id"]] = [$row["name"], $row["description"]];
                  ?>
                  <tr onclick="show_discipline_list_detail(<?= $row['id'] ?>)">
                    <?php
                    if($row["discipline_id"]) {
                      // Ist die Disziplin gülig?
                      switch ($row["auto_type"]) {
                        case "NORMAL":
                          // Darf nicht älter als 3 Monate sein
                          break;
                        case "TIME":
                          // Darf nicht älter als 3 Monate sein
                          // UND time nicht größer als auto_info
                          break;
                        case "AGE":
                          break;
                        case "BADGE":
                          break;
                        case "DOCUMENT":
                          break;
                        default:
                          die("Kein bekannter auto_type");
                      }
                      // Bearbeiten / Löschen Knöpfe anzeigen
                      ?>
                      <td class="text-left"><a class="btn-sm btn-danger mdi mdi-trash-can" href="<?= RELPATH ?>abzeichen/disziplin-löschen-senden.php?id=<?= $row["discipline_id"] ?>"></a></td>
                      <?php
                    }
                    else {
                      ?>
                      <td></td>
                      <?php
                    }
                    ?>
                    <td><?= $row["name"] ?></td>
                    <td><?= $row["date_formatted"] ?? "" ?></td>
                    <td><?= $row["time_formatted"] ?? "" ?></td>
                  </tr>
                  <?php
                }
              ?>
            </tbody>
        </table>
        <div class="alert alert-info mdi mdi-information-outline mt-1" role="alert">
          Klicke auf eine Disziplin, um dessen Beschreibung anzuzeigen.
        </div>
      </div>
    </div>
  </div>

  <!-- Modal für Disziplin-Details (wechselt Inhalt) -->
  <div class="modal fade" id="discipline_detail" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="discipline_detail">Titel</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Beschreibung
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
        </div>
      </div>
    </div>
  </div>
  
  <?= get_foot() ?>
  <script>
    // Disziplin-Info: Setzt Modal-Text und ruft es auf.
    var discipline_list_descriptions = {
      <?php
        $out = "";
        foreach ($discipline_list_descriptions as $discipline_list_id => $discipline_list_info) {
          $title = escape($discipline_list_info[0]);
          $desc = escape($discipline_list_info[1]);
          $desc = nl2br($desc);
          $desc = str_replace(array("\n", "\r"), '', $desc);
          $out = $out . $discipline_list_id . ": ['" . $title . "', '" . $desc . "'],";
        }
        echo substr($out, 0, -1);
      ?>
    }
    function show_discipline_list_detail(discipline_list_id) {
      $("#discipline_detail .modal-title").html(discipline_list_descriptions[discipline_list_id][0])
      $("#discipline_detail .modal-body").html(discipline_list_descriptions[discipline_list_id][1])

      $("#discipline_detail").modal("show");
    }

    // Abzeichen neu
    $('#datepicker-container .input-group.date').datepicker({
      format: "dd.mm.yyyy",
      weekStart: 1,
      endDate: "<?= $current_date ?>",
      startView: 2,
      maxViewMode: 3,
      autoclose: true
    });
  </script>
</body>
</html>
