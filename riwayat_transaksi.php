<?php
include 'partials/header.php';
include 'config/db.php';

// Pagination settings
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;

// Date filters
$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build WHERE clause
$whereClause = "WHERE 1=1";
if (!empty($dateFrom)) {
    $whereClause .= " AND DATE(t.tanggal) >= '$dateFrom'";
}
if (!empty($dateTo)) {
    $whereClause .= " AND DATE(t.tanggal) <= '$dateTo'";
}
if (!empty($search)) {
    $whereClause .= " AND (p.nama LIKE '%$search%' OR t.id LIKE '%$search%')";
}

// Get total for pagination
$countQuery = "SELECT COUNT(*) as total FROM transaksi t 
               LEFT JOIN penerima p ON t.penerima_id = p.id $whereClause";
$countResult = $conn->query($countQuery);
$totalData = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

// Main query
$query = "SELECT t.id, p.nama AS penerima, p.nik, p.golongan, t.jumlah_beras, t.tanggal
          FROM transaksi t 
          LEFT JOIN penerima p ON t.penerima_id = p.id 
          $whereClause
          ORDER BY t.tanggal DESC 
          LIMIT $limit OFFSET $offset";
$result = $conn->query($query);

// Statistics for dashboard
$statsQuery = "SELECT 
    COUNT(*) as total_transaksi,
    SUM(jumlah_beras) as total_beras,
    COUNT(CASE WHEN DATE(tanggal) = CURDATE() THEN 1 END) as hari_ini,
    COUNT(CASE WHEN HOUR(tanggal) = HOUR(NOW()) AND DATE(tanggal) = CURDATE() THEN 1 END) as jam_ini
    FROM transaksi";
$statsResult = $conn->query($statsQuery);
$stats = $statsResult->fetch_assoc();

// Chart period setting (hanya hourly atau daily)
$period = isset($_GET['chart_period']) ? $_GET['chart_period'] : 'daily';

if ($period === 'hourly') {
    // Data per jam untuk 24 jam terakhir
    $chartQuery = "SELECT 
        HOUR(tanggal) AS hour_num,
        CONCAT(LPAD(HOUR(tanggal), 2, '0'), ':00') AS label,
        COUNT(*) AS count,
        SUM(jumlah_beras) AS total
        FROM transaksi 
        WHERE tanggal >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        GROUP BY HOUR(tanggal)
        ORDER BY HOUR(tanggal) ASC";
} else {
    // Data per hari untuk 7 hari terakhir
    $chartQuery = "SELECT 
        DATE(tanggal) AS chart_date,
        COUNT(*) AS count,
        SUM(jumlah_beras) AS total
        FROM transaksi 
        WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(tanggal)
        ORDER BY DATE(tanggal) ASC";
}

$chartResult = $conn->query($chartQuery);
$chartData = [];

while ($row = $chartResult->fetch_assoc()) {

    if ($period === 'daily') {
        $row['label'] = date('d M', strtotime($row['chart_date']));
    }

    $chartData[] = $row;
}

$chartResult = $conn->query($chartQuery);
$chartData = [];
while ($row = $chartResult->fetch_assoc()) {
    $chartData[] = $row;
}
?>

