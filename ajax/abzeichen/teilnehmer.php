<?php
  require_once("../../config.php");
  set_relpath(2);
  $minify = False;

  restricted("Trainer");

  // TEILNEHMERDATEN
  $query = "SELECT p.id, p.name, DATE_FORMAT(p.birthday, '%d.%m.%Y') AS birthday_formatted, p.age, p.birthplace, p.`address`, p.post_code, p.city, p.note, u.display_name
            FROM participant AS p
            LEFT JOIN user AS u ON p.added_by_user_id = u.id";
            $result = mysqli_query($db, $query);

  $output = [];
  while($row = mysqli_fetch_array($result)) {
    $tmp = [];
    $tmp[] = "<a href='teilnehmer-bearbeiten.php?id=<?= " . $row["id"] . " ?>' class='btn btn-primary mdi mdi-account-edit-outline'></a>";
    $tmp[] = escape($row["name"] ?? "");
    $tmp[] = escape($row["birthday_formatted"] ?? "");
    $tmp[] = escape($row["age"] ?? "");
    $tmp[] = escape($row["city"] ?? "");
    $tmp[] = nl2br(escape($row["note"] ?? ""));
    $tmp[] = escape($row["display_name"]);

    $output[] = $tmp;
  }

  if($minify) {
    echo json_encode(["data" => $output]);
  }
  else {
    echo json_encode(["data" => $output], JSON_PRETTY_PRINT);
  }
?>