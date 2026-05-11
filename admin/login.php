<?php
require_once '../src/bootstrap.php';
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: index.php');
    } else {
        header('Location: ../index.php');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICONNEX - Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;600;700&display=swap" rel="stylesheet">
    <script>const csrfToken = "<?php echo getCsrfToken(); ?>";</script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Prompt', sans-serif; }
        body { background: #0d0f14; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .login-card { background: #1a1e28; width: 100%; max-width: 400px; border-radius: 16px; padding: 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.5); border: 1px solid #2a2f3a; }
        .login-card h2 { text-align: center; color: #fff; margin-bottom: 30px; font-weight: 700; font-size: 28px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #aaa; font-size: 14px; font-weight: 600; }
        .form-control { width: 100%; padding: 14px; background: #0d0f14; border: 1px solid #333; color: #fff; border-radius: 8px; font-size: 15px; outline: none; transition: border 0.2s; }
        .form-control:focus { border-color: #6c63ff; }
        .btn-submit { width: 100%; padding: 14px; background: #6c63ff; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background 0.2s; margin-top: 10px; }
        .btn-submit:hover { background: #574fcf; }
        .error-msg { color: #ff6b6b; font-size: 14px; margin-bottom: 20px; text-align: center; display: none; background: rgba(255, 107, 107, 0.1); padding: 10px; border-radius: 6px; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #8890a8; text-decoration: none; font-size: 14px; }
        .back-link:hover { color: #fff; }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Admin Login</h2>
    <div class="error-msg" id="errorMsg"></div>
    <form id="loginForm" onsubmit="handleLogin(event)">
        <div class="form-group">
            <label>Username</label>
            <input type="text" class="form-control" id="username" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" class="form-control" id="password" required>
        </div>
        <button type="submit" class="btn-submit" id="submitBtn">เข้าสู่ระบบ</button>
    </form>
    <a href="../login.php" class="back-link">← เข้าสู่ระบบผู้ใช้งานปกติ</a>
</div>

<script>
async function handleLogin(e) {
    e.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const errorMsg = document.getElementById('errorMsg');
    const submitBtn = document.getElementById('submitBtn');

    submitBtn.textContent = 'กำลังตรวจสอบ...';
    submitBtn.disabled = true;
    errorMsg.style.display = 'none';

    try {
        const fd = new URLSearchParams();
        fd.append('username', username);
        fd.append('password', password);
        fd.append('login_type', 'admin');
        fd.append('csrf_token', csrfToken);

        const res = await fetch('../api/auth.php', {
            method: 'POST',
            body: fd
        });
        const data = await res.json();

        if (data.success) {
            window.location.href = 'index.php'; // already in admin folder
        } else {
            errorMsg.textContent = data.error;
            errorMsg.style.display = 'block';
            submitBtn.textContent = 'เข้าสู่ระบบ';
            submitBtn.disabled = false;
        }
    } catch (err) {
        errorMsg.textContent = 'เกิดข้อผิดพลาดในการเชื่อมต่อ';
        errorMsg.style.display = 'block';
        submitBtn.textContent = 'เข้าสู่ระบบ';
        submitBtn.disabled = false;
    }
}
</script>

</body>
</html>
