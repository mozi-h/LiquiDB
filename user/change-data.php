<?php
  $relative_offset = "../";
  require_once($relative_offset . "config.php");

  // Normalerweise restricted(); außnahme, da Sonderseite
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
    <h1 class="text-info display-4 text-center mdi mdi-pencil"> Daten ändern</h1>

    <?= catch_alert() ?>
    <div class="card mb-3">
      <div class="card-header">
        Namen ändern
      </div>
      <div class="card-body">
        <form class="form-inline" method="post" action="<?= $relative_offset ?>user/change-names-senden.php">
          <input type="text" class="form-control mb-2 mr-sm-2" required minlength=4 maxlength=32 name="username" placeholder="Nutzername" value="<?= $user["username_esc"] ?>">
          <input type="text" class="form-control mb-2 mr-sm-2" maxlength=50 name="name" placeholder="Anzeigename (optional)" value="<?= $user["name_esc"] ?? "" ?>">
          <button type="submit" class="btn btn-primary mb-2">Ändern</button>
        </form>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        Passwort ändern
      </div>
      <div class="card-body">
        <form class="form-inline" method="post" action="<?= $relative_offset ?>user/change-pw-senden.php">
          <input type="password" class="form-control mb-2 mr-sm-2" required minlength=8 maxlength=200 name="pw-alt" placeholder="Altes Passwort">
          <input type="password" class="form-control mb-2 mr-sm-2" required minlength=8 maxlength=200 name="pw-neu" placeholder="Neues Passwort">
          <input type="password" class="form-control mb-2 mr-sm-2" required minlength=8 maxlength=200 name="pw-neu-wdh" placeholder="Passwort wiederholen">
          <button type="submit" class="btn btn-primary mb-2">Ändern</button>
        </form>
      </div>
    </div>
    </div>
  
  <?= get_foot() ?>
</body>
</html>