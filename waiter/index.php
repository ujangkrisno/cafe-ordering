<?php
$title = 'Waiter - Pesanan';
include '../config/database.php';
include '../config/functions.php';
auth('waiter');

// Handle POST antar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['antar'])) {
    $id = (int)$_POST['antar'];
    mysqli_query($con, "UPDATE pesanan SET status='diantar' WHERE id=$id AND status='selesai'");
    $msg = mysqli_affected_rows($con) ? 'Pesanan sudah diantar ke tamu!' : 'Gagal!';
    echo "<script>alert('$msg');location.href='?';</script>"; exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validasi'])) {
    $id = (int)$_POST['validasi'];
    mysqli_query($con, "UPDATE pesanan SET status='diproses' WHERE id=$id AND status='baru'");
    mysqli_query($con, "UPDATE detail_pesanan SET status='dimasak' WHERE pesanan_id=$id AND status='menunggu'");
    echo '<script>alert("Pesanan divalidasi!");location.href="?";</script>'; exit;
}

include '../includes/header.php';

$q_baru = mysqli_query($con, "SELECT p.* FROM pesanan p WHERE p.status='baru' ORDER BY p.created_at DESC");
$q_proses = mysqli_query($con, "SELECT p.* FROM pesanan p WHERE p.status='diproses' ORDER BY p.created_at DESC");
$q_siap = mysqli_query($con, "SELECT p.* FROM pesanan p WHERE p.status='selesai' ORDER BY p.created_at ASC");
$q_riwayat = mysqli_query($con, "SELECT p.* FROM pesanan p WHERE p.status='diantar' ORDER BY p.created_at DESC LIMIT 10");
?>
<meta http-equiv="refresh" content="10">
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold"><i class="fas fa-clipboard-list me-2"></i>Pesanan</h5>
        <div>
            <span class="badge bg-secondary me-2"><?= $_SESSION['user_nama'] ?></span>
            <a href="../logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
        </div>
    </div>

    <?php if (mysqli_num_rows($q_baru)): ?>
    <h6 class="fw-bold text-danger mb-2"><i class="fas fa-exclamation-circle me-1"></i>Pesanan Baru</h6>
    <div class="row g-3 mb-4"><?php while ($r = mysqli_fetch_assoc($q_baru)): ?>
        <div class="col-md-4">
            <div class="card card-order status-baru p-3">
                <div class="d-flex justify-content-between"><strong>#<?= $r['no_order'] ?></strong> <span class="badge bg-danger">baru</span></div>
                <small><?= $r['nama'] ?> | Kursi: <?= $r['no_kursi'] ?></small>
                <hr class="my-1">
                <form method="post" style="display:inline"><button class="btn btn-sm btn-success" name="validasi" value="<?= $r['id'] ?>"><i class="fas fa-check"></i> Validasi</button></form>
            </div>
        </div>
    <?php endwhile; ?></div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($q_siap)): ?>
    <h6 class="fw-bold text-success mb-2"><i class="fas fa-utensils me-1"></i>Siap Antar</h6>
    <div class="row g-3 mb-4"><?php while ($r = mysqli_fetch_assoc($q_siap)): ?>
        <div class="col-md-4">
            <div class="card card-order status-selesai p-3 border border-success">
                <div class="d-flex justify-content-between"><strong>#<?= $r['no_order'] ?></strong> <span class="badge bg-success">siap antar</span></div>
                <small><?= $r['nama'] ?> | Kursi: <?= $r['no_kursi'] ?></small>
                <hr class="my-1">
                <form method="post" onsubmit="return confirm('Konfirmasi makanan sudah DIANTAR ke tamu?')">
                    <button class="btn btn-sm btn-success" name="antar" value="<?= $r['id'] ?>"><i class="fas fa-motorcycle me-1"></i> Konfirmasi Diantar</button>
                </form>
            </div>
        </div>
    <?php endwhile; ?></div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($q_riwayat)): ?>
    <h6 class="fw-bold text-secondary mb-2"><i class="fas fa-history me-1"></i>Riwayat Diantar</h6>
    <div class="row g-3"><?php while ($r = mysqli_fetch_assoc($q_riwayat)): ?>
        <div class="col-md-4">
            <div class="card card-order p-3 bg-light">
                <div class="d-flex justify-content-between"><strong>#<?= $r['no_order'] ?></strong> <span class="badge bg-secondary">diantar</span></div>
                <small><?= $r['nama'] ?> | Kursi: <?= $r['no_kursi'] ?></small>
                <div class="fw-bold mt-1"><?= rupiah($r['total']) ?></div>
            </div>
        </div>
    <?php endwhile; ?></div>
    <?php endif; ?>

    <?php if (!mysqli_num_rows($q_baru) && !mysqli_num_rows($q_siap)): ?>
    <div class="text-center text-muted py-5"><i class="fas fa-check-circle fa-3x mb-2 text-success"></i><p>Tidak ada pesanan.</p></div>
    <?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>
