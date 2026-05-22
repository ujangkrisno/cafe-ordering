<?php
include '../config/database.php';
include '../config/functions.php';
auth('waiter');
$id = (int)($_GET['id'] ?? 0);
if ($id) {
    mysqli_query($con, "UPDATE pesanan SET status='diantar' WHERE id=$id AND status='selesai'");
    $ok = mysqli_affected_rows($con);
}
?><script>alert('<?= $ok ? 'Pesanan sudah diantar ke tamu!' : 'Gagal: status bukan siap antar' ?>');history.back();</script>
