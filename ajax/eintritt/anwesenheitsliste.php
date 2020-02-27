<?php
  require_once("../../config.php");
  set_relpath(2);
  $minify = False;

  restricted();

  // Datum validieren
  // - nicht gegeben = letztes Datum
  // ODER
  // - echtes Datum
  // - Termin, zu dem Personen als anwesend markiert sind
  if(empty($_GET["d"] ?? "")) {
    // Nicht gegeben oder nur Leerzeichen
    unset($_GET["d"]);
    // Vom letzten eingetragenen Tag
    $query_where = "(SELECT `date` FROM attendance ORDER BY `date` DESC LIMIT 1)";
  }
  elseif(!preg_match("/^(0[1-9]|[12][0-9]|3[01])\.(0[1-9]|1[012])\.\d{4}$/", $_GET["d"])) {
    // kein TT.MM.JJJJ Datum
    die("Kein TT.MM.JJJJ Datum");
  }
  else {
    // Termin, zu dem Personen als anwesend markiert sind
    $_GET["d_unformatted"] = substr($_GET["d"], 6, 4) . "-" . substr($_GET["d"], 3, 2) . "-" . substr($_GET["d"], 0, 2);
    $query = sprintf(
      "SELECT 1 FROM attendance WHERE `date` = '%s' LIMIT 1",
      $_GET["d_unformatted"]
    );
    $result = mysqli_query($db, $query);
    if(mysqli_fetch_row($result)[0] != 1) {
      die("Zu dem Datum ist niemand als anwesend markiert");
    }
    // Vom angegebenen Tag
    $query_where = "'" . $_GET["d_unformatted"] . "'";
  }
  // Datum valid

  // TEILNEHMERDATEN
  $query = "SELECT p.id, g.name AS group_name, p.name, p.gender, DATE_FORMAT(p.birthday, '%d.%m.%Y') AS birthday_formatted, p.age, p.birthplace, p.`address`, p.post_code, p.city, p.note
            FROM participant AS p
            LEFT JOIN `group` AS g ON p.group_id = g.id
            LEFT JOIN attendance AS att ON p.id = att.participant_id
            WHERE att.date = $query_where
            ORDER BY g.name ASC";
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