<?php
include 'partials/header.php';
include 'config/db.php';
include 'config/kriteria.php';

// Ambil data penerima dengan pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Ambil total data untuk pagination
$countQuery = "SELECT COUNT(*) as total FROM penerima";
$countResult = $conn->query($countQuery);
$totalData = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

// Query dengan search dan filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

$whereClause = "WHERE 1=1";
if (!empty($search)) {
    $whereClause .= " AND (nama LIKE '%$search%' OR nik LIKE '%$search%' OR hp LIKE '%$search%')";
}
if (!empty($filter) && $filter !== 'all') {
    $whereClause .= " AND golongan = '$filter'";
}

$query = "SELECT * FROM penerima $whereClause ORDER BY saw_score DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($query);

// Statistik untuk dashboard cards
$statsQuery = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN golongan = 'A' THEN 1 ELSE 0 END) as golA,
    SUM(CASE WHEN golongan = 'B' THEN 1 ELSE 0 END) as golB,
    SUM(CASE WHEN golongan = 'C' THEN 1 ELSE 0 END) as golC,
    SUM(CASE WHEN golongan = 'Tidak Layak' THEN 1 ELSE 0 END) as tidakLayak
    FROM penerima";
$statsResult = $conn->query($statsQuery);
$stats = $statsResult->fetch_assoc();
?>

