<?php
include 'partials/header.php';
include 'config/db.php';

// Join ke tabel penerima agar bisa ambil nama
$query = "SELECT rp.*, p.nama 
          FROM riwayat_perubahan rp
          LEFT JOIN penerima p ON rp.penerima_id = p.id
          ORDER BY rp.tanggal DESC";
$result = $conn->query($query);

// Hitung statistik
$stats_query = "SELECT 
                  COUNT(*) as total,
                  SUM(CASE WHEN aksi = 'tambah' THEN 1 ELSE 0 END) as tambah,
                  SUM(CASE WHEN aksi = 'edit' THEN 1 ELSE 0 END) as edit,
                  SUM(CASE WHEN aksi = 'hapus' THEN 1 ELSE 0 END) as hapus
                FROM riwayat_perubahan";
$stats = $conn->query($stats_query)->fetch_assoc();
?>

<style>
/* Custom CSS untuk halaman riwayat */
:root {
  --primary-color: #667eea;
  --secondary-color: #764ba2;
  --success-color: #51cf66;
  --danger-color: #ff6b6b;
  --warning-color: #ffd43b;
  --info-color: #22d3ee;
  --dark-color: #1f2937;
  --light-color: #f8fafc;
  --border-color: #e2e8f0;
  --shadow-sm: 0 1px 3px rgba(0,0,0,0.1);
  --shadow-md: 0 4px 15px rgba(0,0,0,0.1);
  --shadow-lg: 0 10px 30px rgba(0,0,0,0.15);
  --gradient-primary: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  --gradient-success: linear-gradient(135deg, #51cf66, #40c057);
  --gradient-danger: linear-gradient(135deg, #ff6b6b, #fa5252);
  --gradient-warning: linear-gradient(135deg, #ffd43b, #fcc419);
}

body {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  min-height: 100vh;
}

.page-header {
  background: var(--gradient-primary);
  color: white;
  padding: 2rem 0;
  margin-bottom: 2rem;
  border-radius: 0 0 20px 20px;
  position: relative;
  overflow: hidden;
}

.page-header::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="80" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="60" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
  opacity: 0.3;
}

.page-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin: 0;
  text-shadow: 0 2px 4px rgba(0,0,0,0.2);
  animation: slideInDown 0.6s ease-out;
}

.page-subtitle {
  font-size: 1.1rem;
  opacity: 0.9;
  margin: 0;
  animation: slideInDown 0.6s ease-out 0.2s both;
}

/* Stats Cards */
.stats-container {
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  border-radius: 15px;
  padding: 1.5rem;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-lg);
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 4px;
  background: var(--gradient-primary);
}

.stat-card.success::before { background: var(--gradient-success); }
.stat-card.danger::before { background: var(--gradient-danger); }
.stat-card.warning::before { background: var(--gradient-warning); }

.stat-icon {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
  background: var(--gradient-primary);
  color: white;
  font-size: 1.5rem;
}

.stat-card.success .stat-icon { background: var(--gradient-success); }
.stat-card.danger .stat-icon { background: var(--gradient-danger); }
.stat-card.warning .stat-icon { background: var(--gradient-warning); }

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  color: var(--dark-color);
  margin: 0;
  line-height: 1;
}

.stat-label {
  color: #6b7280;
  font-size: 0.9rem;
  margin: 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Controls Section */
.controls-section {
  background: white;
  border-radius: 15px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-color);
}

.search-box {
  position: relative;
}

.search-input {
  width: 100%;
  padding: 12px 45px 12px 20px;
  border: 2px solid var(--border-color);
  border-radius: 10px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: var(--light-color);
}

.search-input:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  background: white;
}

.search-icon {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: #6b7280;
}

.filter-buttons {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.filter-btn {
  padding: 8px 16px;
  border: 2px solid var(--border-color);
  background: white;
  border-radius: 20px;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
  text-transform: capitalize;
}

.filter-btn:hover, .filter-btn.active {
  background: var(--primary-color);
  border-color: var(--primary-color);
  color: white;
  transform: translateY(-2px);
}

/* Table Styles */
.table-container {
  background: white;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-color);
}

