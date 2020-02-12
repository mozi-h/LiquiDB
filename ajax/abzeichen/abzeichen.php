<?php
  require_once("../../config.php");
  set_relpath(2);
  $minify = False;

  restricted("Trainer");

  // ABZEICHENDATEN
  $query = "SELECT b.id, p.name AS participant_name, p.gender, b_l.name_short, DATE_FORMAT(b.issue_date, '%d.%m.%Y') AS issue_date, b.status, b.issue_forced
            FROM (
              SELECT participant_id, badge_name_internal, MAX(issue_date) AS most_recent_issue_date
              FROM badge AS b
              GROUP BY participant_id, badge_name_internal
            ) AS filter_b
            INNER JOIN badge AS b ON filter_b.participant_id = b.participant_id
              AND filter_b.badge_name_internal = b.badge_name_internal
              AND filter_b.most_recent_issue_date = b.issue_date
            LEFT JOIN badge_list AS b_l ON b.badge_name_internal = b_l.name_internal
            LEFT JOIN participant AS p ON b.participant_id = p.id";
            $result = mysqli_query($db, $query);

  $output = [];
  $status_lookup = [
    "WIP" => "i.A.",
    "OK" => "OK",
    "OLD" => "Alt"
  ];
  while($row = mysqli_fetch_array($result)) {
    $tmp = [];
    $tmp["id"] = $row["id"];
    $tmp["participant_name"] = escape($row["participant_name"]) ?? "";
    $tmp["gender"] = $row["gender"];
    $tmp["badge_name"] = escape($row["name_short"]) ?? "";
    $tmp["status"] = $status_lookup[$row["status"]];
    $tmp["issue_date_formatted"] = escape($row["issue_date"]) ?? "";
    $tmp["issue_forced"] = $row["issue_forced"];

    $output[] = $tmp;
  }

  if($minify) {
    echo json_encode($output);
  }
  else {
    echo json_encode($output, JSON_PRETTY_PRINT);
  }
?>