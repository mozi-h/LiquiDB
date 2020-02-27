<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Admin");
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?= get_head() ?>
  
  <title>Neue Gruppe | LiquiDB</title>
</head>
<body>
  <?= get_nav("admin") ?>
  <div class="container">
    <h1 class="text-info display-4 text-center">Neue Gruppe</h1>

    <?= catch_alert() ?>
    <div class="card">
      <!--div class="card-header mdi mdi-account-plus">
        Neue Gruppe
      </div-->
      <div class="card-body">
        <form class="" method="post" action="<?= RELPATH ?>admin/gruppe-neu-senden.php">
          <div class="form-row">
            <div class="form-group col">
              <label for="name">Name</label>
              <input type="text" class="form-control" name="name" id="name" required minlength=5 maxlength=50 <?= get_fill_form("name") ?>>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col">
              <label for="description">Beschreibung</label>
              <textarea class="form-control" name="description" id="description" rows="4" maxlength=500><?= get_fill_form("description", True) ?></textarea>
            </div>
          </div>
          <button type="submit" class="btn btn-success">Hinzufügen</button>
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