.table {
  margin: 0;
}

.table thead th {
  background: var(--gradient-primary);
  color: white;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-size: 0.85rem;
  padding: 1rem 1.5rem;
  border: none;
  position: sticky;
  top: 0;
  z-index: 10;
}

.table tbody tr {
  transition: all 0.3s ease;
  animation: fadeInUp 0.5s ease-out;
}

.table tbody tr:hover {
  background: rgba(102, 126, 234, 0.05);
  transform: scale(1.01);
  box-shadow: var(--shadow-sm);
}

.table tbody td {
  padding: 1rem 1.5rem;
  vertical-align: middle;
  border-color: var(--border-color);
}

/* Action Badges */
.action-badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}

.action-tambah {
  background: rgba(81, 207, 102, 0.1);
  color: #51cf66;
  border: 1px solid rgba(81, 207, 102, 0.2);
}

.action-edit {
  background: rgba(255, 212, 59, 0.1);
  color: #ffd43b;
  border: 1px solid rgba(255, 212, 59, 0.2);
}

.action-hapus {
  background: rgba(255, 107, 107, 0.1);
  color: #ff6b6b;
  border: 1px solid rgba(255, 107, 107, 0.2);
}

/* Detail Cell */
.detail-cell {
  max-width: 300px;
  word-wrap: break-word;
  line-height: 1.5;
}

.detail-toggle {
  background: none;
  border: none;
  color: var(--primary-color);
  cursor: pointer;
  padding: 0;
  text-decoration: underline;
  font-size: 0.9rem;
}

.detail-full {
  display: none;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 3rem 2rem;
  color: #6b7280;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
  opacity: 0.3;
}

