<?php
session_start();
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>ATM Bantuan Sosial</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --bg-primary: #0a0a0a;
      --bg-secondary: #111111;
      --bg-card: #1a1a1a;
      --border-color: #2a2a2a;
      --text-primary: #ffffff;
      --text-secondary: #888888;
      --text-muted: #555555;
      --accent-primary: #00d4ff;
      --accent-secondary: #ff6b6b;
      --accent-success: #51cf66;
      --accent-warning: #ffd43b;
      --shadow-primary: 0 20px 40px rgba(0, 212, 255, 0.15);
      --shadow-secondary: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: var(--bg-primary);
      color: var(--text-primary);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    /* Animated Background */
    .bg-animated {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: 
        radial-gradient(circle at 25% 25%, rgba(0, 212, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 75% 75%, rgba(255, 107, 107, 0.08) 0%, transparent 50%);
      animation: bgPulse 4s ease-in-out infinite alternate;
    }

    @keyframes bgPulse {
      0% { opacity: 0.3; }
      100% { opacity: 0.7; }
    }

    /* Geometric Shapes */
    .shape {
      position: absolute;
      border: 1px solid rgba(0, 212, 255, 0.2);
      animation: float 6s ease-in-out infinite;
    }

    .shape-1 {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      top: 10%;
      left: 10%;
      animation-delay: 0s;
    }

    .shape-2 {
      width: 60px;
      height: 60px;
      top: 20%;
      right: 15%;
      animation-delay: 2s;
      border-color: rgba(255, 107, 107, 0.2);
    }

    .shape-3 {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      bottom: 20%;
      left: 20%;
      animation-delay: 4s;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px) rotate(0deg); }
      50% { transform: translateY(-20px) rotate(180deg); }
    }

    /* Main Container */
    .login-container {
      position: relative;
      z-index: 10;
      width: 100%;
      max-width: 400px;
      padding: 20px;
    }

    .login-card {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 20px;
      padding: 0;
      box-shadow: var(--shadow-secondary);
      position: relative;
      overflow: hidden;
      backdrop-filter: blur(10px);
      transition: all 0.3s ease;
    }

    .login-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 2px;
      background: linear-gradient(90deg, transparent, var(--accent-primary), transparent);
      animation: borderSlide 3s linear infinite;
    }

    @keyframes borderSlide {
      0% { left: -100%; }
      100% { left: 100%; }
    }

    .login-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow-primary), var(--shadow-secondary);
    }

    /* Header */
    .login-header {
      padding: 40px 30px 20px;
      text-align: center;
      position: relative;
    }

    .logo-container {
      margin-bottom: 30px;
      position: relative;
    }

    .logo {
      width: 60px;
      height: 60px;
      background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
      border-radius: 12px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      font-weight: 700;
      color: white;
      position: relative;
      overflow: hidden;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .logo::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
      transform: rotate(45deg);
      transition: all 0.3s ease;
    }

    .logo:hover::before {
      animation: logoShine 0.6s ease-in-out;
    }

    .logo:hover {
      transform: scale(1.05) rotate(5deg);
    }

    @keyframes logoShine {
      0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
      100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }

    .welcome-title {
      font-size: 28px;
      font-weight: 600;
      margin-bottom: 8px;
      background: linear-gradient(135deg, var(--text-primary), var(--text-secondary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .welcome-subtitle {
      color: var(--text-secondary);
      font-size: 14px;
      font-weight: 400;
    }

    /* Form */
    .login-form {
      padding: 0 30px 40px;
    }

    .input-group {
      position: relative;
      margin-bottom: 25px;
    }

    .input-field {
      width: 100%;
      background: var(--bg-secondary);
      border: 1px solid var(--border-color);
      border-radius: 12px;
      padding: 18px 20px 18px 50px;
      color: var(--text-primary);
      font-size: 15px;
      outline: none;
      transition: all 0.3s ease;
    }

    .input-field::placeholder {
      color: var(--text-muted);
      transition: all 0.3s ease;
    }

    .input-field:focus {
      border-color: var(--accent-primary);
      box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
      background: rgba(0, 212, 255, 0.05);
    }

    .input-field:focus::placeholder {
      transform: translateX(10px);
      opacity: 0.7;
    }

    .input-icon {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
      transition: all 0.3s ease;
    }

    .input-field:focus + .input-icon {
      color: var(--accent-primary);
      transform: translateY(-50%) scale(1.1);
    }

    .password-toggle {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: var(--text-muted);
      cursor: pointer;
      padding: 5px;
      border-radius: 6px;
      transition: all 0.3s ease;
    }

    .password-toggle:hover {
      color: var(--accent-primary);
      background: rgba(0, 212, 255, 0.1);
    }

    /* Form Options */
    .form-options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      font-size: 13px;
    }

    .checkbox-wrapper {
      display: flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
    }

    .custom-checkbox {
      width: 18px;
      height: 18px;
      border: 2px solid var(--border-color);
      border-radius: 4px;
      position: relative;
      transition: all 0.3s ease;
    }

    .custom-checkbox input {
      opacity: 0;
      position: absolute;
    }

    .custom-checkbox input:checked + .checkmark {
      background: var(--accent-primary);
      border-color: var(--accent-primary);
    }

    .checkmark {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      border-radius: 4px;
      transition: all 0.3s ease;
    }

    .checkmark::after {
      content: '';
      position: absolute;
      left: 5px;
      top: 2px;
      width: 4px;
      height: 8px;
      border: solid white;
      border-width: 0 2px 2px 0;
      transform: rotate(45deg) scale(0);
      transition: transform 0.2s ease;
    }

    .custom-checkbox input:checked + .checkmark::after {
      transform: rotate(45deg) scale(1);
    }

    .forgot-link {
      color: var(--accent-primary);
      text-decoration: none;
      transition: all 0.3s ease;
      position: relative;
    }

    .forgot-link::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0;
      height: 1px;
      background: var(--accent-primary);
      transition: width 0.3s ease;
    }

    .forgot-link:hover::after {
      width: 100%;
    }

    /* Submit Button */
    .submit-btn {
      width: 100%;
      background: linear-gradient(135deg, var(--accent-primary), #0099cc);
      border: none;
      border-radius: 12px;
      padding: 18px;
      color: white;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      position: relative;
      overflow: hidden;
      transition: all 0.3s ease;
      box-shadow: 0 8px 25px rgba(0, 212, 255, 0.3);
    }

    .submit-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 35px rgba(0, 212, 255, 0.4);
    }

    .submit-btn:active {
      transform: translateY(0);
    }

    .submit-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s ease;
    }

    .submit-btn:hover::before {
      left: 100%;
    }

    /* Loading State */
    .submit-btn.loading {
      pointer-events: none;
      opacity: 0.8;
    }

    .loading-spinner {
      display: none;
      width: 20px;
      height: 20px;
      border: 2px solid rgba(255,255,255,0.3);
      border-top: 2px solid white;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-right: 10px;
    }

    .submit-btn.loading .loading-spinner {
      display: inline-block;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* Alert */
    .alert {
      margin: 0 30px 20px;
      padding: 15px 20px;
      border-radius: 10px;
      border-left: 4px solid var(--accent-secondary);
      background: rgba(255, 107, 107, 0.1);
      color: var(--accent-secondary);
      font-size: 14px;
      animation: alertSlide 0.3s ease-out;
    }

    @keyframes alertSlide {
      from { opacity: 0; transform: translateX(-20px); }
      to { opacity: 1; transform: translateX(0); }
    }

    /* Responsive */
    @media (max-width: 480px) {
      .login-container {
        padding: 15px;
      }
      
      .login-header, .login-form {
        padding-left: 25px;
        padding-right: 25px;
      }
      
      .welcome-title {
        font-size: 24px;
      }
    }

    /* Pulse animation for interactive elements */
    @keyframes pulse {
      0% { box-shadow: 0 0 0 0 rgba(0, 212, 255, 0.4); }
      70% { box-shadow: 0 0 0 10px rgba(0, 212, 255, 0); }
      100% { box-shadow: 0 0 0 0 rgba(0, 212, 255, 0); }
    }

    .pulse {
      animation: pulse 2s infinite;
    }
  </style>
</head>
<body>
  <!-- Animated Background -->
  <div class="bg-animated"></div>
  
  <!-- Floating Shapes -->
  <div class="shape shape-1"></div>
  <div class="shape shape-2"></div>
  <div class="shape shape-3"></div>

  <!-- Login Container -->
  <div class="login-container">
    <div class="login-card">
      <!-- Header -->
      <div class="login-header">
        <div class="logo-container">
          <div class="logo" id="logo">ATM</div>
        </div>
        <h1 class="welcome-title">Selamat Datang</h1>
        <p class="welcome-subtitle">Silakan masuk ke akun Anda</p>
      </div>

      <!-- Error Alert -->
      <?php if (!empty($error)): ?>
        <div class="alert">
          ⚠ <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <!-- Form -->
      <form class="login-form" method="post" id="loginForm">
        <div class="input-group">
          <input 
            type="text" 
            class="input-field" 
            name="username" 
            placeholder="Masukkan username"
            value="<?= isset($username) ? htmlspecialchars($username) : '' ?>"
            required
          >
          <div class="input-icon">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>
          </div>
        </div>

        <div class="input-group">
          <input 
            type="password" 
            class="input-field" 
            name="password" 
            id="password"
            placeholder="Masukkan password"
            required
          >
          <div class="input-icon">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
              <path d="M18,8h-1V6c0-2.76-2.24-5-5-5S7,3.24,7,6v2H6c-1.1,0-2,0.9-2,2v10c0,1.1,0.9,2,2,2h12c1.1,0,2-0.9,2-2V10C20,8.9,19.1,8,18,8z M12,17c-1.1,0-2-0.9-2-2s0.9-2,2-2s2,0.9,2,2S13.1,17,12,17z M15.1,8H8.9V6c0-1.71,1.39-3.1,3.1-3.1s3.1,1.39,3.1,3.1V8z"/>
            </svg>
          </div>
          <button type="button" class="password-toggle" id="togglePassword">
            <svg id="eyeIcon" width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/>
            </svg>
          </button>
        </div>

        <div class="form-options">
          <label class="checkbox-wrapper">
            <div class="custom-checkbox">
              <input type="checkbox">
              <div class="checkmark"></div>
            </div>
            <span>Ingat saya</span>
          </label>
          <a href="#" class="forgot-link">Lupa password?</a>
        </div>

        <button type="submit" class="submit-btn" id="submitBtn">
          <div class="loading-spinner"></div>
          <span class="btn-text">MASUK</span>
        </button>
      </form>
    </div>
  </div>

  <script>
    // DOM Elements
    const form = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    const toggleBtn = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');
    const logo = document.getElementById('logo');

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
      initFormHandling();
      initPasswordToggle();
      initInteractions();
      initAnimations();
    });

    // Form handling
    function initFormHandling() {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Add loading state
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        
        // Simulate processing
        setTimeout(() => {
          submitBtn.classList.remove('loading');
          submitBtn.disabled = false;
          form.submit();
        }, 1500);
      });

      // Input animations
      const inputs = document.querySelectorAll('.input-field');
      inputs.forEach(input => {
        input.addEventListener('focus', function() {
          this.parentElement.style.transform = 'scale(1.02)';
        });
        
        input.addEventListener('blur', function() {
          this.parentElement.style.transform = 'scale(1)';
        });
      });
    }

    // Password toggle
    function initPasswordToggle() {
      const eyeIcon = document.getElementById('eyeIcon');
      
      toggleBtn.addEventListener('click', function() {
        const isPassword = passwordField.type === 'password';
        passwordField.type = isPassword ? 'text' : 'password';
        
        // Change icon
        eyeIcon.innerHTML = isPassword ? 
          '<path d="M11.83,9L15,12.16C15,12.11 15,12.05 15,12A3,3 0 0,0 12,9C11.94,9 11.89,9 11.83,9M7.53,9.8L9.08,11.35C9.03,11.56 9,11.77 9,12A3,3 0 0,0 12,15C12.22,15 12.44,14.97 12.65,14.92L14.2,16.47C13.53,16.8 12.79,17 12,17A5,5 0 0,1 7,12C7,11.21 7.2,10.47 7.53,9.8M2,4.27L4.28,6.55L4.73,7C3.08,8.3 1.78,10 1,12C2.73,16.39 7,19.5 12,19.5C13.55,19.5 15.03,19.2 16.38,18.66L16.81,19.09L19.73,22L21,20.73L3.27,3M12,7A5,5 0 0,1 17,12C17,12.64 16.87,13.26 16.64,13.82L19.57,16.75C21.07,15.5 22.27,13.86 23,12C21.27,7.61 17,4.5 12,4.5C10.6,4.5 9.26,4.75 8,5.2L10.17,7.35C10.76,7.13 11.37,7 12,7Z"/>' :
          '<path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/>';
      });
    }

    // Interactive effects
    function initInteractions() {
      // Logo click effect
      logo.addEventListener('click', function() {
        this.classList.add('pulse');
        setTimeout(() => {
          this.classList.remove('pulse');
        }, 2000);
      });

      // Button hover effect
      submitBtn.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-3px) scale(1.02)';
      });
      
      submitBtn.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
      });
    }

    // Background animations
    function initAnimations() {
      // Parallax effect
      document.addEventListener('mousemove', function(e) {
        const shapes = document.querySelectorAll('.shape');
        const mouseX = e.clientX / window.innerWidth;
        const mouseY = e.clientY / window.innerHeight;
        
        shapes.forEach((shape, index) => {
          const speed = (index + 1) * 10;
          const x = (mouseX - 0.5) * speed;
          const y = (mouseY - 0.5) * speed;
          shape.style.transform = `translate(${x}px, ${y}px) rotate(${x}deg)`;
        });
      });
    }

    // Smooth form validation
    const inputs = document.querySelectorAll('input[required]');
    inputs.forEach(input => {
      input.addEventListener('invalid', function(e) {
        e.preventDefault();
        this.style.borderColor = '#ff6b6b';
        this.style.boxShadow = '0 0 0 3px rgba(255, 107, 107, 0.2)';
        
        setTimeout(() => {
          this.style.borderColor = '';
          this.style.boxShadow = '';
        }, 3000);
      });
    });
  </script>
</body>
</html>
