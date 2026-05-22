<?php
$title = 'Waiter - Pesanan';
include '../config/database.php';
include '../config/functions.php';
auth('waiter');
include '../includes/header.php';

// Handle konfirmasi antar via GET
$flash_msg = '';
if (isset($_GET['antar'])) {
    $id = (int)$_GET['antar'];
    mysqli_query($con, "UPDATE pesanan SET status='diantar' WHERE id=$id AND status='selesai'");
    $flash_msg = mysqli_affected_rows($con) ? 'Pesanan sudah diantar ke tamu!' : 'Gagal: status bukan siap antar';
    echo '<script>alert("'.$flash_msg.'");location.href="?";</script>';
    exit;
}
if (isset($_GET['validasi'])) {
    $id = (int)$_GET['validasi'];
    mysqli_query($con, "UPDATE pesanan SET status='diproses' WHERE id=$id AND status='baru'");
    mysqli_query($con, "UPDATE detail_pesanan SET status='dimasak' WHERE pesanan_id=$id AND status='menunggu'");
    echo '<script>alert("Pesanan divalidasi!");location.href="?";</script>';
    exit;
}
?>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold"><i class="fas fa-clipboard-list me-2"></i>Pesanan</h5>
        <div>
            <span class="badge bg-secondary me-2"><?= $_SESSION['user_nama'] ?></span>
            <a href="../logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
        </div>
    </div>
    <div id="orderList"></div>
</div>
<script>
function loadOrders() {
    fetch('api.php?action=list')
        .then(function(r) { return r.text(); })
        .then(function(html) { document.getElementById('orderList').innerHTML = html; });
}
loadOrders();
setInterval(loadOrders, 5000);
</script>
<?php include '../includes/footer.php'; ?>
