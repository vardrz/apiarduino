<?php
date_default_timezone_set('Asia/Jakarta');

//ini wajib dipanggil paling atas
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//ini sesuaikan foldernya ke file 3 ini
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $this_date = date('d-m-Y');
    // $this_date = '19-06-2023';

    $mysqli = new mysqli('localhost', 'root', 'faridkaka8', 'esp');
    $result = $mysqli->query("SELECT * FROM ultrasoniksmtp WHERE date='$this_date' ORDER BY time DESC LIMIT 1")->fetch_assoc();

    if (isset($result)) {
        $date = $result['date'];
        $time = $result['time'];
        $sensor_data = $result['sensor_data'];

        echo json_encode([
            "date" => $date,
            "time" => $time,
            "sensor_data" => $sensor_data
        ]);
    } else {
        echo json_encode([
            "message" => "NO DATA"
        ]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Data dari request
    parse_str(file_get_contents('php://input'), $p);
    $date = date('d-m-Y');
    $time = date('H:i');
    $sensor_data = $p['sensor_data'];

    // Data dari db
    $mysqli = new mysqli('localhost', 'root', 'faridkaka8', 'esp');
    $result = $mysqli->query("SELECT * FROM ultrasoniksmtp WHERE date='$date' ORDER BY time DESC LIMIT 1")->fetch_assoc();

    if (isset($result)) {
        $LastDate = $result['date'];
        $LastTime = $result['time'];

        // Menghitung Selisih Waktu
        $diff = abs(strtotime($time) - strtotime($LastTime));
        $minutes = floor($diff / 60);

        if ($LastDate == $date && $minutes <= 10) {
            echo "Tidak Bisa Kirim Notifikasi Email, Email Peringatan Dikirim 10 Menit Sekali";
        } else {
            $mysqli->query("INSERT INTO ultrasoniksmtp (date,time,sensor_data) VALUES('$date','$time','$sensor_data')");
            email($sensor_data);
        }
    } else {
        $mysqli->query("INSERT INTO ultrasoniksmtp (date,time,sensor_data) VALUES('$date','$time','$sensor_data')");
        email($sensor_data);
    }
}

function email($data)
{
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->SMTPDebug = 2; //Enable verbose debug output
        $mail->isSMTP(); //Send using SMTP
        $mail->Host = 'ssl://smtp.gmail.com'; //Set the SMTP server to send through
        $mail->SMTPAuth = true; //Enable SMTP authentication
        $mail->Username = 'hwidez@gmail.com'; //SMTP username
        $mail->Password = 'nglpovdxyhhwaddo'; //SMTP password
        $mail->SMTPSecure = 'tls'; //Enable implicit TLS encryption
        $mail->Port = 465; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //pengirim
        $mail->setFrom('hwidez@gmail.com', 'Notifikasi Banjir');
        $mail->addAddress('fatkhurrozakf@gmail.com'); //Add a recipient

        //Content
        $mail->isHTML(true); //Set email format to HTML
        $mail->Subject = 'Peringatan Ketinggian Air!';
        $mail->Body = 'Ketinggian Air : ' . $data . " cm";
        $mail->AltBody = '';
        //$mail->AddEmbeddedImage('gambar/logo.png', 'logo'); //abaikan jika tidak ada logo
        //$mail->addAttachment(''); 

        $mail->send();

        echo "Email Terkirim";
    } catch (Exception $e) {
        echo "Email Gagal Terkirim";
    }
}
