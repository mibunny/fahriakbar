
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'partials/header.php';
?>

<div class="hero-section">
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6">
                <div class="hero-content">
                    <div class="hero-badge">
                        <i class="bi bi-shield-check"></i>
                        Sistem Terpercaya
                    </div>
                    <h1 class="hero-title">
                        Dashboard <span class="text-gradient">ATM Mini</span><br>
                        Beras Bansos
                    </h1>
                    <p class="hero-description">
                        Platform digital yang memudahkan pengelolaan distribusi beras bantuan sosial 
                        dengan sistem yang transparan dan efisien.
                    </p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image">
                    <div class="floating-card card-1">
                        <i class="bi bi-graph-up-arrow"></i>
                        <span>Efisiensi Tinggi</span>
                    </div>
                    <div class="floating-card card-2">
                        <i class="bi bi-shield-lock"></i>
                        <span>Keamanan Terjamin</span>
                    </div>
                    <div class="hero-circle"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="features-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="section-title">Fitur Utama</h2>
                <p class="section-subtitle">Kelola data bantuan sosial dengan mudah dan efisien</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="feature-card h-100" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon primary">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Registrasi Penerima</h4>
                        <p>Daftarkan calon penerima bantuan dengan sistem yang terintegrasi</p>
                        <div class="feature-stats">
                            <small class="text-muted">
                                <i class="bi bi-clock"></i> Proses cepat 2 menit
                            </small>
                        </div>
                    </div>
                    <a href="registrasi.php" class="feature-btn btn-primary">
                        <span>Mulai Registrasi</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="feature-card h-100" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon info">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Data Penerima</h4>
                        <p>Kelola dan pantau data penerima bantuan secara real-time</p>
                        <div class="feature-stats">
                            <small class="text-muted">
                                <i class="bi bi-eye"></i> Monitoring aktif
                            </small>
                        </div>
                    </div>
                    <a href="penerima.php" class="feature-btn btn-info">
                        <span>Lihat Data</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="feature-card h-100" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-icon success">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Riwayat Transaksi</h4>
                        <p>Lacak semua aktivitas transaksi dengan sistem logging yang akurat</p>
                        <div class="feature-stats">
                            <small class="text-muted">
                                <i class="bi bi-database"></i> Data tersimpan aman
                            </small>
                        </div>
                    </div>
                    <a href="riwayat_transaksi.php" class="feature-btn btn-success">
                        <span>Lihat Riwayat</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="feature-card h-100" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-icon warning">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Audit Perubahan</h4>
                        <p>Monitor setiap perubahan data untuk transparansi dan akuntabilitas</p>
                        <div class="feature-stats">
                            <small class="text-muted">
                                <i class="bi bi-shield-check"></i> Audit trail lengkap
                            </small>
                        </div>
                    </div>
                    <a href="riwayat_perubahan.php" class="feature-btn btn-warning">
                        <span>Audit Log</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Section -->

<style>
/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="50" cy="10" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="10" cy="60" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="90" cy="40" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grain)"/></svg>');
    opacity: 0.1;
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
    margin-bottom: 20px;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 20px;
}

.text-gradient {
    background: linear-gradient(45deg, #ffd700, #ff6b6b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-description {
    font-size: 1.2rem;
    opacity: 0.9;
    margin-bottom: 30px;
    line-height: 1.6;
}

.hero-stats {
    display: flex;
    align-items: center;
    gap: 30px;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #ffd700;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
}

.stat-divider {
    width: 1px;
    height: 40px;
    background: rgba(255, 255, 255, 0.3);
}

.hero-image {
    position: relative;
    height: 500px;
}

.hero-circle {
    width: 300px;
    height: 300px;
    background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
    border-radius: 50%;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.floating-card {
    position: absolute;
    background: rgba(255, 255, 255, 0.95);
    color: #333;
    padding: 15px 20px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    font-size: 14px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
    animation: float 3s ease-in-out infinite;
}

.card-1 {
    top: 20%;
    right: 10%;
    animation-delay: 0s;
}

.card-2 {
    bottom: 20%;
    left: 10%;
    animation-delay: 1.5s;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

/* Features Section */
.features-section {
    background: #f8f9fa;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 15px;
}

.section-subtitle {
    font-size: 1.1rem;
    color: #6c757d;
    max-width: 600px;
    margin: 0 auto;
}

.feature-card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border: 1px solid #f0f0f0;
}

.feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--accent-color);
    transform: translateX(-100%);
    transition: transform 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.feature-card:hover::before {
    transform: translateX(0);
}

.feature-icon {
    width: 70px;
    height: 70px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    font-size: 30px;
    color: white;
}

.feature-icon.primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
    --accent-color: #007bff;
}

.feature-icon.info {
    background: linear-gradient(45deg, #17a2b8, #138496);
    --accent-color: #17a2b8;
}

.feature-icon.success {
    background: linear-gradient(45deg, #28a745, #1e7e34);
    --accent-color: #28a745;
}

.feature-icon.warning {
    background: linear-gradient(45deg, #ffc107, #e0a800);
    --accent-color: #ffc107;
}

.feature-content h4 {
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 15px;
    color: #2c3e50;
}

.feature-content p {
    color: #6c757d;
    line-height: 1.6;
    margin-bottom: 15px;
}

.feature-stats {
    margin-bottom: 20px;
}

.feature-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 12px 20px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
}

.feature-btn:hover {
    transform: translateX(5px);
    text-decoration: none;
}

.feature-btn i {
    transition: transform 0.3s ease;
}

.feature-btn:hover i {
    transform: translateX(5px);
}

.btn-primary { background: linear-gradient(45deg, #007bff, #0056b3); color: white; }
.btn-info { background: linear-gradient(45deg, #17a2b8, #138496); color: white; }
.btn-success { background: linear-gradient(45deg, #28a745, #1e7e34); color: white; }
.btn-warning { background: linear-gradient(45deg, #ffc107, #e0a800); color: #212529; }

/* Quick Actions */
.quick-action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 20px;
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 15px;
    color: #6c757d;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 14px;
    font-weight: 600;
    width: 120px;
}

.quick-action-btn:hover {
    border-color: #007bff;
    color: #007bff;
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 123, 255, 0.1);
}

.quick-action-btn i {
    font-size: 24px;
}

/* Responsive */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-stats {
        flex-direction: column;
        gap: 15px;
    }
    
    .stat-divider {
        width: 40px;
        height: 1px;
    }
    
    .floating-card {
        position: static;
        margin: 10px 0;
    }
    
    .hero-image {
        height: auto;
        margin-top: 40px;
    }
    
    .section-title {
        font-size: 2rem;
    }
}

/* AOS Animations */
[data-aos] {
    pointer-events: none;
}
[data-aos].aos-animate {
    pointer-events: auto;
}
</style>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<script>
// Initialize AOS
AOS.init({
    duration: 800,
    once: true
});

// Animated counters
function animateCounter(element, start, end, duration) {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const value = Math.floor(progress * (end - start) + start);
        element.innerHTML = value.toLocaleString('id-ID');
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
}

// Start counters when page loads


// Add smooth scrolling
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});
</script>

<?php
include 'partials/footer.php';
?>
