<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Admin");
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?= get_head() ?>
  
  <title>Neuer Benutzer | LiquiDB</title>
</head>
<body>
  <?= get_nav("admin") ?>
  <div class="container">
    <h1 class="text-info display-4 text-center mdi mdi-account-plus"> Neuer Benutzer</h1>

    <?= catch_alert() ?>
    <div class="card">
      <!--div class="card-header mdi mdi-account-plus">
        Neuer Benutzer
      </div-->
      <div class="card-body">
        <form id="nutzer-neu" class="form-inline" method="post" action="<?= RELPATH ?>user/nutzer-neu-senden.php">
          <input type="text" class="form-control mb-2 mr-sm-2" required minlength=4 maxlength=32 id="username" name="username" placeholder="Nutzername">
          <input type="text" class="form-control mb-2 mr-sm-2" maxlength=50 id="name" name="name" placeholder="Anzeigename (optional)">
          <div class="input-group  mb-2 mr-sm-2">
            <input type="text" class="form-control" required minlength=8 maxlength=200 id="pw" name="pw" id="pw" placeholder="Initialpasswort">
            <div class="input-group-append">
              <button class="btn btn-info" type="button" data-toggle="tooltip" data-placement="top" title="Zufall" onclick="random_password()"><span class="mdi mdi-dice-multiple"></span></button>
            </div>
          </div>
          <button type="submit" class="btn btn-success mb-2">Hinzufügen</button>
        </form>
        <h4>Benutzerrollen</h4>
        <div class="custom-control custom-checkbox ml-sm-3">
          <input form="nutzer-neu" type="checkbox" class="custom-control-input" name="ist_trainer" id="ist_trainer">
          <label class="custom-control-label" for="ist_trainer">Trainer</label>
        </div>
        <div class="custom-control custom-checkbox ml-sm-3">
          <input form="nutzer-neu" type="checkbox" class="custom-control-input" name="ist_admin" id="ist_admin">
          <label class="custom-control-label" for="ist_admin">Admin</label>
        </div>
        <span class="text-muted">Der Benutzer wird aufgefordert, sein Passwort zu ändern.</span>
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