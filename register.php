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
    <title>ICONNEX - สมัครสมาชิก</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Prompt', sans-serif; }
        body { background: linear-gradient(135deg, #022f58 0%, #0f015f 100%); display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; }
        .login-card { background: white; width: 100%; max-width: 450px; border-radius: 16px; padding: 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); }
        .login-card h2 { text-align: center; color: #333; margin-bottom: 30px; font-weight: 700; font-size: 28px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 8px; color: #555; font-size: 14px; font-weight: 600; }
        .form-control { width: 100%; padding: 12px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px; outline: none; transition: border 0.2s; }
        .form-control:focus { border-color: #4A68BD; box-shadow: 0 0 0 3px rgba(74, 104, 189, 0.1); }
        .btn-submit { width: 100%; padding: 14px; background: #22d3a0; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background 0.2s; margin-top: 10px; }
        .btn-submit:hover { background: #1eb387; }
        .error-msg { color: #E53935; font-size: 14px; margin-bottom: 20px; text-align: center; display: none; background: #ffebee; padding: 10px; border-radius: 6px; }
        .success-msg { color: #4CAF50; font-size: 14px; margin-bottom: 20px; text-align: center; display: none; background: #E8F5E9; padding: 10px; border-radius: 6px; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #666; text-decoration: none; font-size: 14px; }
        .back-link:hover { color: #333; text-decoration: underline; }
    </style>
</head>
<body>

<div class="login-card">
    <h2>สมัครสมาชิก</h2>
    <div class="error-msg" id="errorMsg"></div>
    <div class="success-msg" id="successMsg"></div>
    <form id="registerForm" onsubmit="handleRegister(event)">
        <div class="form-group">
            <label>ชื่อผู้ใช้งาน (Username)</label>
            <input type="text" class="form-control" id="username" required>
        </div>
        <div class="form-group">
            <label>อีเมล (Email)</label>
            <input type="email" class="form-control" id="email" required>
        </div>
        <div class="form-group">
            <label>รหัสผ่าน (Password)</label>
            <input type="password" class="form-control" id="password" required minlength="6">
        </div>
        <div class="form-group">
            <label>ยืนยันรหัสผ่าน (Confirm Password)</label>
            <input type="password" class="form-control" id="confirm_password" required minlength="6">
        </div>
        <button type="submit" class="btn-submit" id="submitBtn">ลงทะเบียน</button>
    </form>
    <a href="login.php" class="back-link">มีบัญชีผู้ใช้งานแล้ว? เข้าสู่ระบบที่นี่</a>
    <a href="index.php" class="back-link" style="color:#aaa; font-size:12px; margin-top:10px;">← กลับไปหน้าหลัก</a>
</div>

<script>
async function handleRegister(e) {
    e.preventDefault();
    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirm_password = document.getElementById('confirm_password').value;
    
    const errorMsg = document.getElementById('errorMsg');
    const successMsg = document.getElementById('successMsg');
    const submitBtn = document.getElementById('submitBtn');

    if (password !== confirm_password) {
        errorMsg.textContent = 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน';
        errorMsg.style.display = 'block';
        successMsg.style.display = 'none';
        return;
    }

    submitBtn.textContent = 'กำลังลงทะเบียน...';
    submitBtn.disabled = true;
    errorMsg.style.display = 'none';
    successMsg.style.display = 'none';

    try {
        const fd = new URLSearchParams();
        fd.append('username', username);
        fd.append('email', email);
        fd.append('password', password);

        const res = await fetch('register_action.php', {
            method: 'POST',
            body: fd
        });
        const data = await res.json();

        if (data.success) {
            successMsg.textContent = 'สมัครสมาชิกสำเร็จ! กำลังพากลับไปหน้าเข้าสู่ระบบ...';
            successMsg.style.display = 'block';
            document.getElementById('registerForm').reset();
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 2000);
        } else {
            errorMsg.textContent = data.error;
            errorMsg.style.display = 'block';
            submitBtn.textContent = 'ลงทะเบียน';
            submitBtn.disabled = false;
        }
    } catch (err) {
        errorMsg.textContent = 'เกิดข้อผิดพลาดในการเชื่อมต่อ';
        errorMsg.style.display = 'block';
        submitBtn.textContent = 'ลงทะเบียน';
        submitBtn.disabled = false;
    }
}
</script>

</body>
</html>
