<div id="formStep" class="slide-in">
    <div class="form-container">
        <div class="form-header">
            <h4 class="form-title">📝 Pendaftaran Dan Penilaian Kriteria</h4>
            <p class="form-subtitle">Lengkapi data dan kriteria penilaian untuk bantuan sosial</p>
        </div>

        <form id="mainForm">
            <!-- Data Personal Section -->
            <div class="form-section">
                <div class="section-header">
                    <h5 class="section-title">👤 Data Personal</h5>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap*</label>
                            <input type="text" class="form-control modern-input" id="nama" placeholder="Masukkan nama lengkap" required>
                            <div class="form-icon">👤</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">NIK*</label>
                            <input type="text" class="form-control modern-input" id="nik" maxlength="16" placeholder="16 digit NIK" required>
                            <div class="form-icon">🆔</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">No HP*</label>
                            <input type="tel" class="form-control modern-input" id="hp" placeholder="08xxxxxxxxxx" required>
                            <div class="form-icon">📱</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Alamat*</label>
                            <textarea class="form-control modern-input" id="alamat" rows="3" placeholder="Alamat lengkap tempat tinggal" required></textarea>
                            <div class="form-icon">📍</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kriteria Penilaian Section -->
            <div class="form-section">
                <div class="section-header">
                    <h5 class="section-title">📊 Kriteria Penilaian</h5>
                    <p class="section-subtitle">Pilih kriteria yang sesuai dengan kondisi Anda</p>
                </div>
                <div class="row g-3">
                    <?php foreach ($kriteria as $key => $val): ?>
                        <div class="col-md-6">
                            <div class="criteria-card">
                                <div class="criteria-header">
                                    <span class="criteria-code"><?= strtoupper($key) ?></span>
                                    <span class="criteria-weight">Bobot: <?= $val['bobot'] ?></span>
                                </div>
                                <label class="criteria-label"><?= $val['nama'] ?></label>
                                <select class="form-select modern-select kriteria-input" id="<?= $key ?>" required>
                                    <option value="">-- Pilih <?= $val['nama'] ?> --</option>
                                    <?php foreach ($opsi[$key] as $num => $label): ?>
                                        <option value="<?= $num ?>"><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="criteria-info">
                                    <span class="badge badge-<?= $val['jenis'] ?>"><?= ucfirst($val['jenis']) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Preview Section -->
            <div id="previewHasil" class="preview-section" style="display:none;">
                <div class="preview-header">
                    <h5 class="preview-title">🎯 Preview Perhitungan SAW</h5>
                    <p class="preview-subtitle">Hasil kalkulasi berdasarkan kriteria yang dipilih</p>
                </div>
                
                <div class="table-responsive">
                    <table class="table preview-table">
                        <thead>
                            <tr>
                                <th>Kriteria</th>
                                <th>Nilai</th>
                                <th>Normalisasi</th>
                                <th>Bobot</th>
                                <th>Skor</th>
                            </tr>
                        </thead>
                        <tbody id="previewBody"></tbody>
                    </table>
                </div>

                <div class="score-summary">
                    <div class="score-progress">
                        <label class="progress-label">Skor Akhir SAW</label>
                        <div class="progress">
                            <div class="progress-bar" id="scoreBar" style="width:0%" role="progressbar">
                                <span class="progress-text">0.000</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="result-cards">
                        <div class="result-card status-card">
                            <div class="result-icon">📋</div>
                            <div class="result-content">
                                <h6>Status Kelayakan</h6>
                                <span id="scoreStatus" class="result-value"></span>
                            </div>
                        </div>
                        <div class="result-card quota-card">
                            <div class="result-icon">🌾</div>
                            <div class="result-content">
                                <h6>Kuota Bantuan</h6>
                                <span id="kuotaBantuan" class="result-value"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="form-actions">
                <a href="index.php" class="btn btn-secondary btn-modern">
                    <i class="icon">←</i> Kembali
                </a>
                <button type="button" id="toRFID" class="btn btn-primary btn-modern" disabled>
                    Lanjutkan <i class="icon">→</i>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Container and Layout */
