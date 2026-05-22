<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Cafe Ordering'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background: #f8f6f0; font-family: 'Segoe UI', system-ui, sans-serif; }
        .navbar-cafe { background: #4a2c2a; color: #fff; }
        .navbar-cafe .navbar-brand { font-weight: 700; color: #fff; }
        .card-menu { border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: transform .2s; cursor: pointer; }
        .card-menu:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.1); }
        .card-menu .card-img-top { height: 140px; object-fit: cover; border-radius: 12px 12px 0 0; background: #e8d5c4; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: #4a2c2a; }
        .badge-kategori { background: #4a2c2a; color: #fff; font-weight: 500; }
        .btn-cafe { background: #4a2c2a; color: #fff; border: none; }
        .btn-cafe:hover { background: #5d3a37; color: #fff; }
        .btn-cafe-outline { border: 2px solid #4a2c2a; color: #4a2c2a; background: transparent; }
        .btn-cafe-outline:hover { background: #4a2c2a; color: #fff; }
        .btn-outline-cafe { border: 2px solid #4a2c2a; color: #4a2c2a; background: transparent; padding: 10px 24px; border-radius: 8px; font-weight:600; }
        .btn-outline-cafe:hover { background: #4a2c2a; color: #fff; }
        .card-order { border-left: 4px solid #4a2c2a; border-radius: 8px; }
        .status-baru { background: #fff3cd; }
        .status-proses { background: #cce5ff; }
        .status-selesai { background: #d4edda; }
    </style>
</head>
<body>