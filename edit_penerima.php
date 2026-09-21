<?php
include 'partials/header.php';
include 'config/db.php';
include 'config/kriteria.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>ID tidak ditemukan!</div></div>";
    include 'partials/footer.php';
    exit;
}

$query = "SELECT * FROM penerima WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$penerima = $result->fetch_assoc();

if (!$penerima) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Data penerima tidak ditemukan!</div></div>";
    include 'partials/footer.php';
    exit;
}
?>

<div class="container mt-4">
    <h2>Edit Data Penerima</h2>
    <form action="update_penerima.php" method="POST">
        <input type="hidden" name="id" value="<?= $penerima['id'] ?>">

        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control" value="<?= $penerima['nama'] ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control" required><?= $penerima['alamat'] ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Pendapatan</label>
            <select name="pendapatan" class="form-select" required>
                <?php foreach ($opsi['pendapatan'] as $key => $val): ?>
                    <option value="<?= $key ?>" <?= $penerima['pendapatan'] == $key ? 'selected' : '' ?>>
                        <?= $val ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Pekerjaan</label>
            <select name="pekerjaan" class="form-select" required>
                <?php foreach ($opsi['pekerjaan'] as $key => $val): ?>
                    <option value="<?= $key ?>" <?= $penerima['pekerjaan'] == $key ? 'selected' : '' ?>>
                        <?= $val ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggungan</label>
            <select name="tanggungan" class="form-select" required>
                <?php foreach ($opsi['tanggungan'] as $key => $val): ?>
                    <option value="<?= $key ?>" <?= $penerima['tanggungan'] == $key ? 'selected' : '' ?>>
                        <?= $val ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Rumah</label>
            <select name="rumah" class="form-select" required>
                <?php foreach ($opsi['rumah'] as $key => $val): ?>
                    <option value="<?= $key ?>" <?= $penerima['rumah'] == $key ? 'selected' : '' ?>>
                        <?= $val ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        <a href="penerima.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

<?php include 'partials/footer.php'; ?>