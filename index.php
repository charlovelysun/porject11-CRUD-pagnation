<?php
require_once 'koneksi.php';

// PROSES HAPUS SISWA
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $queryHapus = "DELETE FROM siswa WHERE id = $id";
    if (mysqli_query($koneksi, $queryHapus)) {
        header("Location: index.php?msg=deleted");
        exit();
    }
}

// === LOGIKA PAGINASI (PAGINATION) ===
$limit = 5; // Jumlah data per halaman
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
if ($halaman < 1) { $halaman = 1; }

$offset = ($halaman - 1) * $limit;

// 1. Hitung total seluruh data siswa
$queryTotal  = "SELECT COUNT(*) AS total FROM siswa";
$resultTotal = mysqli_query($koneksi, $queryTotal);
$rowTotal    = mysqli_fetch_assoc($resultTotal);
$totalData   = $rowTotal['total'];

// 2. Hitung total halaman yang dibutuhkan
$totalHalaman = ceil($totalData / $limit);

// 3. Ambil data siswa sesuai batas LIMIT dan OFFSET
$query  = "SELECT * FROM siswa ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Proyek 11 - CRUD Siswa + Pagination</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 40px; }
        .card { max-width: 900px; margin: auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn { padding: 8px 14px; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: bold; border: none; }
        .btn-add { background: #28a745; }
        .btn-edit { background: #ffc107; color: #212529; }
        .btn-delete { background: #dc3545; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #007bff; color: white; }
        .alert { padding: 10px; background: #d4edda; color: #155724; border-radius: 4px; margin-bottom: 15px; }

        /* Style Pagination */
        .pagination { display: flex; justify-content: center; gap: 5px; margin-top: 25px; }
        .pagination a { padding: 8px 12px; border: 1px solid #007bff; color: #007bff; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .pagination a.active { background: #007bff; color: white; font-weight: bold; }
        .pagination a.disabled { color: #ccc; border-color: #ccc; pointer-events: none; }
    </style>
</head>
<body>

<div class="card">
    <h2>Data Siswa (Dengan Pagination)</h2>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert">Data siswa berhasil dihapus!</div>
    <?php endif; ?>

    <div class="top-bar">
        <a href="tambah.php" class="btn btn-add">+ Tambah Siswa Baru</a>
        <span style="font-size: 14px; color: #666;">Total Data: <strong><?= $totalData; ?></strong> Siswa</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama Lengkap</th>
                <th>Kelas</th>
                <th>Jurusan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php 
                $no = $offset + 1; // Penomoran tabel menyesuaikan halaman
                while ($row = mysqli_fetch_assoc($result)): 
                ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['nis']); ?></td>
                        <td><?= htmlspecialchars($row['nama']); ?></td>
                        <td><?= htmlspecialchars($row['kelas']); ?></td>
                        <td><?= htmlspecialchars($row['jurusan']); ?></td>
                        <td>
                            <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-edit">Edit</a>
                            <a href="index.php?hapus=<?= $row['id']; ?>&halaman=<?= $halaman; ?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus data siswa ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #888;">Belum ada data siswa.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- NAVIGASI PAGINASI -->
    <?php if ($totalHalaman > 1): ?>
        <div class="pagination">
            <!-- Tombol Previous -->
            <?php if ($halaman > 1): ?>
                <a href="?halaman=<?= $halaman - 1; ?>">&laquo; Prev</a>
            <?php else: ?>
                <a class="disabled">&laquo; Prev</a>
            <?php endif; ?>

            <!-- Angka Halaman -->
            <?php for ($i = 1; $i <= $totalHalaman; $i++): ?>
                <a href="?halaman=<?= $i; ?>" class="<?= ($i == $halaman) ? 'active' : ''; ?>">
                    <?= $i; ?>
                </a>
            <?php endfor; ?>

            <!-- Tombol Next -->
            <?php if ($halaman < $totalHalaman): ?>
                <a href="?halaman=<?= $halaman + 1; ?>">Next &raquo;</a>
            <?php else: ?>
                <a class="disabled">Next &raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>