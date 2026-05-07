<?php
session_start();
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
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICONNEX - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Prompt', sans-serif; }
        body { background: linear-gradient(135deg, #022f58 0%, #0f015f 100%); display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .login-card { background: white; width: 100%; max-width: 400px; border-radius: 16px; padding: 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); }
        .login-card h2 { text-align: center; color: #333; margin-bottom: 30px; font-weight: 700; font-size: 28px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #555; font-size: 14px; font-weight: 600; }
        .form-control { width: 100%; padding: 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px; outline: none; transition: border 0.2s; }
        .form-control:focus { border-color: #4A68BD; box-shadow: 0 0 0 3px rgba(74, 104, 189, 0.1); }
        .btn-submit { width: 100%; padding: 14px; background: #4A68BD; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background 0.2s; margin-top: 10px; }
        .btn-submit:hover { background: #3a5299; }
        .error-msg { color: #E53935; font-size: 14px; margin-bottom: 20px; text-align: center; display: none; background: #ffebee; padding: 10px; border-radius: 6px; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #666; text-decoration: none; font-size: 14px; }
        .back-link:hover { color: #333; text-decoration: underline; }
    </style>
</head>
<body>

<div class="login-card">
    <h2>เข้าสู่ระบบ (ผู้ใช้งาน)</h2>
    <div class="error-msg" id="errorMsg"></div>
    <form id="loginForm" onsubmit="handleLogin(event)">
        <div class="form-group">
            <label>ชื่อผู้ใช้งาน (Username)</label>
            <input type="text" class="form-control" id="username" required>
        </div>
        <div class="form-group">
            <label>รหัสผ่าน (Password)</label>
            <input type="password" class="form-control" id="password" required>
        </div>
        <button type="submit" class="btn-submit" id="submitBtn">เข้าสู่ระบบ</button>
    </form>
    <a href="register.php" class="back-link">ยังไม่มีบัญชีใช่ไหม? สมัครสมาชิกที่นี่</a>
    <a href="index.php" class="back-link" style="margin-top:10px;">← กลับไปหน้าหลัก</a>
    <a href="admin/login.php" class="back-link" style="color:#aaa; font-size:12px; margin-top:10px;">เข้าสู่ระบบผู้ดูแลระบบ (Admin)</a>
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
        fd.append('login_type', 'user');

        const res = await fetch('auth.php', {
            method: 'POST',
            body: fd
        });
        const data = await res.json();

        if (data.success) {
            window.location.href = data.redirect;
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
