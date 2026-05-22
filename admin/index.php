<?php
$title = 'Admin Cafe';
include '../config/database.php';
include '../config/functions.php';
auth('admin');

$hari = date('Y-m-d');
$bln = date('Y-m');

$q = mysqli_query($con, "SELECT COALESCE(COUNT(*),0) as c, COALESCE(SUM(total),0) as t FROM pesanan WHERE DATE(created_at)='$hari' AND status='dibayar'");
$r = mysqli_fetch_assoc($q);
$trans_hari = $r['c'];
$omzet_hari = $r['t'];

$q = mysqli_query($con, "SELECT COALESCE(COUNT(*),0) as c, COALESCE(SUM(total),0) as t FROM pesanan WHERE DATE_FORMAT(created_at,'%Y-%m')='$bln' AND status='dibayar'");
$r = mysqli_fetch_assoc($q);
$trans_bulan = $r['c'];
$omzet_bulan = $r['t'];

$q = mysqli_query($con, "SELECT COUNT(*) as c FROM pesanan WHERE status='baru'");
$pesanan_baru = mysqli_fetch_assoc($q)['c'];

$q = mysqli_query($con, "SELECT DATE(created_at) as tgl, SUM(total) as total FROM pesanan WHERE status='dibayar' AND created_at >= DATE_SUB('$hari', INTERVAL 7 DAY) GROUP BY DATE(created_at) ORDER BY tgl");
$chart_labels = []; $chart_data = [];
while ($r = mysqli_fetch_assoc($q)) { $chart_labels[] = tgl_indo($r['tgl']); $chart_data[] = (float)$r['total']; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Cafe Ordering</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .sidebar { position:fixed; top:0; left:0; bottom:0; width:220px; background:linear-gradient(180deg,#4a2c2a,#5d3a37); color:#fff; z-index:100; }
        .sidebar .brand { padding:18px 16px; border-bottom:1px solid rgba(255,255,255,0.1); }
        .sidebar .brand h5 { margin:0; font-weight:700; }
        .sidebar a { display:flex; align-items:center; gap:10px; padding:10px 16px; color:rgba(255,255,255,0.75); text-decoration:none; font-size:0.85rem; }
        .sidebar a:hover, .sidebar a.active { background:rgba(255,255,255,0.1); color:#fff; }
        .sidebar a i { width:20px; }
        .main-content { margin-left:220px; padding:20px; }
        .card-stat { border:none; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.06); }
        .card-stat .card-body { padding:16px; }
        .card-stat .label { font-size:0.75rem; color:#888; text-transform:uppercase; }
        .card-stat .value { font-size:1.5rem; font-weight:700; }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="brand"><h5><i class="fas fa-mug-hot me-1"></i> Cafe Admin</h5></div>
    <a href="index.php" class="active"><i class="fas fa-home"></i><span>Dashboard</span></a>
    <a href="menu.php"><i class="fas fa-utensils"></i><span>Menu</span></a>
    <a href="laporan.php"><i class="fas fa-chart-bar"></i><span>Laporan</span></a>
    <hr style="border-color:rgba(255,255,255,0.1);margin:8px 12px;">
    <a href="../logout.php" style="color:#ef5350;"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
</div>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold">Dashboard</h5>
        <span class="badge bg-secondary"><?= $_SESSION['user_nama'] ?> (admin)</span>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3"><div class="card card-stat border-start border-4 border-danger"><div class="card-body"><div class="label">Pesanan Baru</div><div class="value text-danger"><?= $pesanan_baru ?></div></div></div></div>
        <div class="col-6 col-md-3"><div class="card card-stat border-start border-4 border-success"><div class="card-body"><div class="label">Transaksi Hari Ini</div><div class="value text-success"><?= $trans_hari ?></div></div></div></div>
        <div class="col-6 col-md-3"><div class="card card-stat border-start border-4 border-primary"><div class="card-body"><div class="label">Omzet Hari Ini</div><div class="value text-primary"><?= rupiah($omzet_hari) ?></div></div></div></div>
        <div class="col-6 col-md-3"><div class="card card-stat border-start border-4 border-warning"><div class="card-body"><div class="label">Omzet Bulan Ini</div><div class="value text-warning"><?= rupiah($omzet_bulan) ?></div></div></div></div>
    </div>

    <div class="card card-stat p-3">
        <h6 class="fw-bold">Penjualan 7 Hari Terakhir</h6>
        <canvas id="chartPenjualan" height="100"></canvas>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('chartPenjualan'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($chart_labels) ?>,
        datasets: [{ label:'Omzet', data:<?= json_encode($chart_data) ?>, backgroundColor:'#4a2c2a' }]
    },
    options: { responsive:true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}} }
});
</script>
</body>
</html>