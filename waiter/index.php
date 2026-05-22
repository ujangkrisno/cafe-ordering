<?php
$title = 'Waiter - Pesanan';
include '../config/database.php';
include '../config/functions.php';
auth('waiter');
include '../includes/header.php';
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
        .then(function(html) {
            document.getElementById('orderList').innerHTML = html;
        });
}

document.addEventListener('click', function(e) {
    var btn = e.target.closest('[data-action]');
    if (!btn) return;
    var action = btn.getAttribute('data-action');
    var id = btn.getAttribute('data-id');
    if (action === 'validasi') {
        if (!confirm('Validasi pesanan ini?')) return;
        fetch('api.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'action=validasi&id='+id })
            .then(function(r) { return r.text(); })
            .then(function(m) { alert(m); loadOrders(); });
    }
    if (action === 'antar') {
        if (!confirm('Konfirmasi makanan sudah DIANTAR ke tamu?')) return;
        fetch('api.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'action=antar&id='+id })
            .then(function(r) { return r.text(); })
            .then(function(m) { alert(m); loadOrders(); });
    }
});

loadOrders();
setInterval(loadOrders, 5000);
</script>
<?php include '../includes/footer.php'; ?>
