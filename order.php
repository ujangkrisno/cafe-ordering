<?php
$title = 'Pesanan Saya';
include 'config/database.php';
include 'config/functions.php';
include 'includes/header.php';

$msg = '';
if ($_POST && isset($_POST['nama'])) {
    $no_order = generateNoOrder();
    $nama = mysqli_real_escape_string($con, $_POST['nama']);
    $hp = mysqli_real_escape_string($con, $_POST['hp']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $kursi = mysqli_real_escape_string($con, $_POST['no_kursi']);
    $catatan = mysqli_real_escape_string($con, $_POST['catatan'] ?? '');
    $items = json_decode($_POST['items'], true);
    $total = 0;
    foreach ($items as $it) $total += $it['harga'] * $it['qty'];

    $ins = mysqli_query($con, "INSERT INTO pesanan (no_order, nama, hp, email, no_kursi, total, catatan, status) VALUES ('$no_order','$nama','$hp','$email','$kursi',$total,'$catatan','baru')");
    if (!$ins) { $msg = '<div class="alert alert-danger">Gagal: ' . mysqli_error($con) . '</div>'; }
    else {
        $pid = mysqli_insert_id($con);
        foreach ($items as $it) {
            $mid = (int)$it['id'];
            $q = (int)$it['qty'];
            $h = (float)$it['harga'];
            $sub = $q * $h;
            mysqli_query($con, "INSERT INTO detail_pesanan (pesanan_id, menu_id, qty, harga, subtotal) VALUES ($pid, $mid, $q, $h, $sub)");
        }
        echo '<script>sessionStorage.removeItem("cart");</script>';
        $msg = '<div class="alert alert-success">Pesanan #'.$no_order.' berhasil! Silakan tunggu.</div>';
        $_POST = [];
    }
}
?>
<div class="container py-4">
    <nav class="navbar navbar-cafe rounded mb-4 px-3 py-2">
        <span class="navbar-brand mb-0"><i class="fas fa-shopping-cart me-2"></i>Pesanan Saya</span>
        <a href="menu.php" class="btn btn-light btn-sm"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
    </nav>
    <?= $msg ?>

    <div class="row g-3">
        <div class="col-md-7">
            <div class="card card-menu p-3">
                <h6 class="fw-bold"><i class="fas fa-list me-2"></i>Pesanan Saya</h6>
                <div id="cartItems"></div>
                <p class="text-end fw-bold mt-2" style="font-size:1.2rem;">Total: <span id="cartTotal">Rp 0</span></p>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card card-menu p-3">
                <h6 class="fw-bold"><i class="fas fa-user me-2"></i>Data Diri</h6>
                <form method="post" id="orderForm">
                    <input type="hidden" name="items" id="itemsInput">
                    <div class="mb-2">
                        <label class="form-label" style="font-size:0.8rem;">Nama</label>
                        <input type="text" name="nama" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" style="font-size:0.8rem;">No. HP</label>
                        <input type="text" name="hp" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" style="font-size:0.8rem;">Email (opsional)</label>
                        <input type="email" name="email" class="form-control form-control-sm">
                    </div>
                    <div class="mb-2">
                        <label class="form-label" style="font-size:0.8rem;">No. Kursi</label>
                        <input type="text" name="no_kursi" class="form-control form-control-sm" placeholder="Cth: A3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:0.8rem;">Catatan</label>
                        <textarea name="catatan" class="form-control form-control-sm" rows="2" placeholder="Misal: tidak pedas"></textarea>
                    </div>
                    <button type="submit" class="btn btn-cafe w-100" onclick="return kirimPesanan()">
                        <i class="fas fa-paper-plane me-1"></i> Pesan Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function renderCart() {
    var cart = JSON.parse(sessionStorage.getItem('cart') || '[]');
    var html = '';
    var total = 0;
    cart.forEach(function(it, i) {
        var sub = it.harga * it.qty;
        total += sub;
        html += '<div class="d-flex justify-content-between align-items-center border-bottom py-2">';
        html += '<div><strong>' + it.nama + '</strong><br><small>' + it.qty + ' x ' + it.harga.toLocaleString('id-ID') + '</small></div>';
        html += '<div class="d-flex align-items-center gap-2">';
        html += '<span class="fw-bold">Rp ' + sub.toLocaleString('id-ID') + '</span>';
        html += '<button class="btn btn-sm btn-outline-danger" onclick="hapusItem(' + i + ')"><i class="fas fa-times"></i></button>';
        html += '</div></div>';
    });
    if (!cart.length) html = '<p class="text-muted text-center my-4">Belum ada item. <a href="menu.php">Pilih menu</a></p>';
    document.getElementById('cartItems').innerHTML = html;
    document.getElementById('cartTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
}
function hapusItem(idx) {
    var cart = JSON.parse(sessionStorage.getItem('cart') || '[]');
    cart.splice(idx, 1);
    sessionStorage.setItem('cart', JSON.stringify(cart));
    renderCart();
}
function kirimPesanan() {
    var cart = JSON.parse(sessionStorage.getItem('cart') || '[]');
    if (!cart.length) { alert('Keranjang masih kosong!'); return false; }
    document.getElementById('itemsInput').value = JSON.stringify(cart);
    return true;
}
renderCart();
</script>
<?php include 'includes/footer.php'; ?>