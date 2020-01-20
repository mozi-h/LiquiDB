<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Admin");

  // BENUTZERDATEN
  $query = "SELECT * FROM user;";
  $benutzerdaten_result = mysqli_query($db, $query);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?= get_head() ?>
  
  <title>Benutzer | LiquiDB</title>
</head>
<body>
  <?= get_nav("admin") ?>
  <div class="container">
    <h1 class="text-info display-4 text-center mdi mdi-account">Benutzer</h1>

    <?= catch_alert() ?>
    <table class="table table-striped text-center" id="datatable">
      <thead class="">
        <th></th>
        <th>Nutzername</th>
        <th>Name</th>
        <th class="hint" data-toggle="tooltip" data-placement="top" title="Trainer">T</th>
        <th class="hint" data-toggle="tooltip" data-placement="top" title="Admin">A</th>
      </thead>
      <tbody>
        <?php
          // BENUTZERDATEN AUSGEBEN

          while($row = mysqli_fetch_array($benutzerdaten_result)) {
            ?>
            <tr>
              <td><a href="nutzer-bearbeiten.php?id=<?= $row["id"] ?>" class="btn btn-primary mdi mdi-account-edit"></a></td>
              <td><?= escape($row["username"]) ?></td>
              <td><?= escape($row["name"]) ?? "<em>Leer</em>" ?></td>
              <td><span class="<?= ["mdi mdi-checkbox-blank-outline text-danger", "mdi mdi-checkbox-marked-outline text-success"][$row["ist_trainer"]] ?>"></span></td>
              <td><span class="<?= ["mdi mdi-checkbox-blank-outline text-danger", "mdi mdi-checkbox-marked-outline text-success"][$row["ist_admin"]] ?>"></span></td>
            </tr>
            <?php
          }
        ?>
      </tbody>
    </table>
  </div>
  
  <?= get_foot() ?>
  <script>
    // Tooltips aktivieren
    $(function(){
      $('[data-toggle="tooltip"]').tooltip();
    });

    // DataTable aktivieren
    $(document).ready(function() {
      $('#datatable').DataTable( {
        "language": {
          url: "<?= RELPATH ?>js/DataTables/german.json"
        },
        "order": [
          [1, "asc"]
        ],
        "columns": [
          {orderable: false},
          null,
          null,
          null,
          null
        ]
      });
    });
  </script>
</body>
</html>