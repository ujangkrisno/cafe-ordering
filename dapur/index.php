<?php
$title = 'Dapur - Pesanan Masuk';
include '../config/database.php';
include '../config/functions.php';
auth('dapur');
include '../includes/header.php';
?>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold"><i class="fas fa-utensils me-2"></i>Pesanan Dapur</h5>
        <div>
            <span class="badge bg-secondary me-2"><?= $_SESSION['user_nama'] ?></span>
            <a href="../logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
        </div>
    </div>
    <div id="orderList" class="row g-3"></div>
</div>
<script>
function loadOrders() {
    fetch('api.php?action=list')
        .then(r => r.text())
        .then(html => document.getElementById('orderList').innerHTML = html);
}
function selesai(id) {
    fetch('api.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'action=selesai_item&id='+id })
        .then(r => r.text()).then(function(m) { loadOrders(); });
}
loadOrders();
setInterval(loadOrders, 5000);
</script>
<?php include '../includes/footer.php'; ?>