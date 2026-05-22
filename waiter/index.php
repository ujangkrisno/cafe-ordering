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
            <span id="notifCount" class="badge bg-danger me-1" style="display:none;">0</span>
            <span class="badge bg-secondary me-2"><?= $_SESSION['user_nama'] ?></span>
            <a href="../logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
        </div>
    </div>
    <div id="orderList" class="row g-3"></div>
</div>
<script>
var lastSiapCount = 0;
function loadOrders() {
    fetch('api.php?action=list')
        .then(r => r.text())
        .then(html => {
            document.getElementById('orderList').innerHTML = html;
            // Hitung kartu siap antar
            var siap = (html.match(/siap antar/g) || []).length;
            var el = document.getElementById('notifCount');
            if (siap > lastSiapCount && lastSiapCount > 0) {
                el.textContent = '🔔 ' + siap;
                el.style.display = 'inline';
                setTimeout(function() { el.style.display = 'none'; }, 5000);
            }
            lastSiapCount = siap;
        });
}
function validasi(id) {
    if (!confirm('Validasi pesanan ini?')) return;
    fetch('api.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'action=validasi&id='+id })
        .then(r => r.text()).then(function(m) { alert(m); loadOrders(); });
}
function antar(id) {
    if (!confirm('Konfirmasi makanan sudah DIANTAR ke tamu?')) return;
    fetch('api.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'action=antar&id='+id })
        .then(r => r.text()).then(function(m) { alert(m); loadOrders(); });
}
loadOrders();
setInterval(loadOrders, 5000);
</script>
<?php include '../includes/footer.php'; ?>
