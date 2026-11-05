CREATE DATABASE IF NOT EXISTS pln_inventory CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pln_inventory;

CREATE TABLE IF NOT EXISTS users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 username VARCHAR(50) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL,
 role ENUM('admin','petugas') NOT NULL DEFAULT 'petugas',
 active TINYINT(1) NOT NULL DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS barang (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 kode_barang VARCHAR(50) NOT NULL UNIQUE,
 nama_barang VARCHAR(150) NOT NULL,
 kategori VARCHAR(100) NOT NULL DEFAULT 'Material Teknik',
 satuan VARCHAR(30) NOT NULL DEFAULT 'unit',
 stok INT NOT NULL DEFAULT 0,
 stok_minimum INT NOT NULL DEFAULT 5,
 lokasi VARCHAR(100) NOT NULL DEFAULT 'Gudang Utama',
 active TINYINT(1) NOT NULL DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS transaksi (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 jenis ENUM('MASUK','KELUAR') NOT NULL,
 barang_id INT UNSIGNED NOT NULL,
 jumlah INT NOT NULL,
 tanggal DATE NOT NULL,
 referensi VARCHAR(100) DEFAULT NULL,
 keterangan TEXT DEFAULT NULL,
 user_id INT UNSIGNED DEFAULT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_trans_barang FOREIGN KEY (barang_id) REFERENCES barang(id),
 CONSTRAINT fk_trans_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
 INDEX idx_trans_tanggal (tanggal),
 INDEX idx_trans_jenis (jenis)
) ENGINE=InnoDB;

INSERT INTO users(name,username,password,role,active) VALUES
('Administrator','admin','$2y$12$pZo/qQsj9f4.gNAJSJYSh.6ldRaM.xkVFTqK89jltYE4XaJzFteum','admin',1)
ON DUPLICATE KEY UPDATE username=username;

INSERT INTO barang(kode_barang,nama_barang,kategori,satuan,stok,stok_minimum,lokasi) VALUES
('MAT-001','Kabel NYY 4x10 mm²','Kabel','meter',0,50,'Rak A-01'),
('MAT-002','MCB 3P 16A','Proteksi','unit',0,5,'Rak B-02'),
('MAT-003','Isolasi Listrik','Perlengkapan','roll',0,10,'Rak C-01')
ON DUPLICATE KEY UPDATE kode_barang=VALUES(kode_barang);