<!-- Hero Section dengan Statistics -->
<div class="hero-stats-section py-5">
    <div class="container">
        <div class="row align-items-center mb-4">
            <div class="col-md-8">
                <div class="page-header">
                    <div class="page-badge">
                        <i class="bi bi-people-fill"></i>
                        Data Management
                    </div>
                    <h1 class="page-title">
                        Daftar Penerima <span class="text-gradient">Bantuan</span>
                    </h1>
                    <p class="page-description">
                        Kelola dan pantau data penerima bantuan sosial dengan sistem peringkat berbasis metode SAW
                    </p>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <a href="registrasi.php" class="btn btn-primary btn-action">
                    <i class="bi bi-plus-circle"></i>
                    <span>Tambah Penerima</span>
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-5">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card primary">
                    <div class="stats-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-number"><?= number_format($stats['total']) ?></div>
                        <div class="stats-label">Total Penerima</div>
                    </div>
                    <div class="stats-trend up">
                        <i class="bi bi-arrow-up"></i> +12%
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card success">
                    <div class="stats-icon">
                        <i class="bi bi-trophy"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-number"><?= number_format($stats['golA']) ?></div>
                        <div class="stats-label">Golongan A</div>
                    </div>
                    <div class="stats-percentage">
                        <?= $stats['total'] > 0 ? round(($stats['golA'] / $stats['total']) * 100) : 0 ?>%
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card info">
                    <div class="stats-icon">
                        <i class="bi bi-award"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-number"><?= number_format($stats['golB']) ?></div>
                        <div class="stats-label">Golongan B</div>
                    </div>
                    <div class="stats-percentage">
                        <?= $stats['total'] > 0 ? round(($stats['golB'] / $stats['total']) * 100) : 0 ?>%
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card warning">
                    <div class="stats-icon">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-number"><?= number_format($stats['golC'] + $stats['tidakLayak']) ?></div>
                        <div class="stats-label">Perlu Evaluasi</div>
                    </div>
                    <div class="stats-percentage">
                        <?= $stats['total'] > 0 ? round((($stats['golC'] + $stats['tidakLayak']) / $stats['total']) * 100) : 0 ?>%
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Data Section -->
<div class="container pb-5">
    <div class="data-table-card">
        <!-- Search and Filter Section -->
        <div class="table-controls">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="search-box">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="form-control" id="searchInput" 
                               placeholder="Cari nama, NIK, atau nomor HP..." 
                               value="<?= htmlspecialchars($search) ?>">
                        <button class="search-clear" id="clearSearch" style="display: none;">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="filter-controls">
                        <select class="form-select filter-select" id="filterSelect">
                            <option value="all" <?= $filter === 'all' || empty($filter) ? 'selected' : '' ?>>Semua Golongan</option>
                            <option value="A" <?= $filter === 'A' ? 'selected' : '' ?>>Golongan A</option>
                            <option value="B" <?= $filter === 'B' ? 'selected' : '' ?>>Golongan B</option>
                            <option value="C" <?= $filter === 'C' ? 'selected' : '' ?>>Golongan C</option>
                            <option value="Tidak Layak" <?= $filter === 'Tidak Layak' ? 'selected' : '' ?>>Tidak Layak</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="table data-table">
                    <thead>
                        <tr>
                            <th>
                                <div class="th-content">
                                    <span>Rank</span>
                                </div>
                            </th>
                            <th>
                                <div class="th-content sortable" data-sort="nama">
                                    <span>Penerima</span>
                                    <i class="bi bi-arrow-down-up sort-icon"></i>
                                </div>
                            </th>
                            <th>
                                <div class="th-content">
                                    <span>Kontak</span>
                                </div>
                            </th>
                            <th>
                                <div class="th-content">
                                    <span>Alamat</span>
                                </div>
                            </th>
                            <th>
                                <div class="th-content sortable" data-sort="saw_score">
                                    <span>SAW Score</span>
                                    <i class="bi bi-arrow-down-up sort-icon"></i>
                                </div>
                            </th>
                            <th>
                                <div class="th-content">
                                    <span>Status</span>
                                </div>
                            </th>
                            <th>
                                <div class="th-content">
                                    <span>Kuota</span>
                                </div>
                            </th>
                            <th>
                                <div class="th-content">
                                    <span>Aksi</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rank = $offset + 1;
                        if ($result->num_rows > 0):
                            while ($row = $result->fetch_assoc()): 
                        ?>
                            <tr class="table-row" data-aos="fade-up" data-aos-delay="<?= ($rank - $offset) * 50 ?>">
                                <td>
                                    <div class="rank-badge rank-<?= $rank <= 3 ? $rank : 'default' ?>">
                                        <?php if ($rank <= 3): ?>
                                            <i class="bi bi-trophy-fill"></i>
                                        <?php endif; ?>
                                        <?= $rank ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <div class="user-details">
                                            <div class="user-name"><?= htmlspecialchars($row['nama']) ?></div>
                                            <div class="user-nik">NIK: <?= htmlspecialchars($row['nik']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="contact-info">
                                        <div class="contact-item">
                                            <i class="bi bi-phone-fill"></i>
                                            <span><?= htmlspecialchars($row['hp']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="address-info">
                                        <i class="bi bi-geo-alt-fill"></i>
                                        <span><?= htmlspecialchars($row['alamat']) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="score-display">
                                        <div class="score-value"><?= number_format($row['saw_score'], 3) ?></div>
                                        <div class="score-bar">
                                            <div class="score-fill" style="width: <?= ($row['saw_score'] * 100) ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $golongan = $row['golongan'];
                                    $badgeClass = '';
                                    $icon = '';
                                    switch ($golongan) {
                                        case 'A':
                                            $badgeClass = 'success';
                                            $icon = 'bi-trophy-fill';
                                            break;
                                        case 'B':
                                            $badgeClass = 'primary';
                                            $icon = 'bi-award-fill';
                                            break;
                                        case 'C':
                                            $badgeClass = 'warning';
                                            $icon = 'bi-star-fill';
                                            break;
                                        default:
                                            $badgeClass = 'danger';
                                            $icon = 'bi-x-circle-fill';
                                    }
                                    ?>
                                    <span class="status-badge status-<?= $badgeClass ?>">
                                        <i class="bi <?= $icon ?>"></i>
                                        <?= $golongan ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="quota-display">
                                        <?php if ($row['koutaBantuan'] > 0): ?>
                                            <span class="quota-amount"><?= number_format($row['koutaBantuan']) ?></span>
                                            <span class="quota-unit">gram</span>
                                        <?php else: ?>
                                            <span class="quota-none">-</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn edit" onclick="editPenerima(<?= $row['id'] ?>)" 
                                                title="Edit Data">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="action-btn view" onclick="viewDetails(<?= $row['id'] ?>)" 
                                                title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="action-btn delete" onclick="deletePenerima(<?= $row['id'] ?>)" 
                                                title="Hapus Data">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php 
                            $rank++;
                            endwhile; 
                        else: 
                        ?>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="no-data">
                                        <i class="bi bi-inbox"></i>
                                        <h5>Tidak ada data ditemukan</h5>
                                        <p>Belum ada data penerima yang tersedia</p>
                                        <a href="registrasi.php" class="btn btn-primary">
                                            <i class="bi bi-plus-circle"></i> Tambah Data Pertama
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination-wrapper">
            <div class="pagination-info">
                Menampilkan <?= $offset + 1 ?> - <?= min($offset + $limit, $totalData) ?> dari <?= $totalData ?> data
            </div>
            <nav aria-label="Table pagination">
                <ul class="pagination">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&filter=<?= urlencode($filter) ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&filter=<?= urlencode($filter) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                    
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&filter=<?= urlencode($filter) ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-lines-fill"></i>
                    Detail Penerima
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<style>
/* Hero Stats Section */
.hero-stats-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    margin-top: -90px;
    padding-top: 120px;
}

.page-header {
    position: relative;
}

.page-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    padding: 8px 16px;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 15px;
}

