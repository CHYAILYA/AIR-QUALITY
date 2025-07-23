<?php
require 'config.php';

$sql = "SELECT * FROM data_udara WHERE 1 ORDER BY id DESC";
$result = $db->query($sql);
if (!$result) {
  { echo "Error: " . $sql . "<br>" . $db->error; }
}

$row = $result->fetch_assoc();

echo json_encode($row);


?>