<?php
include 'config/functions.php';
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['user_role'];
    $map = ['waiter'=>'waiter/index.php','dapur'=>'dapur/index.php','kasir'=>'kasir/index.php','admin'=>'admin/index.php'];
    header('Location: ' . ($map[$role] ?? 'index.php'));
    exit;
}
include 'config/database.php';
$error = '';
if ($_POST) {
    $u = mysqli_real_escape_string($con, $_POST['username']);
    $p = $_POST['password'];
    $q = mysqli_query($con, "SELECT * FROM users WHERE username='$u'");
    if ($r = mysqli_fetch_assoc($q)) {
        if (password_verify($p, $r['password'])) {
            $_SESSION['user_id'] = $r['id'];
            $_SESSION['user_nama'] = $r['nama'];
            $_SESSION['user_role'] = $r['role'];
            $_SESSION['user_username'] = $r['username'];
            $map = ['waiter'=>'waiter/index.php','dapur'=>'dapur/index.php','kasir'=>'kasir/index.php','admin'=>'admin/index.php'];
            header('Location: ' . ($map[$r['role']] ?? 'index.php'));
            exit;
        }
    }
    $error = 'Username atau password salah!';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background: #4a2c2a; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-box { background: #fff; border-radius: 12px; padding: 32px; width: 100%; max-width: 380px; }
    </style>
</head>
<body>
<div class="login-box">
    <div class="text-center mb-3">
        <i class="fas fa-mug-hot" style="font-size:2.5rem;color:#4a2c2a;"></i>
        <h5 class="fw-bold mt-2">Login Staff</h5>
    </div>
    <?php if ($error): ?><div class="alert alert-danger py-2"><?= $error ?></div><?php endif; ?>
    <form method="post">
        <div class="mb-3"><input type="text" name="username" class="form-control" placeholder="Username" required autofocus></div>
        <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
        <button class="btn btn-cafe w-100"><i class="fas fa-sign-in-alt me-1"></i> Masuk</button>
    </form>
    <div class="text-center mt-3"><small class="text-muted">Demo: waiter/waiter123, dapur/dapur123, kasir/kasir123</small></div>
</div>
</body>
</html>