<?php
include '../config/database.php';
include '../config/functions.php';
$action = $_REQUEST['action'] ?? '';

if ($action === 'list') {
    // Pesanan Baru & Diproses
    $q = mysqli_query($con, "SELECT p.*, 
        (SELECT COUNT(*) FROM detail_pesanan WHERE pesanan_id=p.id AND status='menunggu') as menunggu
        FROM pesanan p WHERE p.status IN ('baru','diproses') ORDER BY p.created_at DESC");
    $ada_baru = mysqli_num_rows($q) > 0;

    if ($ada_baru) {
        echo '<h6 class="fw-bold text-danger mb-2"><i class="fas fa-exclamation-circle me-1"></i>Pesanan Baru</h6>';
        echo '<div class="row g-3 mb-4">';
        while ($r = mysqli_fetch_assoc($q)) {
            $detail = mysqli_query($con, "SELECT dp.*, m.nama as menu FROM detail_pesanan dp JOIN menu m ON dp.menu_id=m.id WHERE dp.pesanan_id={$r['id']}");
            $badge = $r['status']=='baru'?'danger':'warning';
            echo '<div class="col-md-6 col-lg-4"><div class="card card-order status-'.$r['status'].' p-3">';
            echo '<div class="d-flex justify-content-between"><strong>#'.$r['no_order'].'</strong> <span class="badge bg-'.$badge.'">'.$r['status'].'</span></div>';
            echo '<small class="text-muted">'.$r['nama'].' | HP: '.$r['hp'].' | Kursi: '.$r['no_kursi'].'</small>';
            if ($r['email']) echo '<small class="text-muted">Email: '.$r['email'].'</small>';
            echo '<hr class="my-1"><ul class="list-unstyled mb-1">';
            while ($d = mysqli_fetch_assoc($detail)) {
                echo '<li><small>'.$d['qty'].'x '.$d['menu'].' = '.rupiah($d['subtotal']).'</small></li>';
            }
            echo '</ul>';
            echo '<div class="d-flex justify-content-between align-items-center mt-1">';
            echo '<small class="fw-bold">Total: '.rupiah($r['total']).'</small>';
            if ($r['status']=='baru') echo '<a class="btn btn-sm btn-success" href="?validasi='.$r['id'].'"><i class="fas fa-check"></i> Validasi</a>';
            echo '</div></div></div>';
        }
        echo '</div>';
    }

    // Siap Antar
    $q2 = mysqli_query($con, "SELECT p.* FROM pesanan p WHERE p.status='selesai' ORDER BY p.created_at ASC");
    $ada_siap = mysqli_num_rows($q2) > 0;

    if ($ada_siap) {
        echo '<h6 class="fw-bold text-success mb-2"><i class="fas fa-utensils me-1"></i>Siap Antar</h6>';
        echo '<div class="row g-3 mb-4">';
        while ($r = mysqli_fetch_assoc($q2)) {
            $detail = mysqli_query($con, "SELECT dp.*, m.nama as menu FROM detail_pesanan dp JOIN menu m ON dp.menu_id=m.id WHERE dp.pesanan_id={$r['id']}");
            echo '<div class="col-md-6 col-lg-4"><div class="card card-order status-selesai p-3 border border-success">';
            echo '<div class="d-flex justify-content-between"><strong>#'.$r['no_order'].'</strong> <span class="badge bg-success">siap antar</span></div>';
            echo '<small class="text-muted">'.$r['nama'].' | Kursi: '.$r['no_kursi'].'</small>';
            echo '<hr class="my-1"><ul class="list-unstyled mb-1">';
            while ($d = mysqli_fetch_assoc($detail)) {
                echo '<li><small>'.$d['qty'].'x '.$d['menu'].' = '.rupiah($d['subtotal']).'</small></li>';
            }
            echo '</ul>';
            echo '<div class="d-flex justify-content-between align-items-center mt-2">';
            echo '<span class="fw-bold">'.rupiah($r['total']).'</span>';
            echo '<a class="btn btn-sm btn-success" href="?antar='.$r['id'].'" onclick="return confirm(\'Konfirmasi makanan sudah DIANTAR ke tamu?\')"><i class="fas fa-motorcycle me-1"></i>Konfirmasi Diantar</a>';
            echo '</div></div></div>';
        }
        echo '</div>';
    }

    // Riwayat Diantar
    $q3 = mysqli_query($con, "SELECT p.* FROM pesanan p WHERE p.status='diantar' ORDER BY p.created_at DESC LIMIT 10");
    if (mysqli_num_rows($q3) > 0) {
        echo '<h6 class="fw-bold text-secondary mb-2"><i class="fas fa-history me-1"></i>Riwayat Diantar</h6>';
        echo '<div class="row g-3">';
        while ($r = mysqli_fetch_assoc($q3)) {
            $detail = mysqli_query($con, "SELECT dp.*, m.nama as menu FROM detail_pesanan dp JOIN menu m ON dp.menu_id=m.id WHERE dp.pesanan_id={$r['id']}");
            echo '<div class="col-md-6 col-lg-4"><div class="card card-order p-3 bg-light">';
            echo '<div class="d-flex justify-content-between"><strong>#'.$r['no_order'].'</strong> <span class="badge bg-secondary">diantar</span></div>';
            echo '<small class="text-muted">'.$r['nama'].' | Kursi: '.$r['no_kursi'].'</small>';
            echo '<hr class="my-1"><ul class="list-unstyled mb-1">';
            while ($d = mysqli_fetch_assoc($detail)) {
                echo '<li><small>'.$d['qty'].'x '.$d['menu'].'</small></li>';
            }
            echo '</ul><div class="fw-bold mt-1">'.rupiah($r['total']).'</div></div></div>';
        }
        echo '</div>';
    }

    if (!$ada_baru && !$ada_siap) {
        echo '<div class="text-center text-muted py-5"><i class="fas fa-check-circle fa-3x mb-2 text-success"></i><p>Tidak ada pesanan.</p></div>';
    }
    exit;
}

if ($action === 'validasi') {
    $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
    mysqli_query($con, "UPDATE pesanan SET status='diproses' WHERE id=$id AND status='baru'");
    mysqli_query($con, "UPDATE detail_pesanan SET status='dimasak' WHERE pesanan_id=$id AND status='menunggu'");
    echo 'OK';
    exit;
}

if ($action === 'antar') {
    $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
    if ($id) {
        mysqli_query($con, "UPDATE pesanan SET status='diantar' WHERE id=$id AND status='selesai'");
        echo mysqli_affected_rows($con) ? 'OK' : 'GAGAL';
    }
    exit;
}
