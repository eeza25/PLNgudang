<?php
require 'config.php';
if(!empty($_SESSION['user'])) redirect('index.php');
$error='';
if(is_post()){
    check_csrf();
    $username=trim($_POST['username']??''); $password=$_POST['password']??'';
    if($username===''||$password==='') $error='Username dan password wajib diisi.';
    else {
        $s=db()->prepare("SELECT id,name,username,password,role FROM users WHERE username=? AND active=1 LIMIT 1");
        $s->execute([$username]); $u=$s->fetch();
        if($u && password_verify($password,$u['password'])){
            session_regenerate_id(true);
            $_SESSION['user']=['id'=>$u['id'],'name'=>$u['name'],'username'=>$u['username'],'role'=>$u['role']];
            $_SESSION['csrf']=bin2hex(random_bytes(32)); redirect('index.php');
        }
        $error='Username atau password salah.';
    }
}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login - Gudang PLN ULP Tanjung Pura</title><link rel="stylesheet" href="assets/style.css"></head>
<body class="login-page"><div class="login-card"><div class="brand-mark">⚡</div><h1>Gudang PLN</h1><p>ULP Tanjung Pura</p>
<?php if($error): ?><div class="alert danger"><?=e($error)?></div><?php endif; ?>
<form method="post" autocomplete="on"><input type="hidden" name="csrf" value="<?=e(csrf())?>">
<label>Username<input name="username" required autofocus autocomplete="username"></label>
<label>Password<input type="password" name="password" required autocomplete="current-password"></label>
<button class="btn primary full">Masuk ke Sistem</button></form>
<div class="login-note">Akun awal: <b>admin</b> / <b>admin123</b><br><small>Ganti password setelah instalasi.</small></div></div></body></html>
