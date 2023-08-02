<?php

$this_date = date('d-m-Y');

$mysqli = new mysqli('localhost', 'root', 'faridkaka8', 'arduino');
$result = $mysqli->query("SELECT * FROM sampah WHERE date='$this_date' ORDER BY time DESC");

echo date('d M Y') . ' - <b>' . $result->num_rows . ' Data</b><br>&nbsp;';

echo '<table border=1><tr><td><b>NO</b></td><td><b>TIME</b></td><td><b>CAPACITY</b></td></tr>';

$no = 0;
while($r = mysqli_fetch_array($result)){
        $no++;
        echo '<tr>';
        echo '<td>' . $no. '</td>';
        echo '<td>' . $r['time'] . '</td>';
        echo '<td>' . $r['capacity'] . '</td>';
        echo '</tr>';
}

echo '</table>';
