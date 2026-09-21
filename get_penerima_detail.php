<?php
include 'config/db.php';

$id = $_GET['id'] ?? 0;
$sql = "SELECT * FROM penerima WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row):
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Penerima Bantuan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(45deg, #4CAF50, #45a049);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2em;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
            font-size: 1.1em;
        }

        .detail-content {
            padding: 40px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .detail-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            border-left: 4px solid #4CAF50;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .detail-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .detail-label {
            font-weight: 600;
            color: #555;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .detail-value {
            font-size: 1.1em;
            color: #333;
            font-weight: 500;
        }

        .highlight-card {
            background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
            color: white;
            border-left: none;
        }

        .highlight-card .detail-label {
            color: rgba(255,255,255,0.9);
        }

        .highlight-card .detail-value {
            color: white;
            font-weight: 600;
            font-size: 1.3em;
        }

        .pin-card {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border-left: none;
        }

        .pin-card .detail-label {
            color: rgba(255,255,255,0.9);
        }

        .pin-card .detail-value {
            color: white;
            font-weight: 600;
            font-family: monospace;
            font-size: 1.4em;
            letter-spacing: 2px;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            background: #4CAF50;
            color: white;
            font-weight: 600;
            font-size: 0.9em;
        }

        .back-button {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .back-button:hover {
            background: #5a67d8;
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
            }
            
            .header {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 1.5em;
            }
            
            .detail-content {
                padding: 20px;
            }
            
            .detail-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Detail Penerima Bantuan</h1>
            <p>Informasi lengkap data penerima</p>
        </div>
        
        <div class="detail-content">
            <div class="detail-grid">
                <div class="detail-card">
                    <div class="detail-label">👤 Nama Lengkap</div>
                    <div class="detail-value"><?= htmlspecialchars($row['nama']) ?></div>
                </div>
                
                <div class="detail-card">
                    <div class="detail-label">🆔 NIK</div>
                    <div class="detail-value"><?= htmlspecialchars($row['nik']) ?></div>
                </div>
                
                <div class="detail-card">
                    <div class="detail-label">📍 Alamat</div>
                    <div class="detail-value"><?= htmlspecialchars($row['alamat']) ?></div>
                </div>
                
                <div class="detail-card">
                    <div class="detail-label">📱 No HP</div>
                    <div class="detail-value"><?= htmlspecialchars($row['hp']) ?></div>
                </div>
                
                <div class="detail-card">
                    <div class="detail-label">📊 Status</div>
                    <div class="detail-value">
                        <span class="status-badge"><?= htmlspecialchars($row['golongan']) ?></span>
                    </div>
                </div>
                
                <div class="detail-card">
                    <div class="detail-label">🌾 Kuota Bantuan</div>
                    <div class="detail-value"> <?= number_format($row['koutaBantuan'], 0, ',', '.') ?> G</div>
                </div>
                
                <div class="detail-card highlight-card">
                    <div class="detail-label">⭐ SAW Score</div>
                    <div class="detail-value"><?= number_format($row['saw_score'], 3) ?></div>
                </div>
                
                <div class="detail-card pin-card">
                    <div class="detail-label">🔐 PIN Akses</div>
                    <div class="detail-value"><?= htmlspecialchars($row['pin']) ?></div>
                </div>
            </div>
            
            <!-- Tombol kembali diarahkan ke penerima.php -->
            <a href="penerima.php" class="back-button">← Kembali</a>
        </div>
    </div>
</body>
</html>

<?php else: ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Tidak Ditemukan</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        
        .error-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 500px;
        }
        
        .error-icon {
            font-size: 4em;
            margin-bottom: 20px;
        }
        
        .error-title {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 10px;
        }
        
        .error-message {
            color: #666;
            margin-bottom: 30px;
        }
        
        .back-button {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .back-button:hover {
            background: #5a67d8;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">❌</div>
        <h2 class="error-title">Data Tidak Ditemukan</h2>
        <p class="error-message">Maaf, data penerima bantuan yang Anda cari tidak dapat ditemukan dalam sistem.</p>
        <!-- Tombol kembali diarahkan ke penerima.php -->
        <a href="penerima.php" class="back-button">← Kembali</a>
    </div>
</body>
</html>
<?php endif; ?>

<?php
$stmt->close();
$conn->close();
?>
