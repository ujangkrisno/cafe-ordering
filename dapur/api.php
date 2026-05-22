<?php
include '../config/database.php';
include '../config/functions.php';
$action = $_REQUEST['action'] ?? '';

if ($action === 'list') {
    $q = mysqli_query($con, "SELECT p.*, 
        (SELECT COUNT(*) FROM detail_pesanan WHERE pesanan_id=p.id AND status='dimasak') as dimasak,
        (SELECT COUNT(*) FROM detail_pesanan WHERE pesanan_id=p.id) as total_item
        FROM pesanan p WHERE p.status='diproses' ORDER BY p.created_at ASC");
    while ($r = mysqli_fetch_assoc($q)) {
        $detail = mysqli_query($con, "SELECT dp.*, m.nama as menu FROM detail_pesanan dp JOIN menu m ON dp.menu_id=m.id WHERE dp.pesanan_id={$r['id']}");
        echo '<div class="col-md-6 col-lg-4"><div class="card card-order status-proses p-3">';
        echo '<div class="d-flex justify-content-between"><strong>#'.$r['no_order'].'</strong> <span class="badge bg-primary">'.$r['dimasak'].'/'.$r['total_item'].' dimasak</span></div>';
        echo '<small class="text-muted">Kursi: '.$r['no_kursi'].' | '.$r['nama'].'</small>';
        if ($r['catatan']) echo '<div class="mt-1 p-1 bg-warning bg-opacity-10 rounded"><small><strong>Catatan:</strong> '.$r['catatan'].'</small></div>';
        echo '<hr class="my-1"><ul class="list-unstyled mb-1">';
        while ($d = mysqli_fetch_assoc($detail)) {
            $sts = $d['status']=='selesai'?'✅ ':'<button class="btn btn-sm btn-outline-success py-0 px-1 ms-1" onclick="selesai('.$d['id'].')">Selesai</button>';
            echo '<li class="d-flex justify-content-between align-items-center"><small>'.$d['qty'].'x '.$d['menu'].'</small>'.$sts.'</li>';
        }
        echo '</ul></div></div>';
    }
    exit;
}

if ($action === 'selesai_item') {
    $id = (int)$_POST['id'];
    mysqli_query($con, "UPDATE detail_pesanan SET status='selesai' WHERE id=$id");

    // Cek apakah semua item sudah selesai
    $d = mysqli_fetch_assoc(mysqli_query($con, "SELECT pesanan_id FROM detail_pesanan WHERE id=$id"));
    $pid = $d['pesanan_id'];
    $sisa = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM detail_pesanan WHERE pesanan_id=$pid AND status!='selesai'"));
    if ($sisa['c'] == 0) {
        mysqli_query($con, "UPDATE pesanan SET status='selesai' WHERE id=$pid");
    }
    echo 'OK';
    exit;
}

if ($action === 'selesai') {
    // Legacy: selesaikan semua
    $id = (int)$_POST['id'];
    mysqli_query($con, "UPDATE detail_pesanan SET status='selesai' WHERE pesanan_id=$id");
    mysqli_query($con, "UPDATE pesanan SET status='selesai' WHERE id=$id");
    echo 'Pesanan selesai!';
    exit;
}
