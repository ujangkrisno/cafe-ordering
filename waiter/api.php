<?php
include '../config/database.php';
include '../config/functions.php';
$action = $_REQUEST['action'] ?? '';

if ($action === 'list') {
    $q = mysqli_query($con, "SELECT p.*, 
        (SELECT COUNT(*) FROM detail_pesanan WHERE pesanan_id=p.id AND status='menunggu') as menunggu
        FROM pesanan p WHERE p.status IN ('baru','diproses') ORDER BY p.created_at DESC");
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
        if ($r['status']=='baru') echo '<button class="btn btn-sm btn-success" onclick="validasi('.$r['id'].')"><i class="fas fa-check"></i> Validasi</button>';
        echo '</div></div></div>';
    }
    exit;
}

if ($action === 'validasi') {
    $id = (int)$_POST['id'];
    mysqli_query($con, "UPDATE pesanan SET status='diproses' WHERE id=$id AND status='baru'");
    mysqli_query($con, "UPDATE detail_pesanan SET status='dimasak' WHERE pesanan_id=$id AND status='menunggu'");
    echo 'Pesanan divalidasi dan dikirim ke dapur!';
    exit;
}