.form-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
}

.slide-in {
    animation: slideInUp 0.6s ease-out;
}

@keyframes slideInUp {
    from {
        transform: translateY(30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Form Header */
.form-header {
    text-align: center;
    margin-bottom: 2rem;
    padding: 2rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    color: white;
}

.form-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.form-subtitle {
    font-size: 1rem;
    opacity: 0.9;
    margin: 0;
}

/* Form Sections */
.form-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e9ecef;
}

.section-header {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f8f9fa;
}

.section-title {
    color: #495057;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.section-subtitle {
    color: #6c757d;
    font-size: 0.9rem;
    margin: 0;
}

/* Form Groups */
.form-group {
    position: relative;
    margin-bottom: 1rem;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
    display: block;
}

.modern-input, .modern-select {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 12px 16px;
    padding-left: 45px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background-color: #f8f9fa;
}

.modern-input:focus, .modern-select:focus {
    border-color: #667eea;
    background-color: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    outline: none;
}

.form-icon {
    position: absolute;
    left: 15px;
    top: 38px;
    font-size: 1.2rem;
    color: #6c757d;
    z-index: 2;
}

/* Criteria Cards */
.criteria-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1.25rem;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
    height: 100%;
}

.criteria-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.criteria-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.criteria-code {
    background: #667eea;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
}

.criteria-weight {
    font-size: 0.8rem;
    color: #6c757d;
    font-weight: 500;
}

.criteria-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.75rem;
    display: block;
}

.criteria-info {
    margin-top: 0.5rem;
}

.badge-benefit {
    background: #28a745;
    color: white;
}

.badge-cost {
    background: #dc3545;
    color: white;
}

.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

/* Preview Section */
.preview-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 2px solid #dee2e6;
}

.preview-header {
    text-align: center;
    margin-bottom: 1.5rem;
}

.preview-title {
    color: #495057;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.preview-subtitle {
    color: #6c757d;
    margin: 0;
}

.preview-table {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-bottom: 1.5rem;
}

.preview-table thead {
    background: #667eea;
    color: white;
}

.preview-table th, .preview-table td {
    padding: 12px;
    text-align: center;
    vertical-align: middle;
}

/* Score Progress */
.score-summary {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
}

.score-progress {
    margin-bottom: 1.5rem;
}

.progress-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
    display: block;
}

.progress {
    height: 30px;
    background: #e9ecef;
    border-radius: 15px;
    overflow: hidden;
    position: relative;
}

.progress-bar {
    background: linear-gradient(45deg, #28a745, #20c997);
    border-radius: 15px;
    transition: width 1s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.progress-text {
    color: white;
    font-weight: 600;
    font-size: 0.9rem;
}

/* Result Cards */
.result-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.result-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    border: 1px solid #e9ecef;
}

.result-icon {
    font-size: 2rem;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: white;
}

.result-content h6 {
    margin: 0 0 0.25rem 0;
    font-weight: 600;
    color: #495057;
}

.result-value {
    font-weight: 700;
    font-size: 1.1rem;
    color: #28a745;
}

/* Action Buttons */
.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.btn-modern {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    border: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-modern:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-primary.btn-modern {
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: white;
}

.btn-secondary.btn-modern {
    background: #6c757d;
    color: white;
}

.btn-modern:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
}

.icon {
    font-size: 1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-container {
        padding: 10px;
    }
    
    .form-header {
        padding: 1.5rem;
    }
    
    .form-title {
        font-size: 1.5rem;
    }
    
    .form-section {
        padding: 1rem;
    }
    
    .result-cards {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
        gap: 1rem;
    }
    
    .btn-modern {
        width: 100%;
        justify-content: center;
    }
}

/* Animation for form validation */
.modern-input.is-invalid, .modern-select.is-invalid {
    border-color: #dc3545;
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 20%, 40%, 60%, 80% {
        transform: translateX(-2px);
    }
    10%, 30%, 50%, 70%, 90% {
        transform: translateX(2px);
    }
}
</style>
