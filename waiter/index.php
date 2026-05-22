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
        .then(function(html) { document.getElementById('orderList').innerHTML = html; });
}

document.addEventListener('click', function(e) {
    var btn = e.target.closest('[data-antar]');
    if (btn) {
        var id = btn.getAttribute('data-antar');
        if (!confirm('Konfirmasi makanan sudah DIANTAR ke tamu?')) return;
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'api.php?action=antar&id=' + id, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() { alert(this.responseText); loadOrders(); };
        xhr.onerror = function() { alert('Network error'); };
        xhr.send('action=antar&id=' + id);
        return;
    }
    btn = e.target.closest('[data-validasi]');
    if (btn) {
        var id = btn.getAttribute('data-validasi');
        if (!confirm('Validasi pesanan ini?')) return;
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'api.php?action=validasi&id=' + id, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() { alert(this.responseText); loadOrders(); };
        xhr.onerror = function() { alert('Network error'); };
        xhr.send('action=validasi&id=' + id);
    }
});

loadOrders();
setInterval(loadOrders, 5000);
</script>
<?php include '../includes/footer.php'; ?>
