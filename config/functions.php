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

function getLanIp() {
    $ips = [];
    if (function_exists('net_get_interfaces')) {
        foreach (net_get_interfaces() as $name => $iface) {
            if (strpos($name, 'Loopback') !== false || strpos($name, 'lo') !== false) continue;
            foreach ($iface['unicast'] ?? [] as $addr) {
                if ($addr['address'] && filter_var($addr['address'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && !filter_var($addr['address'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    $ips[] = $addr['address'];
                }
            }
        }
    }
    if (!$ips) {
        exec('ipconfig', $out, $rc);
        foreach ($out as $line) {
            if (preg_match('/IPv4 Address[^:]*:\s*([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/', $line, $m)) {
                if (!preg_match('/^169\.254\./', $m[1])) $ips[] = $m[1];
            }
        }
    }
    return $ips[0] ?? '127.0.0.1';
}
