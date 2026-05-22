<?php
$title = 'Laporan Keuangan';
include '../config/database.php';
include '../config/functions.php';
auth('admin');

$periode = $_GET['periode'] ?? 'bulanan';
switch ($periode) {
    case 'harian': $tgl = $_GET['tgl'] ?? date('Y-m-d'); $tgl_awal = $tgl_akhir = $tgl; $label = date('d M Y', strtotime($tgl)); break;
    case 'mingguan':
        $tgl = $_GET['tgl'] ?? date('Y-m-d');
        $day = date('N', strtotime($tgl));
        $tgl_awal = date('Y-m-d', strtotime("-$day day", strtotime($tgl)));
        $tgl_akhir = date('Y-m-d', strtotime('+'.(6-$day).' day', strtotime($tgl)));
        $label = tgl_indo($tgl_awal) . ' - ' . tgl_indo($tgl_akhir); break;
    case 'tahunan': $thn = $_GET['tahun'] ?? date('Y'); $tgl_awal = "$thn-01-01"; $tgl_akhir = "$thn-12-31"; $label = $thn; break;
    default: $bln = $_GET['bulan'] ?? date('Y-m'); $tgl_awal = $bln.'-01'; $tgl_akhir = date('Y-m-t', strtotime($tgl_awal)); $label = tgl_indo($tgl_awal) . ' - ' . tgl_indo($tgl_akhir); break;
}

$q = mysqli_query($con, "SELECT COALESCE(COUNT(*),0) as total_trans, COALESCE(SUM(total),0) as omzet FROM pesanan WHERE status='dibayar' AND DATE(created_at) BETWEEN '$tgl_awal' AND '$tgl_akhir'");
$r = mysqli_fetch_assoc($q);
$total_trans = $r['total_trans'];
$omzet = $r['omzet'];

$q = mysqli_query($con, "SELECT DATE(created_at) as tgl, COUNT(*) as trans, SUM(total) as omzet FROM pesanan WHERE status='dibayar' AND DATE(created_at) BETWEEN '$tgl_awal' AND '$tgl_akhir' GROUP BY DATE(created_at) ORDER BY tgl DESC");
$detail = mysqli_query($con, "SELECT p.*, 
    (SELECT GROUP_CONCAT(CONCAT(dp.qty,'x ',m.nama) SEPARATOR ', ') FROM detail_pesanan dp JOIN menu m ON dp.menu_id=m.id WHERE dp.pesanan_id=p.id) as items
    FROM pesanan p WHERE p.status='dibayar' AND DATE(p.created_at) BETWEEN '$tgl_awal' AND '$tgl_akhir' ORDER BY p.created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Cafe Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background:#f0f2f5; font-family:'Segoe UI',sans-serif; }
        .sidebar { position:fixed; top:0; left:0; bottom:0; width:220px; background:linear-gradient(180deg,#4a2c2a,#5d3a37); color:#fff; z-index:100; }
        .sidebar .brand { padding:18px 16px; border-bottom:1px solid rgba(255,255,255,0.1); }
        .sidebar .brand h5 { margin:0; font-weight:700; }
        .sidebar a { display:flex; align-items:center; gap:10px; padding:10px 16px; color:rgba(255,255,255,0.75); text-decoration:none; font-size:0.85rem; }
        .sidebar a:hover, .sidebar a.active { background:rgba(255,255,255,0.1); color:#fff; }
        .sidebar a i { width:20px; }
        .main-content { margin-left:220px; padding:20px; }
        .card-stat { border:none; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.06); }
        .card-stat .card-body { padding:16px; }
        .table th { background:#4a2c2a; color:#fff; font-weight:500; font-size:0.8rem; }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="brand"><h5><i class="fas fa-mug-hot me-1"></i> Cafe Admin</h5></div>
    <a href="index.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
    <a href="laporan.php" class="active"><i class="fas fa-chart-bar"></i><span>Laporan</span></a>
    <hr style="border-color:rgba(255,255,255,0.1);margin:8px 12px;">
    <a href="../logout.php" style="color:#ef5350;"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
</div>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold">Laporan Keuangan</h5>
        <span class="badge bg-secondary"><?= $_SESSION['user_nama'] ?></span>
    </div>

    <div class="card card-stat mb-4 p-3">
        <form method="get" class="row g-2 align-items-end">
            <div class="col-auto">
                <select name="periode" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="harian" <?= $periode=='harian'?'selected':'' ?>>Harian</option>
                    <option value="mingguan" <?= $periode=='mingguan'?'selected':'' ?>>Mingguan</option>
                    <option value="bulanan" <?= $periode=='bulanan'?'selected':'' ?>>Bulanan</option>
                    <option value="tahunan" <?= $periode=='tahunan'?'selected':'' ?>>Tahunan</option>
                </select>
            </div>
            <?php if ($periode=='harian' || $periode=='mingguan'): ?>
            <div class="col-auto"><input type="date" name="tgl" class="form-control form-control-sm" value="<?= $_GET['tgl']??date('Y-m-d') ?>" onchange="this.form.submit()"></div>
            <?php elseif ($periode=='bulanan'): ?>
            <div class="col-auto"><input type="month" name="bulan" class="form-control form-control-sm" value="<?= $_GET['bulan']??date('Y-m') ?>" onchange="this.form.submit()"></div>
            <?php elseif ($periode=='tahunan'): ?>
            <div class="col-auto"><input type="number" name="tahun" class="form-control form-control-sm" value="<?= $_GET['tahun']??date('Y') ?>" onchange="this.form.submit()"></div>
            <?php endif; ?>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4"><div class="card card-stat border-start border-4 border-primary"><div class="card-body"><div class="label">Periode</div><div class="value" style="font-size:1rem;"><?= $label ?></div></div></div></div>
        <div class="col-md-4"><div class="card card-stat border-start border-4 border-success"><div class="card-body"><div class="label">Total Transaksi</div><div class="value text-success"><?= $total_trans ?></div></div></div></div>
        <div class="col-md-4"><div class="card card-stat border-start border-4 border-warning"><div class="card-body"><div class="label">Total Omzet</div><div class="value text-warning"><?= rupiah($omzet) ?></div></div></div></div>
    </div>

    <div class="card card-stat">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>#</th><th>No Order</th><th>Pelanggan</th><th>Kursi</th><th>Items</th><th>Total</th><th>Tanggal</th></tr></thead>
                <tbody><?php $no=1; while($r=mysqli_fetch_assoc($detail)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $r['no_order'] ?></td>
                        <td><?= $r['nama'] ?></td>
                        <td><?= $r['no_kursi'] ?></td>
                        <td><small><?= $r['items'] ?></small></td>
                        <td><?= rupiah($r['total']) ?></td>
                        <td><?= tgl_indo(date('Y-m-d', strtotime($r['created_at']))) ?></td>
                    </tr>
                <?php endwhile; if(!$no): ?><tr><td colspan="7" class="text-center text-muted py-3">Belum ada data</td></tr><?php endif; ?></tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>