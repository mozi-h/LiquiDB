<?php
  require_once("config.php");
  set_relpath(0);

  if(isset($_SESSION["USER"])) {
    $user = get_user($_SESSION["USER"]);
  }
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?= get_head() ?>
  
  <title>LiquiDB</title>
</head>
<body>
  <?= get_nav("index") ?>
  <div class="container">
    <h1 class="text-info display-4 text-center">Home</h1>

    <?= catch_alert() ?>
    <?php if(isset($_SESSION["USER"])) if($user["pw_must_change"]) { ?>
      <div class="alert alert-info mdi mdi-shield-lock" role="alert">
        Um Ihr Konto benutzen zu können, <a href="user/change-data.php">ändern Sie Ihr Passwort</a>
      </div>
    <?php } ?>
    <?php if(!isset($_SESSION["USER"])) { ?>
      <div class="card">
        <div class="card-header">
          Anmelden
        </div>
        <div class="card-body">
          Um auf LiquiDB zuzugreifen, melden Sie sich bitte an.
          <form class="form-inline mt-3" method="post" action="user/login.php">
            <input type="text" class="form-control mb-2 mr-sm-2" required minlength=4 maxlength=32 name="username" placeholder="Nutzername">
            <input type="password" class="form-control mb-2 mr-sm-2" required minlength=8 maxlength=200 name="pw" placeholder="Passwort">
            <button type="submit" class="btn btn-primary mb-2 mdi mdi-login">Anmelden</button>
          </form>
          <span class="text-muted">Accounts können nur von Administratoren erstellt werden.</span>
        </div>
      </div>
    <?php } else {
      ?>
      <div class="card">
        <div class="card-header">
          Angemeldet
        </div>
        <div class="card-body">
          Hallo, <?= $user["display_name_esc"] ?>
        </div>
      </div>
    <?php } ?>
  </div>
  <div class="fixed-bottom text-right">
    WATERMARK (WORK IN PROGRESS - WIP)
  </div>
  
  <?= get_foot() ?>
</body>
</html>