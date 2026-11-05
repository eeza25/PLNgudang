<?php
require 'config.php'; auth(); $pdo=db();
$action=$_GET['action']??'list'; $id=(int)($_GET['id']??0);
if(is_post()){
 check_csrf(); $act=$_POST['act']??'';
 if($act==='save'){
  $id=(int)($_POST['id']??0); $kode=trim($_POST['kode_barang']??''); $nama=trim($_POST['nama_barang']??'');
  $kategori=trim($_POST['kategori']??'Material Teknik'); $satuan=trim($_POST['satuan']??'unit'); $stok=max(0,(int)($_POST['stok']??0)); $min=max(0,(int)($_POST['stok_minimum']??5)); $lokasi=trim($_POST['lokasi']??'Gudang Utama');
  if($kode===''||$nama===''||$satuan===''){flash('danger','Kode, nama barang, dan satuan wajib diisi.');redirect($id?'barang.php?action=edit&id='.$id:'barang.php?action=add');}
  try{
   if($id){$q=$pdo->prepare("UPDATE barang SET kode_barang=?,nama_barang=?,kategori=?,satuan=?,stok=?,stok_minimum=?,lokasi=? WHERE id=? AND active=1");$q->execute([$kode,$nama,$kategori,$satuan,$stok,$min,$lokasi,$id]);flash('success','Data barang berhasil diperbarui.');}
   else{$q=$pdo->prepare("INSERT INTO barang(kode_barang,nama_barang,kategori,satuan,stok,stok_minimum,lokasi) VALUES(?,?,?,?,?,?,?)");$q->execute([$kode,$nama,$kategori,$satuan,$stok,$min,$lokasi]);flash('success','Barang berhasil ditambahkan.');}
  }catch(PDOException $e){flash('danger',str_contains($e->getMessage(),'Duplicate')?'Kode barang sudah digunakan.':'Data barang gagal disimpan.');}
  redirect('barang.php');
 }
 if($act==='delete'){ $bid=(int)$_POST['id']; $pdo->prepare("UPDATE barang SET active=0 WHERE id=?")->execute([$bid]); flash('success','Barang diarsipkan.'); redirect('barang.php');}
}
$item=null;if($action==='edit'){ $s=$pdo->prepare("SELECT * FROM barang WHERE id=? AND active=1");$s->execute([$id]);$item=$s->fetch();if(!$item)redirect('barang.php');}
$q=trim($_GET['q']??'');$s=$pdo->prepare("SELECT * FROM barang WHERE active=1 AND (kode_barang LIKE ? OR nama_barang LIKE ? OR kategori LIKE ?) ORDER BY nama_barang");$like="%$q%";$s->execute([$like,$like,$like]);$rows=$s->fetchAll();
page_start($action==='list'?'Data Barang':($action==='edit'?'Edit Barang':'Tambah Barang'),'barang');
?>
<?php if($action!=='list'): ?>
<section class="panel form-panel"><div class="panel-title"><h3><?=$action==='edit'?'Edit Barang':'Tambah Barang'?></h3><a href="barang.php">← Kembali</a></div>
<form method="post" class="form-grid"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?=e($item['id']??0)?>">
<label>Kode Barang<input name="kode_barang" required maxlength="50" value="<?=e($item['kode_barang']??'')?>" placeholder="MAT-004"></label>
<label>Nama Barang<input name="nama_barang" required maxlength="150" value="<?=e($item['nama_barang']??'')?>" placeholder="Nama material/peralatan"></label>
<label>Kategori<input name="kategori" maxlength="100" value="<?=e($item['kategori']??'Material Teknik')?>"></label>
<label>Satuan<input name="satuan" required maxlength="30" value="<?=e($item['satuan']??'unit')?>" placeholder="unit / pcs / meter"></label>
<label>Stok Awal / Stok Saat Ini<input type="number" min="0" name="stok" required value="<?=e($item['stok']??0)?>"></label>
<label>Stok Minimum<input type="number" min="0" name="stok_minimum" required value="<?=e($item['stok_minimum']??5)?>"></label>
<label class="span-2">Lokasi Rak<input name="lokasi" maxlength="100" value="<?=e($item['lokasi']??'Gudang Utama')?>"></label>
<div class="span-2 form-help">Catatan: untuk perubahan stok rutin, gunakan menu <b>Barang Masuk/Keluar</b> agar riwayat transaksi tetap tercatat.</div>
<div class="span-2"><button class="btn primary">Simpan Data</button></div></form></section>
<?php else: ?>
<section class="panel"><div class="toolbar"><form method="get"><input name="q" value="<?=e($q)?>" placeholder="Cari kode, nama, kategori..." aria-label="Cari barang"><button class="btn">Cari</button><?php if($q): ?><a class="btn" href="barang.php">Reset</a><?php endif; ?></form><a class="btn primary" href="barang.php?action=add">＋ Tambah Barang</a></div>
<div class="table-wrap"><table><thead><tr><th>No</th><th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Stok</th><th>Satuan</th><th>Minimum</th><th>Lokasi</th><th>Aksi</th></tr></thead><tbody>
<?php foreach($rows as $i=>$r): ?><tr><td><?=$i+1?></td><td><b><?=e($r['kode_barang'])?></b></td><td><?=e($r['nama_barang'])?></td><td><?=e($r['kategori'])?></td><td><b class="<?=$r['stok']<=$r['stok_minimum']?'danger-text':''?>"><?=number_format((int)$r['stok'])?></b></td><td><?=e($r['satuan'])?></td><td><?=number_format((int)$r['stok_minimum'])?></td><td><?=e($r['lokasi'])?></td><td class="actions"><a href="barang.php?action=edit&id=<?=$r['id']?>">Edit</a><form method="post" onsubmit="return confirm('Arsipkan barang ini?')"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?=$r['id']?>"><button>Arsip</button></form></td></tr><?php endforeach; ?>
<?php if(!$rows): ?><tr><td colspan="9" class="empty">Data barang belum tersedia.</td></tr><?php endif; ?></tbody></table></div></section>
<?php endif; page_end(); ?>
