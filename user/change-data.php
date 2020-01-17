<?php
  require_once("../config.php");
  set_relpath(1);

  // Normalerweise restricted(); Ausnahme, da Sonderseite
  if(!isset($_SESSION["USER"])) {
    send_alert("../index.php", "info", "Sie müssen sich zuerst anmelden");
  }
  $user = get_user($_SESSION["USER"]);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?= get_head() ?>
  
  <title>Daten ändern | LiquiDB</title>
</head>
<body>
  <?= get_nav("nutzer") ?>
  <div class="container">
    <h1 class="text-info display-4 text-center mdi mdi-pencil">Daten ändern</h1>

    <?= catch_alert() ?>
    <div class="card mb-3">
      <div class="card-header">
        Namen ändern
      </div>
      <div class="card-body">
        <form class="form-inline" method="post" action="<?= RELPATH ?>user/change-names-senden.php">
          <input type="text" class="form-control mb-2 mr-sm-2" required minlength=4 maxlength=32 name="username" placeholder="Nutzername" value="<?= $user["username_esc"] ?>">
          <input type="text" class="form-control mb-2 mr-sm-2" maxlength=50 name="name" placeholder="Anzeigename (optional)" value="<?= $user["name_esc"] ?? "" ?>">
          <button type="submit" class="btn btn-primary mb-2" <?php if($user["id"] == 1) {echo "disabled";} ?>>Ändern</button>
        </form>
      </div>
    </div>
    <div class="card mb-3">
      <div class="card-header">
        Passwort ändern
      </div>
      <div class="card-body">
        <form class="form-inline" method="post" action="<?= RELPATH ?>user/change-pw-senden.php">
          <input type="password" class="form-control mb-2 mr-sm-2" required minlength=8 maxlength=200 name="pw-alt" placeholder="Altes Passwort">
          <input type="password" class="form-control mb-2 mr-sm-2" required minlength=8 maxlength=200 name="pw-neu" placeholder="Neues Passwort">
          <input type="password" class="form-control mb-2 mr-sm-2" required minlength=8 maxlength=200 name="pw-neu-wdh" placeholder="Passwort wiederholen">
          <button type="submit" class="btn btn-primary mb-2"  <?php if($user["id"] == 1) {echo "disabled";} ?>>Ändern</button>
        </form>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        Benutzerrollen
      </div>
      <div class="card-body">
        <?php if($user["id"] == 1) { ?>
          <div class="custom-control custom-checkbox ml-sm-3">
          <input type="checkbox" class="custom-control-input" readonly checked>
          <label class="custom-control-label">System</label>
          </div>
        <?php } else { ?>
          <div class="custom-control custom-checkbox ml-sm-3">
            <input type="checkbox" class="custom-control-input" readonly <?= ["", "checked"][$user["ist_trainer"]] ?>>
            <label class="custom-control-label">Trainer</label>
          </div>
          <div class="custom-control custom-checkbox ml-sm-3">
            <input type="checkbox" class="custom-control-input" readonly <?= ["", "checked"][$user["ist_admin"]] ?>>
            <label class="custom-control-label">Admin</label>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
  
  <?= get_foot() ?>
</body>
</html>