<?php
include '../config/database.php';
include '../config/functions.php';
auth('waiter');
$id = (int)($_GET['id'] ?? 0);
$msg = 'ID tidak valid';
if ($id) {
    mysqli_query($con, "UPDATE pesanan SET status='diantar' WHERE id=$id AND status='selesai'");
    $msg = mysqli_affected_rows($con) ? 'Pesanan sudah diantar ke tamu!' : 'Gagal: status bukan siap antar';
}
?><script>alert('<?= $msg ?>');location.href='index.php';</script>
