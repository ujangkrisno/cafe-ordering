<?php
$title = 'Kelola Menu';
include '../config/database.php';
include '../config/functions.php';
auth('admin');

$msg = '';

// Upload gambar
if ($_POST && isset($_POST['simpan'])) {
    $id = (int)($_POST['id'] ?? 0);
    $kategori_id = (int)$_POST['kategori_id'];
    $nama = mysqli_real_escape_string($con, $_POST['nama']);
    $harga = str_replace(',', '', $_POST['harga']);
    $deskripsi = mysqli_real_escape_string($con, $_POST['deskripsi']);
    $tersedia = (int)($_POST['tersedia'] ?? 1);
    $gambar = '';

    // Handle upload
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (in_array($ext, $allowed)) {
            $nama_file = 'menu_' . time() . '_' . rand(100,999) . '.' . $ext;
            $upload_dir = __DIR__ . '/../uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $nama_file);
            $gambar = 'uploads/' . $nama_file;
        } else {
            $msg = '<div class="alert alert-danger py-2">Format file tidak didukung (jpg/png/gif/webp)</div>';
        }
    }

    if ($id) {
        $sql = "UPDATE menu SET kategori_id=$kategori_id, nama='$nama', harga='$harga', deskripsi='$deskripsi', tersedia=$tersedia";
        if ($gambar) $sql .= ", gambar='$gambar'";
        $sql .= " WHERE id=$id";
        mysqli_query($con, $sql);
        $msg = '<div class="alert alert-success py-2">Menu diupdate.</div>';
    } else {
        $ins = mysqli_query($con, "INSERT INTO menu (kategori_id, nama, harga, deskripsi, tersedia, gambar) VALUES ($kategori_id, '$nama', '$harga', '$deskripsi', $tersedia, '$gambar')");
        $msg = $ins ? '<div class="alert alert-success py-2">Menu ditambahkan.</div>' : '<div class="alert alert-danger py-2">Gagal: ' . mysqli_error($con) . '</div>';
    }
}

// Hapus
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $r = mysqli_fetch_assoc(mysqli_query($con, "SELECT gambar FROM menu WHERE id=$id"));
    if ($r && $r['gambar'] && file_exists(__DIR__ . '/../' . $r['gambar'])) unlink(__DIR__ . '/../' . $r['gambar']);
    mysqli_query($con, "DELETE FROM menu WHERE id=$id");
    $msg = '<div class="alert alert-success py-2">Menu dihapus.</div>';
}

$q = mysqli_query($con, "SELECT m.*, k.nama as kategori FROM menu m LEFT JOIN kategori k ON m.kategori_id=k.id ORDER BY k.nama, m.nama");
$kategori = mysqli_query($con, "SELECT * FROM kategori ORDER BY nama");

$edit = [];
if (isset($_GET['edit'])) {
    $r = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM menu WHERE id=" . (int)$_GET['edit']));
    if ($r) $edit = $r;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Menu - Cafe Admin</title>
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
        .preview-img { width:60px; height:60px; object-fit:cover; border-radius:8px; }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="brand"><h5><i class="fas fa-mug-hot me-1"></i> Cafe Admin</h5></div>
    <a href="index.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
    <a href="menu.php" class="active"><i class="fas fa-utensils"></i><span>Menu</span></a>
    <a href="laporan.php"><i class="fas fa-chart-bar"></i><span>Laporan</span></a>
    <hr style="border-color:rgba(255,255,255,0.1);margin:8px 12px;">
    <a href="../logout.php" style="color:#ef5350;"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
</div>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold">Kelola Menu</h5>
        <span class="badge bg-secondary"><?= $_SESSION['user_nama'] ?></span>
    </div>
    <?= $msg ?>

    <div class="card card-stat mb-4 p-3">
        <form method="post" enctype="multipart/form-data" class="row g-2">
            <input type="hidden" name="id" value="<?= $edit['id'] ?? 0 ?>">
            <div class="col-md-3">
                <label class="form-label" style="font-size:0.75rem;color:#666;">Nama Menu</label>
                <input type="text" name="nama" class="form-control form-control-sm" value="<?= $edit['nama'] ?? '' ?>" required>
            </div>
            <div class="col-md-2">
                <label class="form-label" style="font-size:0.75rem;color:#666;">Kategori</label>
                <select name="kategori_id" class="form-select form-select-sm" required>
                    <?php mysqli_data_seek($kategori, 0); while ($k = mysqli_fetch_assoc($kategori)): ?>
                    <option value="<?= $k['id'] ?>" <?= (isset($edit['kategori_id']) && $edit['kategori_id']==$k['id'])?'selected':'' ?>><?= $k['nama'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" style="font-size:0.75rem;color:#666;">Harga</label>
                <input type="text" name="harga" class="form-control form-control-sm" value="<?= $edit['harga'] ?? '' ?>" required>
            </div>
            <div class="col-md-2">
                <label class="form-label" style="font-size:0.75rem;color:#666;">Tersedia</label>
                <select name="tersedia" class="form-select form-select-sm">
                    <option value="1" <?= (isset($edit['tersedia']) && $edit['tersedia']==1)?'selected':'' ?>>Ya</option>
                    <option value="0" <?= (isset($edit['tersedia']) && $edit['tersedia']==0)?'selected':'' ?>>Tidak</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" style="font-size:0.75rem;color:#666;">Foto</label>
                <input type="file" name="gambar" class="form-control form-control-sm" accept="image/*">
                <?php if (!empty($edit['gambar'])): ?>
                <small class="text-muted">Current: <a href="../<?= $edit['gambar'] ?>" target="_blank"><?= basename($edit['gambar']) ?></a></small>
                <?php endif; ?>
            </div>
            <div class="col-12">
                <label class="form-label" style="font-size:0.75rem;color:#666;">Deskripsi</label>
                <input type="text" name="deskripsi" class="form-control form-control-sm" value="<?= $edit['deskripsi'] ?? '' ?>">
            </div>
            <div class="col-12">
                <button class="btn btn-sm btn-cafe" name="simpan"><i class="fas fa-save"></i> Simpan</button>
                <?php if (isset($_GET['edit'])): ?><a href="menu.php" class="btn btn-sm btn-secondary">Batal</a><?php endif; ?>
            </div>
        </form>
    </div>

    <div class="card card-stat">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>#</th><th>Foto</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr></thead>
                <tbody><?php $no=1; while($r=mysqli_fetch_assoc($q)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?php if ($r['gambar']): ?><img src="../<?= $r['gambar'] ?>" class="preview-img"><?php else: ?><span class="text-muted">-</span><?php endif; ?></td>
                        <td><?= $r['nama'] ?></td>
                        <td><?= $r['kategori'] ?></td>
                        <td><?= rupiah($r['harga']) ?></td>
                        <td><span class="badge bg-<?= $r['tersedia']?'success':'danger' ?>"><?= $r['tersedia']?'Ya':'Tidak' ?></span></td>
                        <td>
                            <a href="menu.php?edit=<?= $r['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            <a href="menu.php?hapus=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?></tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>