/* Animations */
@keyframes slideInDown {
  from { opacity: 0; transform: translateY(-30px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

.pulse { animation: pulse 2s infinite; }

/* Responsive */
@media (max-width: 768px) {
  .page-title { font-size: 2rem; }
  .filter-buttons { justify-content: center; }
  .table-responsive { border-radius: 0; }
  .controls-section { margin: 0 -15px 1.5rem; border-radius: 0; }
  .stats-container .col-md-3 { margin-bottom: 1rem; }
}

/* Loading State */
.loading {
  opacity: 0.6;
  pointer-events: none;
}

.skeleton {
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
}

@keyframes loading {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
</style>

<div class="page-header">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h1 class="page-title">📋 Riwayat Perubahan</h1>
        <p class="page-subtitle">Pantau semua aktivitas perubahan data penerima</p>
      </div>
      <div class="col-md-4 text-end">
        <div class="d-flex align-items-center justify-content-end gap-2">
          <div class="badge bg-light text-dark px-3 py-2">
            <i class="fas fa-clock me-1"></i>
            Real-time Updates
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container">
  <!-- Statistics Cards -->
  <div class="stats-container">
    <div class="row">
      <div class="col-md-3 mb-3">
        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-list-alt"></i>
          </div>
          <h3 class="stat-number" id="totalStat"><?= number_format($stats['total']) ?></h3>
          <p class="stat-label">Total Aktivitas</p>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="stat-card success">
          <div class="stat-icon">
            <i class="fas fa-plus"></i>
          </div>
          <h3 class="stat-number"><?= number_format($stats['tambah']) ?></h3>
          <p class="stat-label">Data Ditambah</p>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="stat-card warning">
          <div class="stat-icon">
            <i class="fas fa-edit"></i>
          </div>
          <h3 class="stat-number"><?= number_format($stats['edit']) ?></h3>
          <p class="stat-label">Data Diedit</p>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="stat-card danger">
          <div class="stat-icon">
            <i class="fas fa-trash"></i>
          </div>
          <h3 class="stat-number"><?= number_format($stats['hapus']) ?></h3>
          <p class="stat-label">Data Dihapus</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Controls Section -->
  <div class="controls-section">
    <div class="row align-items-center">
      <div class="col-md-6 mb-3 mb-md-0">
        <div class="search-box">
          <input type="text" class="search-input" id="searchInput" placeholder="Cari berdasarkan nama, aksi, atau detail...">
          <i class="fas fa-search search-icon"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Table Container -->
  <div class="table-container">
    <div class="table-responsive">
      <table class="table table-hover align-middle" id="historyTable">
        <thead>
          <tr>
            <th width="80">
              <i class="fas fa-hashtag me-1"></i> ID
            </th>
            <th>
              <i class="fas fa-user me-1"></i> Penerima
            </th>
            <th width="120">
              <i class="fas fa-cog me-1"></i> Aksi
            </th>
            <th>
              <i class="fas fa-info-circle me-1"></i> Detail Perubahan
            </th>
            <th width="180">
              <i class="fas fa-calendar me-1"></i> Waktu
            </th>
          </tr>
        </thead>
        <tbody id="tableBody">
          <?php 
          $no = 1;
          while ($row = $result->fetch_assoc()): 
          ?>
            <tr class="table-row" data-action="<?= strtolower($row['aksi']) ?>">
              <td class="text-center fw-bold"><?= $row['id'] ?></td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px; font-size: 0.8rem;">
                    <?= strtoupper(substr($row['nama'] ?? 'D', 0, 1)) ?>
                  </div>
                  <div>
                    <div class="fw-medium"><?= htmlspecialchars($row['nama'] ?? '(Data Dihapus)') ?></div>
                    <small class="text-muted">ID: <?= $row['penerima_id'] ?></small>
                  </div>
                </div>
              </td>
              <td class="text-center">
                <span class="action-badge action-<?= strtolower($row['aksi']) ?>">
                  <?php
                  $icons = [
                    'tambah' => 'fas fa-plus',
                    'edit' => 'fas fa-edit', 
                    'hapus' => 'fas fa-trash'
                  ];
                  ?>
                  <i class="<?= $icons[strtolower($row['aksi'])] ?>"></i>
                  <?= ucfirst($row['aksi']) ?>
                </span>
              </td>
              <td class="detail-cell">
                <?php 
                $detail = htmlspecialchars($row['detail']);
                $shortDetail = strlen($detail) > 100 ? substr($detail, 0, 100) . '...' : $detail;
                ?>
                <div class="detail-short"><?= nl2br($shortDetail) ?></div>
                <?php if (strlen($detail) > 100): ?>
                  <div class="detail-full"><?= nl2br($detail) ?></div>
                  <button class="detail-toggle btn-link p-0 mt-1" onclick="toggleDetail(this)">
                    Lihat selengkapnya
                  </button>
                <?php endif; ?>
              </td>
              <td class="text-center">
                <div class="fw-medium"><?= date('d M Y', strtotime($row['tanggal'])) ?></div>
                <small class="text-muted"><?= date('H:i:s', strtotime($row['tanggal'])) ?></small>
              </td>
            </tr>
          <?php 
          $no++;
          endwhile; 
          ?>
        </tbody>
      </table>

      <!-- Empty State -->
      <div class="empty-state d-none" id="emptyState">
        <div class="empty-icon">📋</div>
        <h4>Tidak Ada Data</h4>
        <p>Tidak ditemukan riwayat yang sesuai dengan pencarian Anda.</p>
      </div>
    </div>
  </div>

  <!-- Load More Button -->
  <div class="text-center mt-4">
    <button class="btn btn-outline-primary btn-lg px-4" id="loadMoreBtn" style="display: none;">
      <i class="fas fa-chevron-down me-2"></i>
      Muat Lebih Banyak
    </button>
  </div>
</div>

<!-- Loading Overlay -->
<div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" id="loadingOverlay" style="background: rgba(255,255,255,0.8); z-index: 9999; display: none !important;">
  <div class="text-center">
    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
      <span class="visually-hidden">Loading...</span>
    </div>
    <div>Memuat data...</div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  initializeTable();
  initializeSearch();
  initializeFilters();
  initializeAnimations();
});

// Table management
function initializeTable() {
  const rows = document.querySelectorAll('.table-row');
  
  // Add entrance animation delay
  rows.forEach((row, index) => {
    row.style.animationDelay = `${index * 0.1}s`;
  });

  // Update row numbers after filtering
  updateRowNumbers();
}

// Search functionality
function initializeSearch() {
  const searchInput = document.getElementById('searchInput');
  let searchTimeout;

  searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      performSearch(this.value.toLowerCase());
    }, 300);
  });
}

