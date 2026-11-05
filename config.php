<?php
declare(strict_types=1);
session_start();

const DB_HOST = '127.0.0.1';
const DB_NAME = 'pln_inventory';
const DB_USER = 'root';
const DB_PASS = '';

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
            DB_USER, DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }
    return $pdo;
}
function e($value): string { return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8'); }
function redirect(string $url): never { header('Location: '.$url); exit; }
function is_post(): bool { return $_SERVER['REQUEST_METHOD'] === 'POST'; }
function flash(string $type,string $message): void { $_SESSION['flash']=[$type,$message]; }
function get_flash(): ?array { $f=$_SESSION['flash']??null; unset($_SESSION['flash']); return $f; }
function csrf(): string { if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function check_csrf(): void {
    if (!hash_equals($_SESSION['csrf']??'', $_POST['csrf']??'')) { http_response_code(419); exit('Token keamanan tidak valid. Silakan kembali dan coba lagi.'); }
}
function auth(): void { if(empty($_SESSION['user'])) redirect('login.php'); }
function admin_only(): void { auth(); if(($_SESSION['user']['role']??'')!=='admin'){ http_response_code(403); exit('Akses hanya untuk admin.'); } }
function current_user(): string { return $_SESSION['user']['name']??'Pengguna'; }
function is_admin(): bool { return ($_SESSION['user']['role']??'')==='admin'; }
function page_start(string $title,string $active=''): void { ?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="description" content="Sistem Informasi Pengelolaan Stok dan Persediaan Barang PT PLN (Persero) ULP Tanjung Pura">
<title><?=e($title)?> - Gudang PLN ULP Tanjung Pura</title><link rel="stylesheet" href="assets/style.css"></head><body>
<div class="layout"><aside class="sidebar">
<div class="side-brand"><span>⚡</span><div><b>PLN</b><small>ULP Tanjung Pura</small></div></div>
<nav>
<a class="<?=$active==='dashboard'?'active':''?>" href="index.php">⌂ Dashboard</a>
<a class="<?=$active==='barang'?'active':''?>" href="barang.php">▣ Data Barang</a>
<a class="<?=$active==='masuk'?'active':''?>" href="transaksi.php?jenis=MASUK">↓ Barang Masuk</a>
<a class="<?=$active==='keluar'?'active':''?>" href="transaksi.php?jenis=KELUAR">↑ Barang Keluar</a>
<a class="<?=$active==='laporan'?'active':''?>" href="laporan.php">▤ Laporan</a>
<?php if(is_admin()): ?><a class="<?=$active==='users'?'active':''?>" href="users.php">♙ Pengguna</a><?php endif; ?>
</nav><div class="side-bottom"><a href="logout.php" onclick="return confirm('Keluar dari sistem?')">↪ Keluar</a></div>
</aside><main class="main"><header class="topbar"><div><h2><?=e($title)?></h2><span>Sistem Informasi Pengelolaan Stok & Persediaan Barang</span></div>
<div class="user-chip">👤 <?=e(current_user())?><small><?=e($_SESSION['user']['role']??'')?></small></div></header>
<?php if($f=get_flash()): ?><div class="alert <?=e($f[0])?>"><?=e($f[1])?></div><?php endif; ?>
<?php }
function page_end(): void { ?></main></div><script>
document.querySelectorAll('.alert').forEach(function(el){setTimeout(function(){el.style.opacity='.75'},4500)});
</script></body></html><?php }
function nav_active_from_tx(string $jenis): string { return $jenis==='KELUAR'?'keluar':'masuk'; }
