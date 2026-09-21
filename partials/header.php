<?php
// header.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ATM Mini Beras Bansos - Dashboard</title>

  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Animate.css (opsional untuk animasi) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

  <style>
    :root {
      --primary-color: #667eea;
      --secondary-color: #764ba2;
      --accent-color: #ffd700;
      --text-dark: #2c3e50;
      --text-light: #6c757d;
      --shadow-light: 0 2px 15px rgba(0,0,0,0.08);
      --shadow-medium: 0 5px 25px rgba(0,0,0,0.15);
      --border-radius: 15px;
      --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    * { font-family: 'Inter', sans-serif; }

    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: 100vh;
    }

    /* Modern Navbar */
    .navbar-modern {
      background: rgba(255, 255, 255, 0.95) !important;
      backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      padding: 15px 0;
      transition: var(--transition);
    }

    .navbar-modern.scrolled {
      padding: 8px 0;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }

    .navbar-brand-modern {
      display: flex;
      align-items: center;
      gap: 12px;
      font-weight: 800;
      font-size: 1.4rem;
      color: var(--text-dark) !important;
      text-decoration: none;
      transition: var(--transition);
    }

    .navbar-brand-modern:hover {
      color: var(--primary-color) !important;
      transform: translateY(-1px);
    }

    .brand-icon {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 20px;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .brand-text { display: flex; flex-direction: column; line-height: 1.1; }
    .brand-title { font-size: 1.2rem; font-weight: 800; color: var(--text-dark); }
    .brand-subtitle {
      font-size: 0.75rem; color: var(--text-light); font-weight: 500;
      text-transform: uppercase; letter-spacing: 0.5px;
    }

    /* Modern Navigation */
    .nav-modern { gap: 5px; }

    .nav-link-modern {
      display: flex; align-items: center; gap: 8px;
      padding: 12px 18px !important; border-radius: 12px;
      color: var(--text-dark) !important; font-weight: 600; font-size: 0.95rem;
      text-decoration: none; transition: var(--transition); position: relative; overflow: hidden;
    }

    .nav-link-modern::before {
      content: ''; position: absolute; inset: 0;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      opacity: 0; transition: var(--transition); z-index: -1;
    }

    .nav-link-modern:hover { color: white !important; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3); }
    .nav-link-modern:hover::before { opacity: 1; }
    .nav-link-modern.active {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white !important; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .nav-link-modern i { font-size: 16px; transition: var(--transition); }
    .nav-link-modern:hover i { transform: scale(1.1); }

    /* Mobile Menu Button */
    .navbar-toggler-modern {
      border: none; padding: 8px 12px; border-radius: 8px;
      background: rgba(102, 126, 234, 0.1); transition: var(--transition);
    }
    .navbar-toggler-modern:hover { background: rgba(102, 126, 234, 0.2); transform: scale(1.05); }
    .navbar-toggler-modern:focus { box-shadow: none; }

    /* Hamburger Animation */
    .hamburger { display: flex; flex-direction: column; gap: 3px; }
    .hamburger span { width: 20px; height: 2px; background: var(--primary-color); border-radius: 1px; transition: var(--transition); }

    /* Content Spacing */
    .main-content { margin-top: 90px; padding: 0; }

    /* Status Indicator */
    .status-indicator { position: relative; }
    .status-dot {
      width: 8px; height: 8px; background: #28a745; border-radius: 50%;
      position: absolute; top: -2px; right: -2px; border: 2px solid white; animation: pulse 2s infinite;
    }
    @keyframes pulse {
      0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
      70% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
      100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
    }

    /* Notification Badge */
    .notification-badge {
      position: absolute; top: -8px; right: -8px; background: #dc3545; color: white;
      font-size: 10px; padding: 2px 6px; border-radius: 10px; font-weight: 700; min-width: 18px; text-align: center;
      animation: bounce 1s infinite;
    }
    @keyframes bounce {
      0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
      40% { transform: translateY(-5px); }
      60% { transform: translateY(-3px); }
    }

    /* Mobile Responsive */
    @media (max-width: 991.98px) {
      .navbar-collapse {
        background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(20px);
        border-radius: var(--border-radius); padding: 20px; margin-top: 15px; box-shadow: var(--shadow-medium);
      }
      .nav-modern { flex-direction: column; gap: 10px; }
      .nav-link-modern { justify-content: center; text-align: center; }
      .brand-text { display: none; }
      .main-content { margin-top: 80px; }
    }

    @media (max-width: 576px) {
      .brand-title { font-size: 1rem; }
      .brand-icon { width: 35px; height: 35px; font-size: 18px; }
    }

    /* Loading Animation */
    .loading-overlay {
      position: fixed; inset: 0; background: rgba(255, 255, 255, 0.9);
      display: flex; align-items: center; justify-content: center; z-index: 9999;
      opacity: 0; visibility: hidden; transition: var(--transition);
    }
    .loading-overlay.show { opacity: 1; visibility: visible; }

    .spinner {
      width: 50px; height: 50px;
      border: 3px solid rgba(102, 126, 234, 0.1);
      border-top: 3px solid var(--primary-color);
      border-radius: 50%; animation: spin 1s linear infinite;
    }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
  </style>
</head>
<body>
  <!-- Loading Overlay -->
  <div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
  </div>

  <!-- Modern Navbar -->
  <nav class="navbar navbar-expand-lg fixed-top navbar-modern" id="mainNavbar">
    <div class="container">
      <!-- Brand -->
      <a class="navbar-brand-modern" href="index.php">
        <div class="brand-icon">
          <i class="bi bi-house-heart-fill"></i>
        </div>
        <div class="brand-text">
          <span class="brand-title">ATM Mini Beras</span>
          <span class="brand-subtitle">Bantuan Sosial</span>
        </div>
      </a>

      <!-- Mobile Menu Button -->
      <button
        class="navbar-toggler navbar-toggler-modern"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarNav"
        aria-controls="navbarNav"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <div class="hamburger">
          <span></span><span></span><span></span>
        </div>
      </button>

      <!-- Navigation Menu -->
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto nav-modern">
          <li class="nav-item">
            <a class="nav-link nav-link-modern" href="index.php" data-page="home">
              <i class="bi bi-house-door-fill"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link nav-link-modern" href="registrasi.php" data-page="registrasi">
              <i class="bi bi-person-plus-fill"></i>
              <span>Registrasi</span>
              <div class="status-indicator"><div class="status-dot"></div></div>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link nav-link-modern" href="penerima.php" data-page="penerima">
              <i class="bi bi-people-fill"></i>
              <span>Data Penerima</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link nav-link-modern" href="riwayat_transaksi.php" data-page="transaksi">
              <i class="bi bi-clock-history"></i>
              <span>Riwayat</span>
              <div class="notification-badge" id="transactionBadge">5</div>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link nav-link-modern" href="riwayat_perubahan.php" data-page="perubahan">
              <i class="bi bi-pencil-square"></i>
              <span>Audit Log</span>
            </a>
          </li>

          <!-- User Profile Dropdown -->
          <li class="nav-item dropdown ms-2">
            <a class="nav-link nav-link-modern dropdown-toggle"
               href="#"
               id="userDropdown"
               role="button"
               data-bs-toggle="dropdown"
               aria-expanded="false">
              <i class="bi bi-person-circle"></i>
              <span class="d-none d-lg-inline">Admin</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end animate__animated animate__fadeIn" aria-labelledby="userDropdown" style="border-radius: var(--border-radius); box-shadow: var(--shadow-medium); border: none;">
              <li><a class="dropdown-item" href="#"><i class="bi bi-gear"></i> Pengaturan</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-question-circle"></i> Bantuan</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Keluar</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main Content Container -->
  <div class="main-content">
    <!-- Content will be inserted here -->
  </div>

  <!-- Bootstrap Bundle JS (sudah termasuk Popper) -->


  <script>
  // Modern Navigation Script
  document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.getElementById('mainNavbar');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const navLinks = document.querySelectorAll('.nav-link-modern[data-page]');

    // Hide loading overlay after page loads
    setTimeout(() => { loadingOverlay.classList.remove('show'); }, 500);

    // Navbar scroll effect
    window.addEventListener('scroll', function() {
      if (window.scrollY > 50) { navbar.classList.add('scrolled'); }
      else { navbar.classList.remove('scrolled'); }
    });

    // Active page detection
    const currentPage = window.location.pathname.split('/').pop();
    navLinks.forEach(link => {
      const href = link.getAttribute('href');
      if (href === currentPage || (currentPage === '' && href === 'index.php')) {
        link.classList.add('active');
      }
    });

    // Smooth page transitions (skip dropdown toggle)
    document.querySelectorAll('.nav-link-modern:not(.dropdown-toggle)').forEach(link => {
      link.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href && href !== '#' && !href.startsWith('http')) {
          e.preventDefault();
          loadingOverlay.classList.add('show');
          setTimeout(() => { window.location.href = href; }, 300);
        }
      });
    });

    // Mobile menu auto-close
    const navbarCollapse = document.querySelector('.navbar-collapse');
    document.querySelectorAll('.navbar-collapse .nav-link').forEach(link => {
      link.addEventListener('click', () => {
        if (window.innerWidth < 992) {
          const instance = bootstrap.Collapse.getOrCreateInstance(navbarCollapse);
          // Tutup hanya jika bukan dropdown toggle
          if (!link.classList.contains('dropdown-toggle')) {
            instance.hide();
          }
        }
      });
    });

    // Update notification badges (contoh)
    updateNotificationBadges();
  });

  // Function to update notification badges
  function updateNotificationBadges() {
    const transactionBadge = document.getElementById('transactionBadge');
    // transactionBadge.style.display = 'none';
    // transactionBadge.textContent = '12';
  }

  // Add entrance animation
  window.addEventListener('load', function() {
    document.body.classList.add('animate__animated', 'animate__fadeIn');
  });
</script>

</body>
</html>