<!-- Hero Section -->
<div class="transaction-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="hero-content">
                    <div class="hero-badge">
                        <i class="bi bi-clock-history"></i>
                        Transaction Management
                    </div>
                    <h1 class="hero-title">
                        Riwayat <span class="text-gradient">Transaksi</span>
                    </h1>
                    <p class="hero-description">
                        Pantau dan analisis seluruh aktivitas distribusi beras bantuan sosial secara real-time
                    </p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="hero-stats-mini">
                    <div class="stat-mini">
                        <div class="stat-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?= number_format($stats['total_transaksi']) ?></div>
                            <div class="stat-label">Total Transaksi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Dashboard -->
        <div class="row g-4 mt-3">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card primary">
                    <div class="stats-icon">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-number" data-target="<?= $stats['total_transaksi'] ?>">0</div>
                        <div class="stats-label">Total Transaksi</div>
                    </div>
                    <div class="stats-trend">
                        <i class="bi bi-arrow-up"></i>
                        <span>+8.2%</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card success">
                    <div class="stats-icon">
                        <i class="bi bi-bag-check"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-number" data-target="<?= $stats['total_beras'] ?>">0</div>
                        <div class="stats-label">Total Beras (gram)</div>
                    </div>
                    <div class="stats-trend">
                        <i class="bi bi-arrow-up"></i>
                        <span>+15.3%</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card info">
                    <div class="stats-icon">
                        <i class="bi bi-calendar-day"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-number" data-target="<?= $stats['hari_ini'] ?>">0</div>
                        <div class="stats-label">Transaksi Hari Ini</div>
                    </div>
                    <div class="stats-trend">
                        <i class="bi bi-arrow-up"></i>
                        <span>+24.1%</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card warning">
                    <div class="stats-icon">
                        <i class="bi bi-clock"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-number" data-target="<?= $stats['jam_ini'] ?>">0</div>
                        <div class="stats-label">Jam Ini</div>
                    </div>
                    <div class="stats-trend">
                        <i class="bi bi-arrow-up"></i>
                        <span>+12.7%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container py-5">
    <!-- Chart Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="chart-card">
                <div class="chart-header">
                    <h4>
                        <i class="bi bi-bar-chart"></i>
                        <?php if ($period === 'hourly'): ?>
                            Tren Transaksi Per Jam (24 Jam Terakhir)
                        <?php else: ?>
                            Tren Transaksi Harian (7 Hari Terakhir)
                        <?php endif; ?>
                    </h4>
                    <div class="chart-controls">
                        <button class="btn btn-sm btn-outline-primary <?= $period === 'hourly' ? 'active' : '' ?>" 
                                onclick="changeChartPeriod('hourly')">
                            <i class="bi bi-clock"></i> Per Jam
                        </button>
                        <button class="btn btn-sm btn-outline-primary <?= $period === 'daily' ? 'active' : '' ?>" 
                                onclick="changeChartPeriod('daily')">
                            <i class="bi bi-calendar-day"></i> Per Hari
                        </button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="transactionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="filter-section">
        <div class="row align-items-end">
            <div class="col-lg-4 col-md-6 mb-3">
                <label class="form-label">Cari Transaksi</label>
                <div class="search-input-group">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" id="searchInput" 
                           placeholder="Nama penerima atau ID transaksi..." 
                           value="<?= htmlspecialchars($search) ?>">
                    <button class="clear-btn" id="clearSearch" style="display: none;">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control filter-input" id="dateFrom" 
                       value="<?= $dateFrom ?>">
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" class="form-control filter-input" id="dateTo" 
                       value="<?= $dateTo ?>">
            </div>
            <div class="col-lg-2 col-md-6 mb-3">
                <button class="btn btn-primary w-100 filter-btn" onclick="applyFilters()">
                    <i class="bi bi-funnel"></i>
                    Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Transaction Table -->
    <div class="transaction-table-card">
        <div class="table-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5>
                    <i class="bi bi-list-ul"></i>
                    Daftar Transaksi
                    <span class="record-count">(<?= number_format($totalData) ?> record)</span>
                </h5>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table transaction-table">
                <thead>
                    <tr>
                        <th width="8%">
                            <div class="th-content">ID</div>
                        </th>
                        <th width="32%">
                            <div class="th-content">Penerima</div>
                        </th>
                        <th width="15%">
                            <div class="th-content">Golongan</div>
                        </th>
                        <th width="25%">
                            <div class="th-content">Jumlah Beras</div>
                        </th>
                        <th width="20%">
                            <div class="th-content">Tanggal & Waktu</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php 
                        $rank = $offset + 1;
                        while ($row = $result->fetch_assoc()): 
                        ?>
                            <tr class="transaction-row" data-aos="fade-up" data-aos-delay="<?= ($rank - $offset) * 50 ?>">
                                <td>
                                    <div class="transaction-id">
                                        <span class="id-badge">#<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="recipient-info">
                                        <div class="recipient-avatar">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <div class="recipient-details">
                                            <div class="recipient-name"><?= htmlspecialchars($row['penerima']) ?></div>
                                            <div class="recipient-nik">NIK: <?= htmlspecialchars($row['nik']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $golongan = $row['golongan'];
                                    $badgeClass = '';
                                    switch ($golongan) {
                                        case 'A': $badgeClass = 'success'; break;
                                        case 'B': $badgeClass = 'primary'; break;
                                        case 'C': $badgeClass = 'warning'; break;
                                        default: $badgeClass = 'secondary';
                                    }
                                    ?>
                                    <span class="golongan-badge golongan-<?= $badgeClass ?>">
                                        <?= $golongan ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="amount-display">
                                        <div class="amount-value">
                                            <?= number_format($row['jumlah_beras'], 0, ',', '.') ?>
                                        </div>
                                        <div class="amount-unit">gram</div>
                                        <div class="amount-bar">
                                            <div class="amount-fill" style="width: <?= min(100, ($row['jumlah_beras'] / 5000) * 100) ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="datetime-display">
                                        <div class="date-part">
                                            <i class="bi bi-calendar3"></i>
                                            <?= date('d M Y', strtotime($row['tanggal'])) ?>
                                        </div>
                                        <div class="time-part">
                                            <i class="bi bi-clock"></i>
                                            <?= date('H:i', strtotime($row['tanggal'])) ?> WIB
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php 
                        $rank++;
                        endwhile; 
                        ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="no-data">
                                    <i class="bi bi-inbox"></i>
                                    <h5>Tidak ada data transaksi</h5>
                                    <p>Belum ada transaksi yang sesuai dengan filter yang dipilih</p>
                                    <button class="btn btn-outline-primary" onclick="resetFilters()">
                                        <i class="bi bi-arrow-clockwise"></i>
                                        Reset Filter
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="table-pagination">
            <div class="pagination-info">
                Menampilkan <?= $offset + 1 ?> - <?= min($offset + $limit, $totalData) ?> dari <?= $totalData ?> transaksi
            </div>
            <nav aria-label="Table pagination">
                <ul class="pagination">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= buildPaginationUrl($page - 1, $search, $dateFrom, $dateTo) ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= buildPaginationUrl($i, $search, $dateFrom, $dateTo) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                    
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= buildPaginationUrl($page + 1, $search, $dateFrom, $dateTo) ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
function buildPaginationUrl($page, $search, $dateFrom, $dateTo) {
    $params = ['page' => $page];
    if (!empty($search)) $params['search'] = $search;
    if (!empty($dateFrom)) $params['date_from'] = $dateFrom;
    if (!empty($dateTo)) $params['date_to'] = $dateTo;
    if (isset($_GET['chart_period'])) $params['chart_period'] = $_GET['chart_period'];
    return '?' . http_build_query($params);
}
?>

<style>
/* Hero Section */
.transaction-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    margin-top: -90px;
    padding: 120px 0 50px;
    position: relative;
    overflow: hidden;
}

