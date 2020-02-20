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
  $current_date_formatted = $current_date->format("d.m.Y");

  // Liste der Disziplinen, die noch nicht eingeragen sind
  $query = sprintf(
    "SELECT id, `name`, `type`, auto_type
    FROM discipline_list
    WHERE auto_type != 'AGE'
    AND auto_type != 'BADGE'
    AND badge_name_internal = '%s'
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
      SELECT id AS discipline_id, discipline_list_id, `date`, `time`, CONCAT(FLOOR(`time`/60), ':', MOD(`time`, 60)) AS time_formatted
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
      <form class="m-4">
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
          <div class="form-group col-md-5">
            <label>Ausstelldatum</label>
            <div class="input-group">
              <input type="text" class="form-control" readonly value="<?= $badge["issue_date_formatted"] ?? "" ?>">
            </div>
          </div>
        </div>
      </form>
    </div>
    <?php
      if($badge["status"] == "WIP") {
        ?>
        <div class="card mb-3">
          <div class="card-header mdi mdi-clipboard-outline" id="disziplinen">
            Disziplinen
          </div>
          <div class="card-body">
            <form method="post" action="<?= RELPATH ?>abzeichen/disziplin-neu-senden.php?id=<?= $_GET["id"] ?>">
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="discipline">Disziplin</label>
                  <select class="form-control selectpicker" data-style="custom-select" data-live-search="true" name="discipline" id="discipline" title="Auswählen" onchange="disable_time(this)" required>
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
                        <option <?php if ($row["auto_type"] == "TIME") echo "class='mdi mdi-timer'"; ?> value="<?= $row["id"] ?>" data-allow-time=<?= $row["auto_type"] == "TIME" ?>><?= $row["name"] ?></option>
                        <?php
                      }
                    ?>
                  </select>
                </div>
                <div id="datepicker-container" class="form-group col-md-4">
                  <label for="issue-date">(Ausstell-) Datum</label>
                  <div class="input-group date">
                    <input type="text" class="form-control" name="issue-date" title="Datum im TT.MM.JJJJ Format" maxlen=10 placeholder="TT.MM.JJJJ" required value="<?= $current_date_formatted ?>">
                    <div class="input-group-append input-group-addon">
                      <button class="btn btn-secondary mdi mdi-calendar" type="button"></button>
                    </div>
                  </div>
                </div>
                <div class="form-group col-md-2">
                  <label for="issue-date">Zeit</label>
                  <input type="time" class="form-control" name="time" id="time_form" disabled>
                </div>
              </div>
              <button type="submit" class="btn btn-success mdi mdi-clipboard-plus-outline">Absolviert</button>
            </form>
            <hr>
            <table class="table text-center table-hover bg-light">
                <thead class="thead-dark">
                  <tr>
                    <th></th>
                    <th>Disziplin</th>
                    <th>Absolviert</th>
                    <th>Zusatz</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $hinder_issue = False;
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
                        $bg_color = "";
                        // Ist die Disziplin gülig?
                        switch ($row["auto_type"]) {
                          case "NORMAL":
                            // Darf nicht älter als 3 Monate sein
                          case "TIME":
                            // Darf nicht älter als 3 Monate sein
                            // UND time nicht größer als auto_info (hier nicht überprüft; da eintragen nicht möglich)
                            // (deswegen teilen sich NORMAl und TIME einen Case-Body)
                            if($row["date"] == NULL) {
                              $bg_color = "bg-warning";
                              $hinder_issue = True;
                              break;
                            }
                            $discipline_date = new DateTime($row["date"], $timezone);
                            $discipline_date->add(new DateInterval("P1D"));
                            $max_discipline_date = new DateTime("now", $timezone);;
                            $max_discipline_date->sub(new DateInterval("P3M"));
                            if($discipline_date <= $max_discipline_date) {
                              $bg_color = "bg-warning";
                            };
                            break;
                          case "AGE":
                            // Teilnehmer muss mindestend auto_info Jahre alt sein
                            // Blockiert Ausstellung
                            if($participant["age"] < $row["auto_info"]) {
                              $bg_color = "bg-danger";
                              $hinder_issue = True;
                            }
                            break;
                          case "BADGE":
                            // Teilnehmer muss ein gültiges auto_info Abzeichen haben
                            // Blockiert Ausstellung
                            $query = sprintf(
                              "SELECT issue_date FROM badge WHERE participant_id = %d AND badge_name_internal = '%s' AND `status` = 'OK'",
                              $participant["id"],
                              $row["auto_info"]
                            );
                            $result = mysqli_query($db, $query);
                            if(mysqli_num_rows($result) != 1) {
                              // Kein benötigtes Abzeichen vorhanden
                              $bg_color = "bg-danger";
                              $hinder_issue = True;
                            }
                            else {
                              $badge_date = mysqli_fetch_array($result)["issue_date"];
                              $row["date_formatted"] = substr($badge_date, 8, 2) . "." . substr($badge_date, 5, 2) . "." . substr($badge_date, 0, 4);
                            }
                            break;
                          case "DOCUMENT":
                            // Das Dokument darf maximal auto_info Jahre in der Vergangenheit ausgestellt worden sein
                            if($row["date"] == NULL) {
                              $bg_color = "bg-warning";
                              $hinder_issue = True;
                              break;
                            }
                            $document_date = new DateTime($row["date"], $timezone);
                            $document_date->add(new DateInterval("P1D"));
                            $max_document_date = new DateTime("now", $timezone);;
                            $max_document_date->sub(new DateInterval("P" . $row["auto_info"] . "Y"));
                            if($document_date <= $max_document_date) {
                              $bg_color = "bg-warning";
                              $hinder_issue = True;
                            };
                            break;
                          default:
                            die("Kein bekannter auto_type");
                        }

                        if($row["discipline_id"]) {
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
                        <td class="<?= $bg_color ?>"><?= $row["date_formatted"] ?? "" ?></td>
                        <td><?= $row["time_formatted"] ?? "" ?><?php if($row["auto_type"]=="AGE") echo $participant["age"] ?></td>
                      </tr>
                      <?php
                    }
                  ?>
                </tbody>
            </table>
            <button class="btn btn-block btn-success" <?php if($hinder_issue) echo "disabled" ?> data-toggle="modal" data-target="#issue_modal">Abzeichen Ausstellen</button>
            <?php
              if($hinder_issue) {
                ?>
                <button type="button" class="btn btn-block btn-info" data-toggle="modal" data-target="#force_issue">Setzen</button>
                <?php
              }
            ?>
            <div class="alert alert-info mdi mdi-information-outline mt-1" role="alert">
              Klicke auf eine Disziplin, um dessen Beschreibung anzuzeigen.
            </div>
          </div>
        </div>
        <?php
      }
      else {
        ?>
        <div class="card mb-3">
          <div class="card-header mdi mdi-clipboard-outline" id="disziplinen">
            Disziplinen
          </div>
          <div class="card-body">
            <table class="table text-center table-hover bg-light">
                <thead class="thead-dark">
                  <tr>
                    <th>Disziplin</th>
                    <th>Absolviert</th>
                    <th>Zusatz</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $hinder_issue = False;
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

                      // Hinweis bei den disziplinen anzeigen, wenn Abzeichen gesetzt
                      if($badge["issue_forced"]) {
                        $row["date_formatted"] = "<em>gesetzt</em>";
                      }
                      ?>
                      <tr onclick="show_discipline_list_detail(<?= $row['id'] ?>)">
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
        <?php
      }
    ?>
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
  <!-- Modal für das Ausstellen -->
  <div class="modal fade" id="issue_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="issue_modal">Abzeichen ausstellen</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="<?= RELPATH ?>abzeichen/abzeichen-ausstellen-senden.php?id=<?= $_GET["id"] ?>" id="modal_issue_form" method="post">
              <div id="datepicker-container" class="form-group">
                <label for="issue-date">Ausstelldatum</label>
                <div class="input-group date">
                  <input type="text" class="form-control" name="issue-date" title="Datum im TT.MM.JJJJ Format" maxlen=10 placeholder="TT.MM.JJJJ" required value="<?= $current_date_formatted ?>">
                  <div class="input-group-append input-group-addon">
                    <button class="btn btn-secondary mdi mdi-calendar" type="button"></button>
                  </div>
                </div>
              </div>
          </form>
          Das Abzeichen wird ausgestellt. Deine ID wird hinterlegt.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
          <button form="modal_issue_form" type="submit" class="btn btn-success">Ausstellen</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal für das Setzen -->
  <div class="modal fade" id="force_issue" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="force_issue">Abzeichen setzen</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="<?= RELPATH ?>abzeichen/abzeichen-setzen-senden.php?id=<?= $_GET["id"] ?>" id="modal_force_issue_form" method="post">
              <div id="datepicker-container" class="form-group">
                <label for="issue-date">Ausstelldatum (setzen)</label>
                <div class="input-group date">
                  <input type="text" class="form-control" name="issue-date" title="Datum im TT.MM.JJJJ Format" maxlen=10 placeholder="TT.MM.JJJJ" required value="<?= $current_date_formatted ?>">
                  <div class="input-group-append input-group-addon">
                    <button class="btn btn-secondary mdi mdi-calendar" type="button"></button>
                  </div>
                </div>
              </div>
          </form>
          Das Abzeichen wird als absolviert gesetzt und die Warnungen oben übersprungen. Bisherig hinterlegte Daten werden gelöscht. Deine ID wird hinterlegt.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
          <button form="modal_force_issue_form" type="submit" class="btn btn-info">Setzen</button>
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

    // Datepicker
    $('#datepicker-container .input-group.date').datepicker({
      format: "dd.mm.yyyy",
      weekStart: 1,
      endDate: "<?= $current_date_formatted ?>",
      startView: 2,
      maxViewMode: 3,
      autoclose: true
    });

    // Erlaubt bei data-allow-time == 1 das Zeit-Feld beim Ablegen von disziplinen
    function disable_time(object) {
      var allow_time = object.selectedOptions[0].dataset["allowTime"] == "1";
      console.log(allow_time);
      if(!allow_time) {
        $("#time_form").val("");
      }
      $("#time_form").prop("disabled", !allow_time);
    }
  </script>
</body>
</html>
