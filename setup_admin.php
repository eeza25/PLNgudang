<?php
require 'config.php';
$pdo=db();
$hash=password_hash('admin123',PASSWORD_DEFAULT);
$pdo->prepare("INSERT INTO users(name,username,password,role,active) VALUES('Administrator','admin',?,'admin',1)
ON DUPLICATE KEY UPDATE password=VALUES(password),role='admin',active=1")->execute([$hash]);
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Setup Admin</title><link rel="stylesheet" href="assets/style.css"></head>
<body class="login-page"><div class="login-card"><div class="brand-mark">✓</div><h1>Admin siap</h1><p>Akun administrator berhasil dibuat/reset.</p><div class="alert success">Username: <b>admin</b><br>Password: <b>admin123</b></div><a class="btn primary full" href="login.php">Ke halaman login</a><p class="security-note">Setelah berhasil login, hapus atau rename file <b>setup_admin.php</b>.</p></div></body></html>
