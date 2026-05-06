<?php
session_start();
require_once 'course_data.php';

if (!isset($_SESSION['cart']))
    $_SESSION['cart'] = [];

$items = $_SESSION['cart']; // [course_id => quantity]
$total = 0;
foreach ($items as $id => $qty) {
    if (isset($courses[$id])) {
        $total += $courses[$id]['price'] * $qty;
    }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>ตะกร้าสินค้า – ICONNEX</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;600;700&family=Sarabun:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <nav>
        <div class="nav-logo">
            <div class="logo-icon">🌀</div>ICONNEX
        </div>
        <ul class="nav-links">
            <li><a href="index.php">หน้าหลัก</a></li>
            <li><a href="index.php#courses">คอร์ส</a></li>
            <li><a href="cart.php" class="active">🛒 ตะกร้า
                    <?php if (array_sum($items) > 0): ?>
                        <span
                            style="background:var(--accent,#6c63ff);color:#fff;border-radius:50%;padding:1px 7px;font-size:.75rem;font-weight:700;margin-left:4px;">
                            <?= array_sum($items) ?>
                        </span>
                    <?php endif; ?>
                </a></li>
        </ul>
    </nav>

    <div class="cart-page">
        <div class="cart-title">🛒 ตะกร้าสินค้า</div>

        <?php if (empty($items)): ?>
            <div class="cart-empty">
                <div style="font-size:4rem;margin-bottom:16px;">🛒</div>
                <p>ตะกร้าของคุณว่างเปล่า</p>
                <a href="index.php#courses">← เลือกดูคอร์สทั้งหมด</a>
            </div>

        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>คอร์ส</th>
                        <th>ราคา/ชิ้น</th>
                        <th>จำนวน</th>
                        <th>รวม</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="cart-body">
                    <?php foreach ($items as $id => $qty):
                        $c = $courses[$id] ?? null;
                        if (!$c)
                            continue;
                        ?>
                        <tr id="row-<?= $id ?>">
                            <td>
                                <div class="cart-course-name"><?= htmlspecialchars($c['name']) ?></div>
                                <div class="cart-instructor">โดย <?= htmlspecialchars($c['instructor']) ?></div>
                            </td>
                            <td>฿<?= number_format($c['price']) ?></td>
                            <td>
                                <div class="qty-control">
                                    <button class="qty-btn"
                                        onclick="updateQty(<?= $id ?>, +document.getElementById('qty-<?= $id ?>').textContent - 1)">−</button>
                                    <span class="qty-val" id="qty-<?= $id ?>"><?= $qty ?></span>
                                    <button class="qty-btn"
                                        onclick="updateQty(<?= $id ?>, +document.getElementById('qty-<?= $id ?>').textContent + 1)">+</button>
                                </div>
                            </td>
                            <td id="subtotal-<?= $id ?>">฿<?= number_format($c['price'] * $qty) ?></td>
                            <td><button class="remove-btn" onclick="removeItem(<?= $id ?>)">✕</button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <div class="summary-box">
                    <div class="summary-row">
                        <span>จำนวนรายการ</span>
                        <span id="item-count"><?= count($items) ?> คอร์ส</span>
                    </div>
                    <div class="summary-total">
                        <span>ยอดรวม</span>
                        <span id="grand-total">฿<?= number_format($total) ?></span>
                    </div>
                    <button class="checkout-btn" onclick="checkout()">ดำเนินการชำระเงิน →</button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="toast" id="toast"></div>

    <script>
        // ราคาต่อหน่วย (ส่งมาจาก PHP เพื่อคำนวณ subtotal ฝั่ง client)
        const prices = {
            <?php foreach ($courses as $id => $c): ?>
            <?= $id ?>: <?= $c['price'] ?>,
            <?php endforeach; ?>
        };

        function showToast(msg) {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 2500);
        }

        function updateQty(courseId, qty) {
            fetch('cart_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=update&course_id=${courseId}&quantity=${qty}`
            })
                .then(r => r.json())
                .then(data => {
                    if (qty < 1) {
                        document.getElementById('row-' + courseId)?.remove();
                        showToast('ลบสินค้าแล้ว');
                    } else {
                        document.getElementById('qty-' + courseId).textContent = qty;
                        const sub = prices[courseId] * qty;
                        document.getElementById('subtotal-' + courseId).textContent = '฿' + sub.toLocaleString();
                    }
                    document.getElementById('grand-total').textContent = '฿' + data.total.toLocaleString();
                });
        }

        function removeItem(courseId) {
            fetch('cart_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=remove&course_id=${courseId}`
            })
                .then(r => r.json())
                .then(data => {
                    document.getElementById('row-' + courseId)?.remove();
                    document.getElementById('grand-total').textContent = '฿' + data.total.toLocaleString();
                    showToast('ลบสินค้าแล้ว ✓');
                });
        }
        function checkout() {
            const items = [];
            <?php foreach ($items as $id => $qty): 
                $c = $courses[$id] ?? null;
                if ($c): ?>
                items.push({
                    id: <?= $id ?>,
                    name: <?= json_encode($c['name']) ?>,
                    price: <?= $c['price'] ?>,
                    qty: <?= $qty ?>,
                    instructor: <?= json_encode($c['instructor']) ?>,
                    image: <?= json_encode($c['image']) ?>,
                    description: <?= json_encode($c['description']) ?>,
                    category: <?= json_encode($c['category']) ?>,
                    hours: <?= $c['hours'] ?>,
                    lessons: <?= $c['lessons'] ?>
                });
            <?php endif; endforeach; ?>
            
            localStorage.setItem('checkoutCart', JSON.stringify(items));
            location.href = 'pay2.1/index.html';
        }
    </script>
</body>

</html>