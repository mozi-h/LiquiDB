<?php
  require_once("../../config.php");
  set_relpath(2);
  $minify = False;

  restricted("Admin");

  $query = "SELECT user.id, user.username, user.name, user.ist_trainer, user.ist_admin
            FROM user";
  $result = mysqli_query($db, $query);

  $output = [];
  while($row = mysqli_fetch_array($result)) {
    $tmp = [];
    $tmp["id"] = $row["id"];
    $tmp["username"] = escape($row["username"]);
    $tmp["name"] = escape($row["name"]) ?? "";
    $tmp["ist_admin"] = "<span class='" . ["mdi mdi-checkbox-blank-outline text-danger", "mdi mdi-checkbox-marked-outline text-success"][$row["ist_admin"]] . "'></span>";
    $tmp["ist_trainer"] = " <span class='" . ["mdi mdi-checkbox-blank-outline text-danger", "mdi mdi-checkbox-marked-outline text-success"][$row["ist_trainer"]]. "'></span>";

    $output[] = $tmp;
  }

  if($minify) {
    echo json_encode($output);
  }
  else {
    echo json_encode($output, JSON_PRETTY_PRINT);
  }
?>