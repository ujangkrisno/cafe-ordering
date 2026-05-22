<?php
include 'config/database.php';
@mysqli_query($con, "ALTER TABLE pesanan MODIFY status ENUM('baru','diproses','selesai','diantar','dibayar','dibatalkan') DEFAULT 'baru'");
$drop = ['detail_pesanan','pesanan','menu','kategori','users'];
foreach ($drop as $t) @mysqli_query($con, "DROP TABLE IF EXISTS $t");
$sql = file_get_contents('database.sql');
foreach (explode(';', $sql) as $q) {
    $q = trim($q);
    if ($q) mysqli_query($con, $q);
}
$users = [['waiter','waiter123','Waiter','waiter'],['dapur','dapur123','Koki','dapur'],['kasir','kasir123','Kasir','kasir'],['admin','admin123','Admin','admin']];
foreach ($users as $u) {
    $h = password_hash($u[1], PASSWORD_DEFAULT);
    mysqli_query($con, "UPDATE users SET password='$h' WHERE username='$u[0]'");
}
echo "Setup selesai!<br><a href='index.php'>Buka Cafe</a> | <a href='login.php'>Login Staff</a>";
