<?php
require_once 'lang.php';
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/index.php');
    } else {
        header('Location: index.php');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('login_title'); ?> - ICONNEX Creators Club</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4A68BD;
            --primary-dark: #3a5299;
            --accent: #ffae35;
            --bg-dark: #022f58;
            --text-main: #333;
            --text-muted: #666;
            --glass-bg: rgba(255, 255, 255, 0.9);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Prompt', sans-serif;
        }

        body {
            background-color: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        .login-wrapper {
            display: flex;
            width: 1000px;
            max-width: 95%;
            height: 750px;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            position: relative;
        }

        /* Left Side: Image/Info */
        .login-visual {
            flex: 1.2;
            background: linear-gradient(135deg, rgba(2, 47, 88, 0.8) 0%, rgba(15, 1, 95, 0.8) 100%),
                        url('https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            padding: 50px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .login-visual::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(circle at center, transparent 0%, rgba(0,0,0,0.4) 100%);
        }

        .visual-content {
            position: relative;
            z-index: 1;
        }

        .visual-content h1 {
            font-size: 3rem;
            line-height: 1.2;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .visual-content p {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 400px;
            line-height: 1.6;
        }

        /* Right Side: Form */
        .login-container {
            flex: 1;
            padding: 40px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #fff;
            overflow-y: auto;
        }

        .brand {
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--bg-dark);
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            background: var(--bg-dark);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .login-header h2 {
            font-size: 1.8rem;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .login-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            color: #444;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 41px;
            color: #aaa;
            transition: var(--transition);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #eee;
            border-radius: 12px;
            font-size: 1rem;
            outline: none;
            transition: var(--transition);
            background: #fcfcfc;
        }

        .form-control:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(74, 104, 189, 0.1);
        }

        .form-control:focus + .input-icon {
            color: var(--primary);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            font-size: 0.85rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: var(--text-muted);
        }

        .forgot-pass {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-pass:hover {
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(to right, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(74, 104, 189, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 104, 189, 0.4);
        }

        .btn-login:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .social-login {
            margin-top: 30px;
        }

        .social-divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin-bottom: 20px;
            color: #aaa;
            font-size: 0.8rem;
        }

        .social-divider::before, .social-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #eee;
        }

        .social-divider:not(:empty)::before { margin-right: 15px; }
        .social-divider:not(:empty)::after { margin-left: 15px; }

        .social-buttons {
            display: flex;
            gap: 15px;
        }

        .social-btn {
            flex: 1;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 10px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
        }

        .social-btn:hover {
            background: #f8f9fa;
            border-color: #ddd;
        }

        .register-link {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .register-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .error-msg {
            background: #fff5f5;
            color: #e53e3e;
            padding: 12px;
            border-radius: 10px;
            font-size: 0.9rem;
            margin-bottom: 20px;
            display: none;
            border-left: 4px solid #e53e3e;
            align-items: center;
            gap: 10px;
        }

        .back-to-home {
            position: absolute;
            top: 20px;
            right: 20px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 5px;
            z-index: 10;
        }

        .back-to-home:hover {
            color: var(--primary);
        }

        /* Mobile Adjustments */
        @media (max-width: 900px) {
            .login-wrapper {
                height: auto;
                flex-direction: column;
            }
            .login-visual {
                display: none;
            }
            .login-container {
                padding: 40px 25px;
            }
        }

        /* Loading Spinner */
        .spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <a href="index.php" class="back-to-home"><i class="fas fa-arrow-left"></i> <?php echo __('nav_home'); ?></a>

    <div class="login-wrapper">
        <!-- Left Side -->
        <div class="login-visual">
            <div class="visual-content">
                <h1>Unlock Your Creative Potential.</h1>
                <p>Join the community of next-gen creators and master the art of video editing with ICONNEX Creators Club.</p>
            </div>
        </div>

        <!-- Right Side -->
        <div class="login-container">
            <div class="brand">
                <div class="brand-icon"><i class="fas fa-wind"></i></div>
                ICONNEX
            </div>

            <div class="login-header">
                <h2><?php echo __('login_title'); ?></h2>
                <p><?php echo __('login_subtitle'); ?></p>
            </div>

            <div class="error-msg" id="errorMsg">
                <i class="fas fa-circle-exclamation"></i>
                <span id="errorText"></span>
            </div>

            <form id="loginForm" onsubmit="handleLogin(event)">
                <div class="form-group">
                    <label for="username"><?php echo __('label_username'); ?></label>
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" class="form-control" id="username" placeholder="<?php echo __('label_username'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password"><?php echo __('label_password'); ?></label>
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" class="form-control" id="password" placeholder="<?php echo __('label_password'); ?>" required>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox"> จดจำฉันไว้
                    </label>
                    <a href="#" class="forgot-pass">ลืมรหัสผ่าน?</a>
                </div>

                <button type="submit" class="btn-login" id="submitBtn">
                    <span id="btnText"><?php echo __('btn_submit_login'); ?></span>
                    <div class="spinner" id="btnSpinner"></div>
                </button>
            </form>

            <div class="social-login">
                <div class="social-divider">หรือเข้าสู่ระบบด้วย</div>
                <div class="social-buttons">
                    <button class="social-btn">
                        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" width="18" alt="Google">
                        Google
                    </button>
                    <button class="social-btn">
                        <i class="fab fa-apple"></i>
                        Apple
                    </button>
                </div>
            </div>

            <div class="register-link">
                <?php echo __('link_no_account'); ?> <a href="register.php"><?php echo __('btn_register'); ?></a>
            </div>
        </div>
    </div>

    <script>
    async function handleLogin(e) {
        e.preventDefault();
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        const errorMsg = document.getElementById('errorMsg');
        const errorText = document.getElementById('errorText');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');

        // Reset & Loading State
        errorMsg.style.display = 'none';
        submitBtn.disabled = true;
        btnText.style.display = 'none';
        btnSpinner.style.display = 'block';

        try {
            const fd = new URLSearchParams();
            fd.append('username', username);
            fd.append('password', password);
            fd.append('login_type', 'user');

            const res = await fetch('auth.php', {
                method: 'POST',
                body: fd
            });
            const data = await res.json();

            if (data.success) {
                // Success animation/delay
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 500);
            } else {
                showError(data.error);
            }
        } catch (err) {
            showError('เกิดข้อผิดพลาดในการเชื่อมต่อ กรุณาลองใหม่อีกครั้ง');
        }
    }

    function showError(msg) {
        const errorMsg = document.getElementById('errorMsg');
        const errorText = document.getElementById('errorText');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');

        errorText.textContent = msg;
        errorMsg.style.display = 'flex';
        submitBtn.disabled = false;
        btnText.style.display = 'block';
        btnSpinner.style.display = 'none';
        
        // Shake animation
        errorMsg.style.animation = 'shake 0.5s cubic-bezier(.36,.07,.19,.97) both';
        setTimeout(() => { errorMsg.style.animation = ''; }, 500);
    }
    </script>

    <style>
    @keyframes shake {
        10%, 90% { transform: translate3d(-1px, 0, 0); }
        20%, 80% { transform: translate3d(2px, 0, 0); }
        30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
        40%, 60% { transform: translate3d(4px, 0, 0); }
    }
    </style>

</body>
</html>
