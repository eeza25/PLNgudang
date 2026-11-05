<?php
require 'config.php'; auth(); $pdo=db();
$stats=[
'barang'=>(int)$pdo->query("SELECT COUNT(*) FROM barang WHERE active=1")->fetchColumn(),
'stok'=>(int)$pdo->query("SELECT COALESCE(SUM(stok),0) FROM barang WHERE active=1")->fetchColumn(),
'masuk'=>(int)$pdo->query("SELECT COALESCE(SUM(jumlah),0) FROM transaksi WHERE jenis='MASUK'")->fetchColumn(),
'keluar'=>(int)$pdo->query("SELECT COALESCE(SUM(jumlah),0) FROM transaksi WHERE jenis='KELUAR'")->fetchColumn(),
'minimum'=>(int)$pdo->query("SELECT COUNT(*) FROM barang WHERE active=1 AND stok<=stok_minimum")->fetchColumn(),
'today'=>(int)$pdo->query("SELECT COUNT(*) FROM transaksi WHERE tanggal=CURDATE()")->fetchColumn()
];
$recent=$pdo->query("SELECT t.*,b.nama_barang,b.kode_barang,b.satuan,u.name FROM transaksi t JOIN barang b ON b.id=t.barang_id LEFT JOIN users u ON u.id=t.user_id ORDER BY t.id DESC LIMIT 10")->fetchAll();
$low=$pdo->query("SELECT * FROM barang WHERE active=1 AND stok<=stok_minimum ORDER BY stok ASC,nama_barang LIMIT 10")->fetchAll();
page_start('Dashboard','dashboard');
?>
<section class="cards">
<div class="stat"><span>Total Barang</span><strong><?=$stats['barang']?></strong><i>▣</i></div>
<div class="stat"><span>Total Stok</span><strong><?=number_format($stats['stok'])?></strong><i>◈</i></div>
<div class="stat"><span>Barang Masuk</span><strong><?=number_format($stats['masuk'])?></strong><i>↓</i></div>
<div class="stat"><span>Barang Keluar</span><strong><?=number_format($stats['keluar'])?></strong><i>↑</i></div>
<div class="stat"><span>Transaksi Hari Ini</span><strong><?=$stats['today']?></strong><i>▤</i></div>
<div class="stat alert-stat"><span>Stok Minimum</span><strong><?=$stats['minimum']?></strong><i>!</i></div>
</section>
<div class="grid-2">
<section class="panel"><div class="panel-title"><h3>Transaksi Terbaru</h3><a href="laporan.php">Lihat laporan →</a></div>
<div class="table-wrap"><table><thead><tr><th>Tanggal</th><th>Barang</th><th>Jenis</th><th>Jumlah</th><th>Petugas</th></tr></thead><tbody>
<?php foreach($recent as $r): ?><tr><td><?=e(date('d-m-Y',strtotime($r['tanggal'])))?></td><td><b><?=e($r['nama_barang'])?></b><br><small><?=e($r['kode_barang'])?></small></td><td><span class="badge <?=$r['jenis']==='MASUK'?'green':'orange'?>"><?=e($r['jenis'])?></span></td><td><b><?=number_format((int)$r['jumlah'])?></b> <?=e($r['satuan'])?></td><td><?=e($r['name']??'-')?></td></tr><?php endforeach; ?>
<?php if(!$recent): ?><tr><td colspan="5" class="empty">Belum ada transaksi.</td></tr><?php endif; ?></tbody></table></div></section>
<section class="panel"><div class="panel-title"><h3>Perlu Restock</h3><span class="badge red"><?=$stats['minimum']?> item</span></div>
<div class="table-wrap"><table><thead><tr><th>Barang</th><th>Stok</th><th>Min.</th></tr></thead><tbody>
<?php foreach($low as $b): ?><tr><td><b><?=e($b['nama_barang'])?></b><br><small><?=e($b['kode_barang'])?></small></td><td><b class="danger-text"><?=number_format((int)$b['stok'])?></b></td><td><?=number_format((int)$b['stok_minimum'])?></td></tr><?php endforeach; ?>
<?php if(!$low): ?><tr><td colspan="3" class="empty">Semua stok aman.</td></tr><?php endif; ?></tbody></table></div></section>
</div>
<section class="quick"><a href="barang.php?action=add" class="btn primary">＋ Tambah Barang</a><a href="transaksi.php?jenis=MASUK" class="btn">↓ Catat Barang Masuk</a><a href="transaksi.php?jenis=KELUAR" class="btn">↑ Catat Barang Keluar</a><a href="laporan.php" class="btn">▤ Lihat Laporan</a></section>
<?php page_end(); ?>
