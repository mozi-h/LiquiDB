<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Admin");

  /** Ziel für Alerts */
  $target = RELPATH . "admin/gruppe.php";

  // Existiert die Gruppe?
  if(!filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    // id keine Nummer
    send_alert($target, "warning", "ID ist keine Nummer");
  }
  $_GET["id"] = intval($_GET["id"]);
  $query = sprintf(
    "SELECT 1 FROM `group` WHERE id = %d",
    $_GET["id"]
  );
  $result = mysqli_query($db, $query);
  if(mysqli_num_rows($result) != 1) {
    // id keine Gruppe
    send_alert($target, "warning", "ID ist keine Gruppe");
  }
  $group = get_group($_GET["id"]);

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
  
  <title>Gruppe bearbeiten | LiquiDB</title>
</head>
<body>
  <?= get_nav("admin") ?>
  <div class="container">
    <h1 class="text-info display-4 text-center mdi mdi-account-edit-outline">Gruppe bearbeiten</h1>

    <?= catch_alert() ?>
    <div class="card">
      <form class="m-4" method="post" action="<?= RELPATH ?>admin/gruppe-bearbeiten-senden.php?id=<?= $group["id"] ?>">
        <div class="form-row">
          <div class="form-group col">
            <label for="name">Name</label>
            <input type="text" class="form-control" name="name" id="name" required minlength=5 maxlength=50 value="<?= $group["name_esc"] ?>">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col">
            <label for="description">Beschreibung</label>
            <textarea class="form-control" name="description" id="description" rows="4" maxlength=500><?= $group["description_esc"] ?></textarea>
          </div>
        </div>
        <button type="submit" class="btn btn-success">Ändern</button>
      </form>
    </div>
  </div>
  <?= get_foot(True) ?>
</body>
</html>