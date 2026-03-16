<?php

include("connect.php");

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="voting_results.csv"');

$output = fopen("php://output", "w");

fputcsv($output, ["Group Name","Votes"]);

$query = mysqli_query($connect,"SELECT name,votes FROM user WHERE role=2");

while($row = mysqli_fetch_assoc($query)){
    fputcsv($output, [$row['name'],$row['votes']]);
}

fclose($output);
?>