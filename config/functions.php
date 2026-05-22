<?php
session_start();

function rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function tgl_indo($date) {
    $b = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    $t = explode('-', $date);
    return $t[2] . ' ' . $b[(int)$t[1]] . ' ' . $t[0];
}

function generateNoOrder() {
    global $con;
    $thnbln = date('ymd');
    $q = @mysqli_query($con, "SELECT COUNT(*) as c FROM pesanan WHERE no_order LIKE 'ORD/$thnbln/%'");
    $no = 1;
    if ($q && $r = mysqli_fetch_assoc($q)) $no = $r['c'] + 1;
    return 'ORD/' . $thnbln . '/' . str_pad($no, 4, '0', STR_PAD_LEFT);
}

function auth($role = null) {
    if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
    if ($role && $_SESSION['user_role'] !== $role && $_SESSION['user_role'] !== 'admin') {
        header('Location: index.php'); exit;
    }
}
