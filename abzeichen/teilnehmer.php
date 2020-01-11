<?php
  require_once("../config.php");
  set_relpath(1);

  restricted("Trainer");

  // TEILNEHMERDATEN
  $query = "SELECT * FROM participant LEFT JOIN user ON participant.added_by_user_id = user.id";
  $teilnehmerdaten_result = mysqli_query($db, $query);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?= get_head() ?>
  
  <title>Teilnehmer | LiquiDB</title>
</head>
<body>
  <?= get_nav("abzeichen") ?>
  <div class="container">
    <h1 class="text-info display-4 text-center mdi mdi-account-outline"> Teilnehmer</h1>

    <?= catch_alert() ?>
    <table class="table table-striped text-center" id="datatable">
      <thead>
        <th></th>
        <th>Name</th>
        <th>Geburtstag</th>
        <th>Geburtsort</th>
        <th>Adresse</th>
        <th>PLZ</th>
        <th>Ort</th>
        <th>Notiz</th>
        <th>Eingepflegt von</th>
      </thead>
      <tbody>
        <?php
          // TEILNEHMERDATEN AUSGEBEN

          while($row = mysqli_fetch_array($teilnehmerdaten_result)) {
            ?>
            <tr>
              <td><a href="teilnehmer-bearbeiten.php?id=<?= $row["id"] ?>" class="btn btn-primary mdi mdi-account-edit-outline"></a></td>
              <td><?= escape($row["name"]) ?></td>
              <td><?= escape($row["birthday"]) ?? "<em>Leer</em>" ?></td>
              <td><?= escape($row["birthplace"]) ?? "<em>Leer</em>" ?></td>
              <td><?= escape($row["address"]) ?? "<em>Leer</em>" ?></td>
              <td><?= escape($row["post_code"]) ?? "<em>Leer</em>" ?></td>
              <td><?= escape($row["city"]) ?? "<em>Leer</em>" ?></td>
              <td><?= escape($row["note"]) ?? "<em>Leer</em>" ?></td>
              <td><?= escape($row["display_name"]) ?></td>
            </tr>
            <?php
          }
        ?>
      </tbody>
    </table>
  </div>
  
  <?= get_foot() ?>
  <script>
    // DataTable aktivieren
    $(document).ready(function() {
      $('#datatable').DataTable( {
        "language": {
          url: "<?= RELPATH ?>DataTables/german.json"
        },
        "order": [
          [1, "asc"]
        ],
        "columns": [
          {orderable: false},
          null,
          null,
          null,
          null,
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