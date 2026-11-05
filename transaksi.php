<?php
require 'config.php'; auth(); $pdo=db();
$jenis=$_GET['jenis']??'MASUK'; if(!in_array($jenis,['MASUK','KELUAR'],true))$jenis='MASUK';
if(is_post()){
 check_csrf(); $barang=(int)($_POST['barang_id']??0);$jumlah=(int)($_POST['jumlah']??0);$tanggal=$_POST['tanggal']??date('Y-m-d');$keterangan=trim($_POST['keterangan']??'');$ref=trim($_POST['referensi']??'');
 if($barang<=0||$jumlah<=0){flash('danger','Barang dan jumlah harus diisi dengan benar.');redirect("transaksi.php?jenis=$jenis");}
 if($tanggal>date('Y-m-d')){flash('danger','Tanggal transaksi tidak boleh melebihi hari ini.');redirect("transaksi.php?jenis=$jenis");}
 try{$pdo->beginTransaction();
  $s=$pdo->prepare("SELECT id,stok FROM barang WHERE id=? AND active=1 FOR UPDATE");$s->execute([$barang]);$b=$s->fetch();if(!$b)throw new RuntimeException('Barang tidak ditemukan.');
  if($jenis==='KELUAR' && (int)$b['stok']<$jumlah)throw new RuntimeException('Stok tidak mencukupi. Stok tersedia: '.$b['stok'].'.');
  $delta=$jenis==='MASUK'?$jumlah:-$jumlah;
  $pdo->prepare("UPDATE barang SET stok=stok+? WHERE id=?")->execute([$delta,$barang]);
  $pdo->prepare("INSERT INTO transaksi(jenis,barang_id,jumlah,tanggal,referensi,keterangan,user_id) VALUES(?,?,?,?,?,?,?)")->execute([$jenis,$barang,$jumlah,$tanggal,$ref?:null,$keterangan?:null,$_SESSION['user']['id']]);
  $pdo->commit();flash('success',"Barang $jenis berhasil dicatat. Stok telah diperbarui.");
 }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();flash('danger',$e->getMessage());}
 redirect("transaksi.php?jenis=$jenis");
}
$barang=$pdo->query("SELECT id,kode_barang,nama_barang,stok,satuan FROM barang WHERE active=1 ORDER BY nama_barang")->fetchAll();
$hist=$pdo->prepare("SELECT t.*,b.nama_barang,b.kode_barang,b.satuan,u.name FROM transaksi t JOIN barang b ON b.id=t.barang_id LEFT JOIN users u ON u.id=t.user_id WHERE t.jenis=? ORDER BY t.tanggal DESC,t.id DESC LIMIT 50");$hist->execute([$jenis]);$hist=$hist->fetchAll();
page_start('Barang '.ucfirst(strtolower($jenis)),nav_active_from_tx($jenis));
?>
<section class="panel form-panel"><div class="panel-title"><h3>Input Barang <?=e($jenis)?></h3><span class="badge <?=$jenis==='MASUK'?'green':'orange'?>"><?=$jenis?></span></div>
<form method="post" class="form-grid"><input type="hidden" name="csrf" value="<?=e(csrf())?>">
<label>Barang<select name="barang_id" required><option value="">-- Pilih barang --</option><?php foreach($barang as $b): ?><option value="<?=$b['id']?>"><?=e($b['kode_barang'].' — '.$b['nama_barang'])?> (stok: <?=number_format((int)$b['stok'])?> <?=e($b['satuan'])?>)</option><?php endforeach; ?></select></label>
<label>Jumlah<input type="number" name="jumlah" min="1" required></label><label>Tanggal<input type="date" name="tanggal" value="<?=date('Y-m-d')?>" max="<?=date('Y-m-d')?>" required></label><label>Nomor Referensi<input name="referensi" maxlength="100" placeholder="SPB / DO / nota"></label>
<label class="span-2">Keterangan<textarea name="keterangan" rows="3" maxlength="1000" placeholder="Keterangan transaksi (opsional)"></textarea></label><div class="span-2"><button class="btn primary">Simpan Transaksi</button></div></form></section>
<section class="panel"><div class="panel-title"><h3>Riwayat Barang <?=e($jenis)?></h3><span>50 transaksi terakhir</span></div><div class="table-wrap"><table><thead><tr><th>Tanggal</th><th>Barang</th><th>Jumlah</th><th>Referensi</th><th>Petugas</th><th>Keterangan</th></tr></thead><tbody>
<?php foreach($hist as $r): ?><tr><td><?=e(date('d-m-Y',strtotime($r['tanggal'])))?></td><td><b><?=e($r['nama_barang'])?></b><br><small><?=e($r['kode_barang'])?></small></td><td><b><?=number_format((int)$r['jumlah'])?></b> <?=e($r['satuan'])?></td><td><?=e($r['referensi']??'-')?></td><td><?=e($r['name']??'-')?></td><td><?=e($r['keterangan']??'-')?></td></tr><?php endforeach; ?><?php if(!$hist): ?><tr><td colspan="6" class="empty">Belum ada transaksi.</td></tr><?php endif; ?></tbody></table></div></section>
<?php page_end(); ?>
