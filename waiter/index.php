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
            <span id="notifCount" class="badge bg-danger me-1 py-1" style="display:none;">🔔 0 siap antar</span>
            <span class="badge bg-secondary me-2"><?= $_SESSION['user_nama'] ?></span>
            <a href="../logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
        </div>
    </div>
    <div id="orderList"></div>
</div>
<script>
var lastSiapCount = 0;
var titleFlash = null;

function beep() {
    try {
        var ctx = new (window.AudioContext || window.webkitAudioContext)();
        var osc = ctx.createOscillator();
        var gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.frequency.value = 800; osc.type = 'sine';
        gain.gain.value = 0.3;
        osc.start(); osc.stop(ctx.currentTime + 0.3);
    } catch(e) {}
}

function api(action, id, msg) {
    if (msg && !confirm(msg)) return;
    fetch('api.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'action='+action+'&id='+id })
        .then(function(r) { return r.text(); })
        .then(function(m) { alert(m); loadOrders(); })
        .catch(function(e) { alert('Error: ' + e.message); });
}

function loadOrders() {
    fetch('api.php?action=list')
        .then(function(r) { return r.text(); })
        .then(function(html) {
            document.getElementById('orderList').innerHTML = html;
            var siap = (html.match(/siap antar/g) || []).length;
            var el = document.getElementById('notifCount');
            if (siap > lastSiapCount && lastSiapCount > 0) {
                beep();
                el.textContent = '🔔 ' + siap + ' siap antar!';
                el.style.display = 'inline';
                if (titleFlash) clearInterval(titleFlash);
                var origTitle = document.title;
                var flash = true;
                titleFlash = setInterval(function() {
                    document.title = flash ? '🔔 SIAP ANTAR!' : 'Waiter - Pesanan';
                    flash = !flash;
                }, 800);
                setTimeout(function() {
                    clearInterval(titleFlash); titleFlash = null;
                    document.title = origTitle;
                    el.style.display = 'none';
                }, 8000);
            } else if (siap === 0) {
                el.style.display = 'none';
            }
            if (siap > 0) lastSiapCount = siap;
        })
        .catch(function(e) { console.log('Load error:', e); });
}

// Event delegation untuk semua tombol
document.addEventListener('click', function(e) {
    var btn = e.target.closest('button');
    if (!btn) return;
    var id = btn.getAttribute('data-id');
    if (!id) return;
    if (btn.classList.contains('btn-validasi')) api('validasi', id, 'Validasi pesanan ini?');
    if (btn.classList.contains('btn-antar')) api('antar', id, 'Konfirmasi makanan sudah DIANTAR ke tamu?');
});

loadOrders();
setInterval(loadOrders, 5000);
</script>
<?php include '../includes/footer.php'; ?>
