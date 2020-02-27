<?php
  require_once("../config.php");
  set_relpath(1);

  /** Ziel für Alerts */
  $target = RELPATH . "resource/abzeichen-infos.php";

  // Ist Abzeichen ausgewählt?
  $_GET["a"] = trim($_GET["a"] ?? "");
  if(empty($_GET["a"])) {
    unset($_GET["a"]);
  }
  else {
    // Existiert das Abzeichen?
    $query = sprintf(
      "SELECT COUNT(1) FROM badge_list WHERE name_internal = '%s'",
      mysqli_real_escape_string($db, $_GET["a"])
    );
    if(mysqli_fetch_array(mysqli_query($db, $query))[0] != 1) {
      send_alert($target, "warning", "Abzeichen existiert nicht");
    }

    // Gültiges Abzeichen ausgewählt
    // Infos zum Abzeichen
    $query = sprintf(
      "SELECT `name`, name_short
      FROM badge_list
      WHERE name_internal = '%s'",
      $_GET["a"]
    );
    $badge_response = mysqli_query($db, $query);
    $badge = mysqli_fetch_array($badge_response);

    // Disziplin Liste mit derzeitigem Status
    $query = sprintf(
      "SELECT id, `type`, `name`, description
      FROM discipline_list
      WHERE badge_name_internal = '%s'
      ORDER BY `type`, `count`",
      $_GET["a"]
    );
    $disciplines = mysqli_query($db, $query);
  }
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?= get_head() ?>
  
  <title><?= $badge["name_short"] ?? "Abzeichen Info" ?> | LiquiDB</title>
</head>
<body>
  <?= get_nav("a-infos") ?>
  <div class="container">
    <h1 class="text-info display-4 text-center">Abzeichen Infos</h1>

    <?= catch_alert() ?>
    <div class="card mb-3">
      
      <div class="card-header">
        <select class="selectpicker" data-style="custom-select" data-live-search="true" name="badge" id="badge" title="Auswählen" onchange="window.location.replace('<?= RELPATH ?>resource/abzeichen-infos.php?a=' + this.value)">
          <?php get_badge_options($_GET["a"] ?? "") ?>
        </select>
      </div>
      <div class="card-body">
        <?php
          if(isset($_GET["a"])) {
            ?>
            <table class="table text-center table-hover bg-light">
              <thead class="thead-dark">
                <tr>
                  <th><?= $badge["name"] ?></th>
                </tr>
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
                      <td><?= $row["name"] ?></td>
                    </tr>
                    <?php
                  }
                ?>
              </tbody>
            </table>
            <div class="alert alert-info mdi mdi-information-outline mt-1" role="alert">
              Klicke auf eine Disziplin, um dessen Beschreibung anzuzeigen.
            </div>
            <?php
          }
          else {
            ?>
            Wähle ein Abzeichen aus, um dessen Disziplinen anzuzeigen
            <?php
          }
        ?>
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
  </script>
</body>
</html>