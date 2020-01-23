<?php
  require_once("../../config.php");
  set_relpath(2);
  $minify = False;

  restricted("Trainer");

  // TEILNEHMERDATEN
  $query = "SELECT p.id, g.name AS group_name, p.name, p.gender, DATE_FORMAT(p.birthday, '%d.%m.%Y') AS birthday_formatted, p.age, p.birthplace, p.`address`, p.post_code, p.city, p.note
            FROM participant AS p
            LEFT JOIN `group` AS g ON p.group_id = g.id";
            $result = mysqli_query($db, $query);

  $output = [];
  while($row = mysqli_fetch_array($result)) {
    $tmp = [];
    $tmp["id"] = $row["id"];
    $tmp["group"] = escape($row["group_name"]) ?? "";
    $tmp["name"] = escape($row["name"]);
    $tmp["gender"] = $row["gender"];
    $tmp["birthday"] = escape($row["birthday_formatted"]) ?? "";
    $tmp["age"] = escape($row["age"]) ?? "";
    $tmp["city"] = escape($row["city"]) ?? "";
    $tmp["note"] = nl2br(escape($row["note"])) ?? "";

    $output[] = $tmp;
  }

  if($minify) {
    echo json_encode($output);
  }
  else {
    echo json_encode($output, JSON_PRETTY_PRINT);
  }
?>