.page-title {
    font-size: 3rem;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 15px;
}

.text-gradient {
    background: linear-gradient(45deg, #ffd700, #ff6b6b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.page-description {
    font-size: 1.1rem;
    opacity: 0.9;
    max-width: 600px;
}

.btn-action {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px 25px;
    font-weight: 600;
    border-radius: 15px;
    background: linear-gradient(45deg, #28a745, #20c997);
    border: none;
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
    transition: all 0.3s ease;
}

.btn-action:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(40, 167, 69, 0.4);
}

/* Statistics Cards */
.stats-card {
    background: white;
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--card-color);
}

.stats-card.primary { --card-color: #007bff; }
.stats-card.success { --card-color: #28a745; }
.stats-card.info { --card-color: #17a2b8; }
.stats-card.warning { --card-color: #ffc107; }

.stats-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    background: linear-gradient(45deg, var(--card-color), var(--card-color));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    margin-bottom: 15px;
}

.stats-number {
    font-size: 2.2rem;
    font-weight: 800;
    color: #2c3e50;
    line-height: 1;
}

.stats-label {
    color: #6c757d;
    font-weight: 500;
    font-size: 0.95rem;
}

.stats-trend {
    position: absolute;
    top: 20px;
    right: 20px;
    color: #28a745;
    font-size: 0.85rem;
    font-weight: 600;
}

.stats-percentage {
    position: absolute;
    top: 20px;
    right: 20px;
    background: rgba(var(--card-color), 0.1);
    color: var(--card-color);
    padding: 4px 8px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
}

/* Data Table Card */
.data-table-card {
    background: white;
    border-radius: 25px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.table-controls {
    padding: 25px;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

.search-box {
    position: relative;
}

.search-box .form-control {
    padding-left: 45px;
    padding-right: 40px;
    border-radius: 15px;
    border: 2px solid #e9ecef;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.search-box .form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.1);
}

.search-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    font-size: 16px;
}

.search-clear {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6c757d;
    padding: 5px;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.search-clear:hover {
    background: #f8f9fa;
    color: #dc3545;
}

.filter-controls {
    display: flex;
    gap: 15px;
    align-items: center;
}

.filter-select {
    border-radius: 15px;
    border: 2px solid #e9ecef;
    font-weight: 500;
}

.export-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 12px;
    font-weight: 600;
    white-space: nowrap;
}

/* Table Styling */
.table-container {
    overflow-x: auto;
}

.data-table {
    margin: 0;
    font-size: 0.95rem;
}

.data-table thead th {
    background: #2c3e50;
    color: white;
    border: none;
    padding: 20px 15px;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.th-content {
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
}

.sortable {
    cursor: pointer;
    transition: all 0.2s ease;
}

.sortable:hover {
    color: #ffd700;
}

.sort-icon {
    font-size: 12px;
    opacity: 0.7;
}

.table-row {
    transition: all 0.3s ease;
    border-bottom: 1px solid #f8f9fa;
}

.table-row:hover {
    background: #f8f9fa;
    transform: scale(1.01);
}

.data-table td {
    padding: 20px 15px;
    vertical-align: middle;
    border: none;
}

/* Rank Badge */
.rank-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    width: 50px;
    height: 50px;
    border-radius: 15px;
    font-weight: 700;
    font-size: 1.1rem;
}

.rank-1 {
    background: linear-gradient(45deg, #ffd700, #ffed4e);
    color: #b8860b;
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
}

.rank-2 {
    background: linear-gradient(45deg, #c0c0c0, #e5e5e5);
    color: #666;
    box-shadow: 0 4px 15px rgba(192, 192, 192, 0.3);
}

.rank-3 {
    background: linear-gradient(45deg, #cd7f32, #daa520);
    color: #8b4513;
    box-shadow: 0 4px 15px rgba(205, 127, 50, 0.3);
}

.rank-default {
    background: #f8f9fa;
    color: #6c757d;
    border: 2px solid #e9ecef;
}

/* User Info */
.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    background: linear-gradient(45deg, #007bff, #0056b3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
}

.user-name {
    font-weight: 600;
    color: #2c3e50;
    font-size: 1rem;
}

.user-nik {
    font-size: 0.8rem;
    color: #6c757d;
    font-weight: 500;
}

/* Contact Info */
.contact-info {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
}

.contact-item i {
    color: #28a745;
    font-size: 14px;
}

/* Address Info */
.address-info {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    font-size: 0.9rem;
    color: #6c757d;
}

.address-info i {
    color: #dc3545;
    font-size: 14px;
    margin-top: 2px;
}

/* Score Display */
.score-display {
    text-align: center;
}

.score-value {
    font-size: 1.2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 5px;
}

.score-bar {
    width: 60px;
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
    margin: 0 auto;
}

.score-fill {
    height: 100%;
    background: linear-gradient(45deg, #28a745, #20c997);
    border-radius: 3px;
    transition: width 0.8s ease;
}

/* Status Badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-success {
    background: linear-gradient(45deg, #28a745, #20c997);
    color: white;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
}

.status-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
    color: white;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
}

.status-warning {
    background: linear-gradient(45deg, #ffc107, #e0a800);
    color: #212529;
    box-shadow: 0 4px 15px rgba(255, 193, 7, 0.2);
}

.status-danger {
    background: linear-gradient(45deg, #dc3545, #c82333);
    color: white;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2);
}

/* Quota Display */
.quota-display {
    text-align: center;
    font-weight: 600;
}

.quota-amount {
    font-size: 1.1rem;
    color: #2c3e50;
}

.quota-unit {
    font-size: 0.8rem;
    color: #6c757d;
    margin-left: 2px;
}

.quota-none {
    color: #adb5bd;
    font-size: 1.2rem;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 5px;
    justify-content: center;
}

.action-btn {
    width: 35px;
    height: 35px;
    border: none;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.action-btn.edit {
    background: #ffc107;
    color: #212529;
}

.action-btn.edit:hover {
    background: #e0a800;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
}

.action-btn.view {
    background: #17a2b8;
    color: white;
}

.action-btn.view:hover {
    background: #138496;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
}

.action-btn.delete {
    background: #dc3545;
    color: white;
}

.action-btn.delete:hover {
    background: #c82333;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

/* No Data */
.no-data {
    text-align: center;
    padding: 50px 20px;
    color: #6c757d;
}

.no-data i {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.5;
}

.no-data h5 {
    margin-bottom: 10px;
    color: #495057;
}

/* Pagination */
.pagination-wrapper {
    padding: 20px 25px;
    border-top: 1px solid #e9ecef;
    display: flex;
    justify-content: between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.pagination-info {
    color: #6c757d;
    font-size: 0.9rem;
    font-weight: 500;
}

.pagination {
    margin: 0;
}

.page-link {
    border: none;
    padding: 10px 15px;
    margin: 0 2px;
    border-radius: 10px;
    color: #6c757d;
    font-weight: 500;
    transition: all 0.3s ease;
}

.page-link:hover {
    background: #f8f9fa;
    color: #007bff;
    transform: translateY(-2px);
}

.page-item.active .page-link {
    background: linear-gradient(45deg, #007bff, #0056b3);
    color: white;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
}

.page-item.disabled .page-link {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Modal Styling */
.modal-content {
    border: none;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}

.modal-header {
    background: linear-gradient(45deg, #007bff, #0056b3);
    color: white;
    border-radius: 20px 20px 0 0;
    padding: 20px 25px;
}

.modal-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
}

.btn-close {
    filter: brightness(0) invert(1);
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-title {
        font-size: 2rem;
    }
    
    .stats-card {
        margin-bottom: 20px;
    }
    
    .table-controls {
        padding: 20px 15px;
    }
    
    .table-controls .row {
        gap: 15px;
    }
    
    .filter-controls {
        flex-direction: column;
        gap: 10px;
    }
    
    .data-table {
        font-size: 0.85rem;
    }
    
    .user-info {
        flex-direction: column;
        text-align: center;
        gap: 8px;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 5px;
    }
    
    .pagination-wrapper {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<script>
// Initialize AOS
AOS.init({
    duration: 600,
    once: true
});

// Search functionality
let searchTimeout;
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value;
    const clearBtn = document.getElementById('clearSearch');
    
    clearBtn.style.display = searchTerm ? 'block' : 'none';
    
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        performSearch();
    }, 500);
});

document.getElementById('clearSearch').addEventListener('click', function() {
    document.getElementById('searchInput').value = '';
    this.style.display = 'none';
    performSearch();
});

// Filter functionality
document.getElementById('filterSelect').addEventListener('change', function() {
    performSearch();
});

function performSearch() {
    const search = document.getElementById('searchInput').value;
    const filter = document.getElementById('filterSelect').value;
    
    const url = new URL(window.location);
    url.searchParams.set('search', search);
    url.searchParams.set('filter', filter);
    url.searchParams.set('page', '1'); // Reset to page 1
    
    window.location.href = url.toString();
}

// Action functions
function editPenerima(id) {
    window.location.href = `edit_penerima.php?id=${id}`;
}

function viewDetails(id) {
    // Load detail via AJAX
    fetch(`get_penerima_detail.php?id=${id}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalContent').innerHTML = html;
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal memuat detail penerima');
        });
}

function deletePenerima(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data penerima ini?')) {
        if (confirm('Data yang dihapus tidak dapat dikembalikan. Lanjutkan?')) {
            window.location.href = `hapus_penerima.php?id=${id}`;
        }
    }
}

function exportData() {
    const search = document.getElementById('searchInput').value;
    const filter = document.getElementById('filterSelect').value;
    
    window.open(`export_penerima.php?search=${encodeURIComponent(search)}&filter=${encodeURIComponent(filter)}`, '_blank');
}

// Table sorting
document.querySelectorAll('.sortable').forEach(header => {
    header.addEventListener('click', function() {
        const sortField = this.dataset.sort;
        const currentUrl = new URL(window.location);
        const currentSort = currentUrl.searchParams.get('sort');
        const currentOrder = currentUrl.searchParams.get('order') || 'asc';
        
        let newOrder = 'asc';
        if (currentSort === sortField && currentOrder === 'asc') {
            newOrder = 'desc';
        }
        
        currentUrl.searchParams.set('sort', sortField);
        currentUrl.searchParams.set('order', newOrder);
        
        window.location.href = currentUrl.toString();
    });
});

// Auto-refresh notification badges
setInterval(updateNotifications, 30000); // Update every 30 seconds

function updateNotifications() {
    // This would typically fetch from your backend
    // Example implementation:
    // fetch('get_notifications.php')
    //     .then(response => response.json())
    //     .then(data => {
    //         updateBadgeCount(data.newTransactions);
    //     });
}

// Smooth scroll to top after page change
if (window.location.hash === '#top') {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}
</script>

<?php include 'partials/footer.php'; ?>
