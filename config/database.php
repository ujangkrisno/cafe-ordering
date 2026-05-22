<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'cafe_ordering';

$con = mysqli_connect($host, $user, $pass);
if (!$con) die("Koneksi MySQL gagal: " . mysqli_connect_error());
mysqli_report(MYSQLI_REPORT_OFF);
@mysqli_select_db($con, $db);
mysqli_report(MYSQLI_REPORT_STRICT);
