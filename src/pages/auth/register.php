<?php
useService('lang');
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
    <title><?php echo __('register_title'); ?> - ICONNEX Creators Club</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #22d3a0;
            --primary-dark: #1eb387;
            --accent: #ffae35;
            --bg-dark: #022f58;
            --text-main: #333;
            --text-muted: #666;
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
            padding: 20px 0;
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
                        url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=2071&auto=format&fit=crop');
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
            margin-bottom: 30px;
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
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 15px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            font-size: 0.85rem;
            color: #444;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 36px;
            color: #aaa;
            transition: var(--transition);
        }

        .form-control {
            width: 100%;
            padding: 10px 15px 10px 45px;
            border: 2px solid #eee;
            border-radius: 12px;
            font-size: 0.95rem;
            outline: none;
            transition: var(--transition);
            background: #fcfcfc;
        }

        .form-control:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(34, 211, 160, 0.1);
        }

        .form-control:focus + .input-icon {
            color: var(--primary);
        }

        .btn-register {
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
            box-shadow: 0 4px 15px rgba(34, 211, 160, 0.3);
            margin-top: 10px;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(34, 211, 160, 0.4);
        }

        .btn-register:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .login-link a {
            color: var(--primary-dark);
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .error-msg, .success-msg {
            padding: 12px;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            display: none;
            align-items: center;
            gap: 10px;
        }

        .error-msg {
            background: #fff5f5;
            color: #e53e3e;
            border-left: 4px solid #e53e3e;
        }

        .success-msg {
            background: #f0fff4;
            color: #2f855a;
            border-left: 4px solid #38a169;
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
            color: var(--bg-dark);
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

        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }
    </style>
</head>
<body>

    <a href="index.php" class="back-to-home"><i class="fas fa-arrow-left"></i> <?php echo __('nav_home'); ?></a>

    <div class="login-wrapper">
        <!-- Left Side -->
        <div class="login-visual">
            <div class="visual-content">
                <h1>Join the Club of Visionaries.</h1>
                <p>Start your journey with ICONNEX today. Connect, learn, and grow with thousands of creative minds.</p>
            </div>
        </div>

        <!-- Right Side -->
        <div class="login-container">
            <div class="brand">
                <div class="brand-icon"><i class="fas fa-wind"></i></div>
                ICONNEX
            </div>

            <div class="login-header">
                <h2><?php echo __('register_title'); ?></h2>
                <p><?php echo __('register_subtitle'); ?></p>
            </div>

            <div class="error-msg" id="errorMsg">
                <i class="fas fa-circle-exclamation"></i>
                <span id="errorText"></span>
            </div>

            <div class="success-msg" id="successMsg">
                <i class="fas fa-circle-check"></i>
                <span id="successText"></span>
            </div>

            <form id="registerForm" onsubmit="handleRegister(event)">
                <div class="form-group">
                    <label for="username"><?php echo __('label_username'); ?></label>
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" class="form-control" id="username" placeholder="<?php echo __('label_username'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email"><?php echo __('label_email'); ?></label>
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" class="form-control" id="email" placeholder="example@email.com" required>
                </div>

                <div class="form-group">
                    <label for="password"><?php echo __('label_password'); ?></label>
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" class="form-control" id="password" placeholder="<?php echo __('label_password'); ?>" required minlength="6">
                </div>

                <div class="form-group">
                    <label for="confirm_password"><?php echo __('label_confirm_password'); ?></label>
                    <i class="fas fa-shield-halved input-icon"></i>
                    <input type="password" class="form-control" id="confirm_password" placeholder="<?php echo __('label_confirm_password'); ?>" required minlength="6">
                </div>

                <button type="submit" class="btn-register" id="submitBtn">
                    <span id="btnText"><?php echo __('btn_submit_register'); ?></span>
                    <div class="spinner" id="btnSpinner"></div>
                </button>
            </form>

            <div class="login-link">
                <?php echo __('link_has_account'); ?> <a href="login.php"><?php echo __('btn_login'); ?></a>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = "<?php echo getCsrfToken(); ?>";
    async function handleRegister(e) {
        e.preventDefault();
        const username = document.getElementById('username').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const confirm_password = document.getElementById('confirm_password').value;
        
        const errorMsg = document.getElementById('errorMsg');
        const errorText = document.getElementById('errorText');
        const successMsg = document.getElementById('successMsg');
        const successText = document.getElementById('successText');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');

        // Validation
        if (password !== confirm_password) {
            showError('รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน');
            return;
        }

        // Loading State
        errorMsg.style.display = 'none';
        successMsg.style.display = 'none';
        submitBtn.disabled = true;
        btnText.style.display = 'none';
        btnSpinner.style.display = 'block';

        try {
            const fd = new URLSearchParams();
            fd.append('username', username);
            fd.append('email', email);
            fd.append('password', password);
            fd.append('csrf_token', csrfToken);

            const res = await fetch('api/register.php', {
                method: 'POST',
                body: fd
            });
            const data = await res.json();

            if (data.success) {
                successText.textContent = 'สมัครสมาชิกสำเร็จ! กำลังพาไปหน้าเข้าสู่ระบบ...';
                successMsg.style.display = 'flex';
                document.getElementById('registerForm').reset();
                btnSpinner.style.display = 'none';
                
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 2000);
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
        
        errorMsg.style.animation = 'shake 0.5s cubic-bezier(.36,.07,.19,.97) both';
        setTimeout(() => { errorMsg.style.animation = ''; }, 500);
    }
    </script>

</body>
</html>
