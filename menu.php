<?php
$title = 'Menu';
include 'config/database.php';
include 'config/functions.php';
include 'includes/header.php';

$kat_id = (int)($_GET['kategori'] ?? 0);
$where = $kat_id ? "WHERE m.kategori_id=$kat_id AND m.tersedia=1" : "WHERE m.tersedia=1";
$menu = mysqli_query($con, "SELECT m.*, k.nama as kategori FROM menu m LEFT JOIN kategori k ON m.kategori_id=k.id $where ORDER BY k.nama, m.nama");
$kategori = mysqli_query($con, "SELECT * FROM kategori ORDER BY nama");
?>
<div class="container py-4">
    <nav class="navbar navbar-cafe rounded mb-4 px-3 py-2">
        <span class="navbar-brand mb-0"><i class="fas fa-utensils me-2"></i>Menu Cafe</span>
        <a href="order.php" class="btn btn-light btn-sm position-relative">
            <i class="fas fa-shopping-cart"></i> Pesanan
            <span class="badge bg-danger" id="cartCount">0</span>
        </a>
    </nav>

    <div class="d-flex gap-2 mb-3 flex-wrap">
        <a href="menu.php" class="btn btn-sm <?= !$kat_id?'btn-cafe':'btn-cafe-outline' ?>">Semua</a>
        <?php mysqli_data_seek($kategori, 0); while ($k = mysqli_fetch_assoc($kategori)): ?>
        <a href="menu.php?kategori=<?= $k['id'] ?>" class="btn btn-sm <?= $kat_id==$k['id']?'btn-cafe':'btn-cafe-outline' ?>">
            <i class="fas <?= $k['icon'] ?>"></i> <?= $k['nama'] ?>
        </a>
        <?php endwhile; ?>
    </div>

    <div class="row g-3">
        <?php while ($m = mysqli_fetch_assoc($menu)): ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card card-menu h-100" onclick="tambahKeranjang(<?= $m['id'] ?>, '<?= addslashes($m['nama']) ?>', <?= $m['harga'] ?>)">
                <div class="card-img-top d-flex align-items-center justify-content-center" style="height:120px;">
                    <i class="fas fa-<?= $m['kategori_id']==2?'coffee':'utensils' ?>" style="font-size:2.5rem;color:#4a2c2a;"></i>
                </div>
                <div class="card-body p-2">
                    <h6 class="fw-bold mb-1"><?= $m['nama'] ?></h6>
                    <p class="text-muted mb-1" style="font-size:0.75rem;"><?= $m['deskripsi'] ?></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold" style="color:#4a2c2a;"><?= rupiah($m['harga']) ?></span>
                        <span class="badge bg-secondary"><?= $m['kategori'] ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>
<script>
var keranjang = JSON.parse(sessionStorage.getItem('cart') || '[]');
function simpanCart() { sessionStorage.setItem('cart', JSON.stringify(keranjang)); updateCartCount(); }
function updateCartCount() {
    var total = keranjang.reduce((s,i) => s + i.qty, 0);
    document.getElementById('cartCount').textContent = total;
}
function tambahKeranjang(id, nama, harga) {
    var idx = keranjang.findIndex(i => i.id === id);
    if (idx >= 0) keranjang[idx].qty++;
    else keranjang.push({id, nama, harga, qty: 1});
    simpanCart();
}
updateCartCount();
</script>
<?php include 'includes/footer.php'; ?>