.transaction-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="%23ffffff" opacity="0.05"/><circle cx="75" cy="75" r="1" fill="%23ffffff" opacity="0.05"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grain)"/></svg>');
}

.hero-content {
    position: relative;
    z-index: 2;
}

.hero-badge {
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

.hero-title {
    font-size: 3rem;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 15px;
}

.text-gradient {
    background: linear-gradient(45deg, #ffd700, #ff6b6b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.hero-description {
    font-size: 1.1rem;
    opacity: 0.9;
    max-width: 500px;
}

.hero-stats-mini {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 25px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.stat-mini {
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-mini .stat-icon {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.stat-mini .stat-number {
    font-size: 2rem;
    font-weight: 800;
    color: #ffd700;
}

.stat-mini .stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
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
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
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
    font-size: 2rem;
    font-weight: 800;
    color: #2c3e50;
    line-height: 1;
}

.stats-label {
    color: #6c757d;
    font-weight: 500;
    font-size: 0.9rem;
}

.stats-trend {
    position: absolute;
    top: 20px;
    right: 20px;
    display: flex;
    align-items: center;
    gap: 5px;
    color: #28a745;
    font-size: 0.8rem;
    font-weight: 600;
}

/* Chart Card */
.chart-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.chart-header {
    padding: 25px;
    border-bottom: 1px solid #f8f9fa;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.chart-header h4 {
    margin: 0;
    color: #2c3e50;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.chart-controls {
    display: flex;
    gap: 10px;
}

.chart-controls button {
    border-radius: 25px;
    padding: 10px 20px;
    font-weight: 500;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.chart-controls button.active {
    background: linear-gradient(45deg, #007bff, #0056b3);
    border-color: #007bff;
    color: white;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
    transform: translateY(-2px);
}

.chart-controls button:hover:not(.active) {
    background: #f8f9fa;
    border-color: #007bff;
    color: #007bff;
    transform: translateY(-2px);
}

.chart-container {
    padding: 25px;
    height: 350px;
}

/* Filter Section */
.filter-section {
    background: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.search-input-group {
    position: relative;
}

.search-input-group i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    z-index: 2;
}

.search-input-group .form-control {
    padding-left: 45px;
    padding-right: 40px;
    border-radius: 10px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.search-input-group .form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.1);
}

.clear-btn {
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

.clear-btn:hover {
    background: #f8f9fa;
    color: #dc3545;
}

.filter-input {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.filter-input:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.1);
}

.filter-btn {
    border-radius: 10px;
    font-weight: 600;
    padding: 12px;
}

/* Transaction Table */
.transaction-table-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.table-header {
    padding: 25px;
    border-bottom: 1px solid #f8f9fa;
}

.table-header h5 {
    margin: 0;
    color: #2c3e50;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.record-count {
    color: #6c757d;
    font-weight: 400;
    font-size: 0.9rem;
}

.transaction-table {
    margin: 0;
    font-size: 0.95rem;
}

.transaction-table thead th {
    background: #2c3e50;
    color: white;
    border: none;
    padding: 20px 15px;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
    text-align: center;
}

.th-content {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}

.transaction-row {
    transition: all 0.3s ease;
    border-bottom: 1px solid #f8f9fa;
}

.transaction-row:hover {
    background: #f8f9fa;
    transform: translateX(3px);
}

.transaction-table td {
    padding: 20px 15px;
    vertical-align: middle;
    border: none;
}

/* Table Cell Styles */
.transaction-id {
    text-align: center;
}

.id-badge {
    background: #f8f9fa;
    color: #495057;
    padding: 6px 12px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.85rem;
}

.recipient-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.recipient-avatar {
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

.recipient-name {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 2px;
}

.recipient-nik {
    font-size: 0.8rem;
    color: #6c757d;
}

.golongan-badge {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    justify-content: center;
}

.golongan-success {
    background: linear-gradient(45deg, #28a745, #20c997);
    color: white;
}

.golongan-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
    color: white;
}

.golongan-warning {
    background: linear-gradient(45deg, #ffc107, #e0a800);
    color: #212529;
}

.golongan-secondary {
    background: #6c757d;
    color: white;
}

.amount-display {
    text-align: center;
}

.amount-value {
    font-size: 1.2rem;
    font-weight: 700;
    color: #2c3e50;
}

.amount-unit {
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 8px;
}

.amount-bar {
    width: 80px;
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
    margin: 0 auto;
}

.amount-fill {
    height: 100%;
    background: linear-gradient(45deg, #28a745, #20c997);
    border-radius: 3px;
    transition: width 0.8s ease;
}

.datetime-display {
    text-align: center;
}

.date-part, .time-part {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    font-size: 0.9rem;
}

.date-part {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 3px;
}

.time-part {
    color: #6c757d;
    font-size: 0.8rem;
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
    opacity: 0.3;
}

.no-data h5 {
    margin-bottom: 10px;
    color: #495057;
}

/* Pagination */
.table-pagination {
    padding: 20px 25px;
    border-top: 1px solid #f8f9fa;
    display: flex;
    justify-content: space-between;
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

/* Responsive Design */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .stats-card {
        margin-bottom: 20px;
    }
    
    .chart-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .chart-container {
        height: 250px;
    }
    
    .transaction-table td {
        padding: 12px 8px;
    }
    
    .recipient-info {
        flex-direction: column;
        text-align: center;
        gap: 8px;
    }
    
    .chart-controls {
        width: 100%;
        justify-content: center;
    }
    
    .chart-controls button {
        flex: 1;
        justify-content: center;
    }
}
</style>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Initialize AOS
AOS.init({
    duration: 600,
    once: true
});

// Animated Counter
function animateCounters() {
    document.querySelectorAll('.stats-number[data-target]').forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 2000;
        const increment = target / (duration / 16);
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            counter.textContent = Math.floor(current).toLocaleString('id-ID');
        }, 16);
    });
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(animateCounters, 500);
    initializeChart();
});

// Chart initialization
function initializeChart() {
    const ctx = document.getElementById('transactionChart').getContext('2d');
    
    const chartData = <?= json_encode($chartData) ?>;
    const period = '<?= $period ?>';
    
    // Membuat data yang lengkap untuk tampilan per jam (0-23)
    let processedData = chartData;
    
    if (period === 'hourly') {
        // Buat array untuk 24 jam dengan data kosong
        const fullHourData = [];
        for (let i = 0; i < 24; i++) {
            const hour = String(i).padStart(2, '0') + ':00';
            const existingData = chartData.find(item => item.label === hour);
            fullHourData.push({
                label: hour,
                count: existingData ? existingData.count : 0,
                total: existingData ? existingData.total : 0
            });
        }
        processedData = fullHourData;
    }
    
    const chart = new Chart(ctx, {
        type: period === 'hourly' ? 'bar' : 'line',
        data: {
            labels: processedData.map(item => item.label),
            datasets: [{
                label: 'Jumlah Transaksi',
                data: processedData.map(item => item.count),
                borderColor: '#007bff',
                backgroundColor: period === 'hourly' ? 'rgba(0, 123, 255, 0.7)' : 'rgba(0, 123, 255, 0.1)',
                borderWidth: period === 'hourly' ? 2 : 3,
                fill: period === 'daily',
                tension: period === 'daily' ? 0.4 : 0,
                pointBackgroundColor: '#007bff',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: period === 'daily' ? 5 : 0,
                barThickness: period === 'hourly' ? 20 : undefined
            }, {
                label: 'Total Beras (kg)',
                data: processedData.map(item => Math.round(item.total / 1000)),
                borderColor: '#28a745',
                backgroundColor: period === 'hourly' ? 'rgba(40, 167, 69, 0.7)' : 'rgba(40, 167, 69, 0.1)',
                borderWidth: period === 'hourly' ? 2 : 3,
                fill: period === 'daily',
                tension: period === 'daily' ? 0.4 : 0,
                yAxisID: 'y1',
                pointBackgroundColor: '#28a745',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: period === 'daily' ? 5 : 0,
                barThickness: period === 'hourly' ? 20 : undefined
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        font: {
                            weight: 600,
                            size: 13
                        },
                        padding: 20
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    cornerRadius: 10,
                    callbacks: {
                        title: function(context) {
                            return period === 'hourly' ? 'Jam: ' + context[0].label : 'Tanggal: ' + context[0].label;
                        },
                        label: function(context) {
                            if (context.datasetIndex === 0) {
                                return 'Transaksi: ' + context.parsed.y + ' transaksi';
                            } else {
                                return 'Total Beras: ' + context.parsed.y + ' kg';
                            }
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: period === 'hourly' ? 'Jam (24 jam terakhir)' : 'Hari (7 hari terakhir)',
                        font: {
                            weight: 600
                        }
                    },
                    ticks: {
                        maxRotation: period === 'hourly' ? 90 : 45,
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Transaksi',
                        font: {
                            weight: 600
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Total Beras (kg)',
                        font: {
                            weight: 600
                        }
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                    beginAtZero: true,
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        }
    });
}

// Function to change chart period
function changeChartPeriod(period) {
    const url = new URL(window.location);
    url.searchParams.set('chart_period', period);
    window.location.href = url.toString();
}

// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value;
    const clearBtn = document.getElementById('clearSearch');
    clearBtn.style.display = searchTerm ? 'block' : 'none';
});

document.getElementById('clearSearch').addEventListener('click', function() {
    document.getElementById('searchInput').value = '';
    this.style.display = 'none';
});

// Filter functions
function applyFilters() {
    const search = document.getElementById('searchInput').value;
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    
    const url = new URL(window.location);
    url.searchParams.set('search', search);
    url.searchParams.set('date_from', dateFrom);
    url.searchParams.set('date_to', dateTo);
    url.searchParams.set('page', '1');
    
    window.location.href = url.toString();
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('dateFrom').value = '';
    document.getElementById('dateTo').value = '';
    
    const url = new URL(window.location.pathname);
    if (new URLSearchParams(window.location.search).get('chart_period')) {
        url.searchParams.set('chart_period', new URLSearchParams(window.location.search).get('chart_period'));
    }
    window.location.href = url.toString();
}

// Enter key for search
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyFilters();
    }
});
</script>

<?php include 'partials/footer.php'; ?>
