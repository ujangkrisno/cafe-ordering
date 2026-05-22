<?php
$title = 'QR Code Menu';
include 'config/database.php';
include 'config/functions.php';
include 'includes/header.php';
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$base_url = str_replace('/qrcode.php', '', $base_url);
?>
<div class="container py-4 text-center">
    <nav class="navbar navbar-cafe rounded mb-4 px-3 py-2">
        <span class="navbar-brand mb-0"><i class="fas fa-qrcode me-2"></i>QR Code Menu</span>
        <a href="index.php" class="btn btn-light btn-sm"><i class="fas fa-home"></i> Kembali</a>
    </nav>
    <div class="card card-menu p-5 mx-auto" style="max-width:400px;">
        <h4 class="fw-bold mb-3" style="color:#4a2c2a;">Scan untuk memesan</h4>
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=<?= urlencode($base_url . '/menu.php') ?>"
             alt="QR Code" class="img-fluid mb-3" style="border-radius:12px;">
        <p class="text-muted mb-2">Arahkan kamera HP ke QR code di atas</p>
        <a href="https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=<?= urlencode($base_url . '/menu.php') ?>"
           target="_blank" class="btn btn-cafe btn-sm"><i class="fas fa-download"></i> Download QR</a>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
