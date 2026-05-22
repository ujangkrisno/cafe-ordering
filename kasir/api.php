<?php
include '../config/database.php';
include '../config/functions.php';
$action = $_REQUEST['action'] ?? '';

if ($action === 'list') {
    $q = mysqli_query($con, "SELECT * FROM pesanan WHERE status IN ('selesai','dibayar') ORDER BY FIELD(status,'selesai','dibayar'), created_at DESC");
    while ($r = mysqli_fetch_assoc($q)) {
        $detail = mysqli_query($con, "SELECT dp.*, m.nama as menu FROM detail_pesanan dp JOIN menu m ON dp.menu_id=m.id WHERE dp.pesanan_id={$r['id']}");
        $bg = $r['status']=='selesai'?'success':'secondary';
        echo '<div class="col-md-6 col-lg-4"><div class="card card-order status-'.$r['status'].' p-3">';
        echo '<div class="d-flex justify-content-between"><strong>#'.$r['no_order'].'</strong> <span class="badge bg-'.$bg.'">'.$r['status'].'</span></div>';
        echo '<small class="text-muted">'.$r['nama'].' | Kursi: '.$r['no_kursi'].'</small>';
        echo '<hr class="my-1"><ul class="list-unstyled mb-1">';
        while ($d = mysqli_fetch_assoc($detail)) {
            echo '<li><small>'.$d['qty'].'x '.$d['menu'].' = '.rupiah($d['subtotal']).'</small></li>';
        }
        echo '</ul>';
        echo '<div class="d-flex justify-content-between align-items-center mt-2">';
        echo '<span class="fw-bold" style="font-size:1.2rem;">'.rupiah($r['total']).'</span>';
        if ($r['status']=='selesai') echo '<button class="btn btn-cafe btn-sm" onclick="bayar('.$r['id'].')"><i class="fas fa-check-circle me-1"></i>Bayar</button>';
        else echo '<span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i>LUNAS</span>';
        echo '</div></div></div>';
    }
    exit;
}

if ($action === 'bayar') {
    $id = (int)$_POST['id'];
    mysqli_query($con, "UPDATE pesanan SET status='dibayar' WHERE id=$id");
    echo 'Pembayaran berhasil!';
    exit;
}