function performSearch(searchTerm) {
  const rows = document.querySelectorAll('.table-row');
  const emptyState = document.getElementById('emptyState');
  let visibleCount = 0;

  rows.forEach(row => {
    const nama = row.cells[1].textContent.toLowerCase();
    const aksi = row.cells[2].textContent.toLowerCase();
    const detail = row.cells[3].textContent.toLowerCase();
    
    const isVisible = nama.includes(searchTerm) || 
                     aksi.includes(searchTerm) || 
                     detail.includes(searchTerm);
    
    if (isVisible) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });

  // Show empty state if no results
  if (visibleCount === 0) {
    emptyState.classList.remove('d-none');
  } else {
    emptyState.classList.add('d-none');
  }

  updateStatistics();
}

// Filter functionality
function initializeFilters() {
  const filterButtons = document.querySelectorAll('.filter-btn');
  
  filterButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      // Update active button
      filterButtons.forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      
      // Apply filter
      const filter = this.dataset.filter;
      applyFilter(filter);
    });
  });
}

function applyFilter(filter) {
  const rows = document.querySelectorAll('.table-row');
  const emptyState = document.getElementById('emptyState');
  let visibleCount = 0;

  rows.forEach(row => {
    const action = row.dataset.action;
    
    if (filter === 'all' || action === filter) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });

  // Show empty state if no results
  if (visibleCount === 0) {
    emptyState.classList.remove('d-none');
  } else {
    emptyState.classList.add('d-none');
  }

  updateStatistics();
}

// Detail toggle
function toggleDetail(button) {
  const cell = button.closest('.detail-cell');
  const shortDetail = cell.querySelector('.detail-short');
  const fullDetail = cell.querySelector('.detail-full');
  
  if (fullDetail.style.display === 'none' || fullDetail.style.display === '') {
    shortDetail.style.display = 'none';
    fullDetail.style.display = 'block';
    button.textContent = 'Lihat lebih sedikit';
  } else {
    shortDetail.style.display = 'block';
    fullDetail.style.display = 'none';
    button.textContent = 'Lihat selengkapnya';
  }
}

// Update row numbers
function updateRowNumbers() {
  const visibleRows = document.querySelectorAll('.table-row:not([style*="display: none"])');
  visibleRows.forEach((row, index) => {
    // Optional: update row numbers if needed
  });
}

// Update statistics based on visible rows
function updateStatistics() {
  const visibleRows = document.querySelectorAll('.table-row:not([style*="display: none"])');
  const totalStat = document.getElementById('totalStat');
  
  totalStat.textContent = visibleRows.length.toLocaleString();
  totalStat.parentElement.classList.add('pulse');
  
  setTimeout(() => {
    totalStat.parentElement.classList.remove('pulse');
  }, 1000);
}

// Initialize animations
function initializeAnimations() {
  // Observe elements for scroll animations
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.stat-card').forEach(card => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';
    card.style.transition = 'all 0.6s ease';
    observer.observe(card);
  });
}

// Auto refresh functionality (optional)
function autoRefresh() {
  setInterval(() => {
    // You can implement auto-refresh logic here
    console.log('Auto-refresh check...');
  }, 30000); // Check every 30 seconds
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
  // Ctrl/Cmd + F to focus search
  if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
    e.preventDefault();
    document.getElementById('searchInput').focus();
  }
  
  // Escape to clear search
  if (e.key === 'Escape') {
    const searchInput = document.getElementById('searchInput');
    searchInput.value = '';
    performSearch('');
  }
});

// Initialize auto refresh
// autoRefresh();
</script>

<?php include 'partials/footer.php'; ?>
