<?php
date_default_timezone_set('Asia/Jakarta');
$this_date = date('d-m-Y');

$mysqli = new mysqli('localhost', 'root', 'faridkaka8', 'arduino');
$result = $mysqli->query("DELETE FROM sampah WHERE date!='$this_date'");

echo json_encode([
    "code" => 200,
    "message" => "Data Deleted"
]);
