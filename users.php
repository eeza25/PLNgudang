<?php
require 'config.php'; admin_only(); $pdo=db();
if(is_post()){
 check_csrf();$act=$_POST['act']??'';
 if($act==='save'){
  $id=(int)($_POST['id']??0);$name=trim($_POST['name']??'');$username=trim($_POST['username']??'');$role=($_POST['role']??'')==='admin'?'admin':'petugas';$pass=$_POST['password']??'';
  if($name===''||$username===''){flash('danger','Nama dan username wajib diisi.');redirect($id?'users.php?edit='.$id:'users.php');}
  if(!$id && strlen($pass)<6){flash('danger','Password pengguna baru minimal 6 karakter.');redirect('users.php');}
  try{
   if($id){
    if($pass!==''){$pdo->prepare("UPDATE users SET name=?,username=?,role=?,password=? WHERE id=?")->execute([$name,$username,$role,password_hash($pass,PASSWORD_DEFAULT),$id]);}
    else{$pdo->prepare("UPDATE users SET name=?,username=?,role=? WHERE id=?")->execute([$name,$username,$role,$id]);}
   }else{$pdo->prepare("INSERT INTO users(name,username,password,role) VALUES(?,?,?,?)")->execute([$name,$username,password_hash($pass,PASSWORD_DEFAULT),$role]);}
   flash('success','Data pengguna berhasil disimpan.');
  }catch(PDOException $e){flash('danger',str_contains($e->getMessage(),'Duplicate')?'Username sudah digunakan.':'Data pengguna gagal disimpan.');}
  redirect('users.php');
 }
 if($act==='delete'){
  $id=(int)($_POST['id']??0);
  if($id===$_SESSION['user']['id'])flash('danger','Akun yang sedang digunakan tidak boleh dihapus.');
  else{$pdo->prepare("UPDATE users SET active=0 WHERE id=?")->execute([$id]);flash('success','Pengguna dinonaktifkan.');}
  redirect('users.php');
 }
}
$edit=null;if(isset($_GET['edit'])){$s=$pdo->prepare("SELECT * FROM users WHERE id=? AND active=1");$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();if(!$edit)redirect('users.php');}
$rows=$pdo->query("SELECT id,name,username,role,active,created_at FROM users WHERE active=1 ORDER BY name")->fetchAll();
page_start('Manajemen Pengguna','users');
?>
<section class="panel form-panel"><div class="panel-title"><h3><?=$edit?'Edit Pengguna':'Tambah Pengguna'?></h3><?php if($edit): ?><a href="users.php">＋ Pengguna baru</a><?php endif; ?></div>
<form method="post" class="form-grid"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?=e($edit['id']??0)?>">
<label>Nama<input name="name" required maxlength="100" value="<?=e($edit['name']??'')?>"></label><label>Username<input name="username" required maxlength="50" value="<?=e($edit['username']??'')?>"></label>
<label>Password<input type="password" name="password" <?=$edit?'':'required'?> minlength="6" placeholder="<?=$edit?'Kosongkan jika tidak diubah':'Minimal 6 karakter'?>"></label>
<label>Role<select name="role"><option value="petugas" <?=($edit['role']??'')==='petugas'?'selected':''?>>Petugas</option><option value="admin" <?=($edit['role']??'')==='admin'?'selected':''?>>Admin</option></select></label>
<div class="span-2"><button class="btn primary">Simpan Pengguna</button></div></form></section>
<section class="panel"><div class="panel-title"><h3>Daftar Pengguna Aktif</h3><span><?=count($rows)?> pengguna</span></div><div class="table-wrap"><table><thead><tr><th>Nama</th><th>Username</th><th>Role</th><th>Dibuat</th><th>Aksi</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><b><?=e($r['name'])?></b></td><td><?=e($r['username'])?></td><td><span class="badge <?=$r['role']==='admin'?'blue':''?>"><?=e($r['role'])?></span></td><td><?=e(date('d-m-Y H:i',strtotime($r['created_at'])))?></td><td class="actions"><a href="users.php?edit=<?=$r['id']?>">Edit</a><?php if($r['id']!=$_SESSION['user']['id']): ?><form method="post" onsubmit="return confirm('Nonaktifkan pengguna ini?')"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?=$r['id']?>"><button>Nonaktifkan</button></form><?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div></section>
<?php page_end(); ?>
