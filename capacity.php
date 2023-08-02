<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Content-type: application/json");

date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $this_date = date('d-m-Y');

    $mysqli = new mysqli('localhost', 'root', 'faridkaka8', 'arduino');
    $result = $mysqli->query("SELECT * FROM sampah WHERE date='$this_date' ORDER BY time DESC LIMIT 1")->fetch_assoc();

    if (isset($result)) {
        $date = $result['date'];
        $time = $result['time'];
        $capacity = $result['capacity'];

        echo json_encode([
            "code" => 200,
            "date" => $date,
            "time" => $time,
            "capacity" => $capacity
        ]);
    } else {
        echo json_encode([
            "code" => 404,
	    "date" => date('d-m-Y'),
            "time" => date('H:i:s'),
            "message" => "NO DATA"
        ]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mysqli = new mysqli('localhost', 'root', 'faridkaka8', 'arduino');

    parse_str(file_get_contents('php://input'), $p);

    $date = date('d-m-Y');
    $time = date('H:i:s');
    $capacity = $p['capacity'];

    $result = $mysqli->query("INSERT INTO sampah VALUES('$date','$time','$capacity')");

    echo json_encode([
        'message' => 'Data berhasil ditambahkan.',
        'capacity' => $capacity,
        'date' => $date,
        'time' => $time
    ]);
}
