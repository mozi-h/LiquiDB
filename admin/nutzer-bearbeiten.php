<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Admin");

  /** Ziel für Alerts */
  $target = RELPATH . "admin/nutzer.php";

  // Existiert der Nutzer?
  if(!filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    // id keine Nummer
    send_alert($target, "warning", "ID ist keine Nummer");
  }
  $_GET["id"] = intval($_GET["id"]);
  $query = sprintf(
    "SELECT 1 FROM user WHERE id = %d",
    $_GET["id"]
  );
  $result = mysqli_query($db, $query);
  if(mysqli_num_rows($result) != 1) {
    // id kein Nutzer
    send_alert($target, "warning", "ID ist kein Nutzer");
  }
  $user = get_user($_GET["id"]);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?= get_head() ?>
  
  <title>Nutzer bearbeiten | LiquiDB</title>
</head>
<body>
  <?= get_nav("admin") ?>
  <div class="container">
    <h1 class="text-info display-4 text-center mdi mdi-account-edit">Nutzer bearbeiten</h1>

    <?= catch_alert() ?>
    <div class="card mb-3">
      <div class="card-header">
        Namen ändern
      </div>
      <div class="card-body">
        <form class="form-inline" method="post" action="<?= RELPATH ?>admin/nutzer-bearbeiten-namen-senden.php?id=<?= $user["id"] ?>">
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
        <form class="form-inline" method="post" action="<?= RELPATH ?>admin/nutzer-bearbeiten-pw-senden.php?id=<?= $user["id"] ?>">
          <input type="password" class="form-control mb-2 mr-sm-2" required minlength=8 maxlength=200 name="pw-admin" placeholder="Eigenes Passwort">
          <div class="input-group  mb-2 mr-sm-2">
            <input type="text" autocomplete="off" class="form-control" required minlength=8 maxlength=200 name="pw" id="pw" placeholder="Passwort setzen">
            <div class="input-group-append">
              <button class="btn btn-info" type="button" data-toggle="tooltip" data-placement="top" title="Zufall" onclick="random_password()"><span class="mdi mdi-dice-multiple"></span></button>
            </div>
          </div>
          <button type="submit" class="btn btn-primary mb-2" <?php if($user["id"] == 1) {echo "disabled";} ?>>Ändern</button>
        </form>
        <span class="text-muted">Der Benutzer wird abgemeldet und bei erneutem Anmelden aufgefordert, sein Passwort zu ändern.</span>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        Benutzerrollen
      </div>
      <div class="card-body">
        <form method="post" action="<?= RELPATH ?>admin/nutzer-bearbeiten-rollen-senden.php?id=<?= $user["id"] ?>">
          <?php if($user["id"] == 1) { ?>
            <div class="custom-control custom-checkbox ml-sm-3">
            <input type="checkbox" class="custom-control-input" readonly checked>
            <label class="custom-control-label">System</label>
            </div>
          <?php } else { ?>
            <div class="custom-control custom-checkbox ml-sm-3">
              <input type="checkbox" class="custom-control-input" name="ist_trainer" id="ist_trainer" <?= ["", "checked"][$user["ist_trainer"]] ?>>
              <label class="custom-control-label" for="ist_trainer">Trainer</label>
            </div>
            <div class="custom-control custom-checkbox ml-sm-3 mb-2">
              <input type="checkbox" class="custom-control-input" name="ist_admin" id="ist_admin" <?= ["", "checked"][$user["ist_admin"]] ?>>
              <label class="custom-control-label" for="ist_admin">Admin</label>
            </div>
            <button type="submit" class="btn btn-primary" <?php if($user["id"] == 1) {echo "disabled";} ?>>Ändern</button>
          <?php } ?>
        </form>
      </div>
    </div>
  </div>
  
  <?= get_foot() ?>
  <script>
    // Tooltips aktivieren
    $(function(){
      $('[data-toggle="tooltip"]').tooltip();
    });

    function random_password() {
      var rand_pw = Math.random().toString(36).substr(2, 12);
      $("#pw").val(rand_pw);
    }
  </script>
</body>
</html>