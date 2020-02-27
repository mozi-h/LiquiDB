<?php
  require_once("../../config.php");
  set_relpath(2);
  $minify = False;

  restricted("Admin");

  // TEILNEHMERDATEN
  $query = "SELECT id, name, description
            FROM `group`";
            $result = mysqli_query($db, $query);

  $output = [];
  while($row = mysqli_fetch_array($result)) {
    $tmp = [];
    $tmp["id"] = $row["id"];
    $tmp["name"] = escape($row["name"]) ?? "";
    $tmp["description"] = nl2br(escape($row["description"])) ?? "";

    $output[] = $tmp;
  }

  if($minify) {
    echo json_encode($output);
  }
  else {
    echo json_encode($output, JSON_PRETTY_PRINT);
  }
?>