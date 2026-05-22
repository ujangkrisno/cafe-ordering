<?php
$title = 'Selamat Datang';
include 'config/database.php';
include 'config/functions.php';
include 'includes/header.php';
$kategori = mysqli_query($con, "SELECT * FROM kategori ORDER BY nama");
$ip_server = $_SERVER['SERVER_ADDR'] ?? '127.0.0.1';
$port = $_SERVER['SERVER_PORT'];
$menu_url = "http://$ip_server:$port/menu.php";
?>
<div class="container py-4">
    <div class="text-center mb-4">
        <i class="fas fa-mug-hot" style="font-size:3rem;color:#4a2c2a;"></i>
        <h2 class="fw-bold mt-2" style="color:#4a2c2a;">Selamat Datang di Cafe Kami</h2>
        <p class="text-muted">Scan QR code untuk memesan makanan & minuman</p>
        <a href="menu.php" class="btn btn-cafe btn-lg mt-2"><i class="fas fa-utensils me-2"></i>Pesan Sekarang</a>
        <a href="qrcode.php" class="btn btn-outline-cafe btn-lg mt-2 ms-2"><i class="fas fa-qrcode me-2"></i>Lihat QR</a>
    </div>
    <div class="mt-4 text-center">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=<?= urlencode($menu_url) ?>"
             alt="QR" style="border-radius:10px;" class="border p-1 bg-white">
        <p class="text-muted mt-1" style="font-size:0.75rem;">Scan dengan HP untuk pesan</p>
    </div>
    <div class="row g-3 mt-3">
        <?php while ($k = mysqli_fetch_assoc($kategori)): ?>
        <div class="col-4">
            <div class="card card-menu text-center p-3">
                <div class="card-img-top d-flex align-items-center justify-content-center" style="height:100px;background:#e8d5c4;border-radius:12px;">
                    <i class="fas <?= $k['icon'] ?>" style="font-size:2rem;color:#4a2c2a;"></i>
                </div>
                <div class="card-body p-2">
                    <h6 class="fw-bold mb-0"><?= $k['nama'] ?></h6>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>