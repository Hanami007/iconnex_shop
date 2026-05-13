<?php
useService('db');
useService('lang');
useService('invoice_service');
useService('tax_service');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Invoice ID required");
}

$invoiceService = new InvoiceService($pdo);
$invoice = $invoiceService->getInvoice($id);

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function getOrderNoById($orderId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT order_no FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $res = $stmt->fetch();
    return $res ? $res['order_no'] : 'N/A';
}

if (!$invoice || ($invoice['user_id'] != $_SESSION['user_id'] && !isAdmin())) {
    die("Invoice not found or access denied");
}

// Company Info (Hardcoded for this demo, usually from config/settings)
$company = [
    'name' => 'ICONNEX CREATORS CLUB CO., LTD.',
    'address' => '123 Creative Building, Sukhumvit Rd, Khlong Toei, Bangkok 10110',
    'tax_id' => '0105566000123',
    'branch' => 'สำนักงานใหญ่ (Head Office)',
    'phone' => '02-123-4567',
    'email' => 'billing@iconnex.club'
];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?php echo $invoice['invoice_no']; ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&family=Sora:wght@700;800&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="src/styles/notification.css?v=1.2" />
    <link rel="stylesheet" href="src/styles/style.css?v=1.1" />
    <style>
        :root {
            --primary: #0b1221;
            --accent: #c9a84c;
            --border: #e2e8f0;
            --text: #1e293b;
            --text-light: #64748b;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Sarabun', sans-serif; background: #f1f5f9; color: var(--text); padding: 40px 20px; line-height: 1.5; }
        
        .invoice-actions { max-width: 800px; margin: 0 auto 20px; display: flex; justify-content: space-between; align-items: center; }
        .btn { padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; border: none; transition: 0.3s; }
        .btn-back { background: #fff; color: var(--text); border: 1px solid var(--border); }
        .btn-print { background: var(--primary); color: #fff; }
        .btn-pdf { background: var(--accent); color: #fff; }
        
        .invoice-paper { background: #fff; width: 800px; min-height: 1120px; margin: 0 auto; padding: 60px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); position: relative; }
        
        /* Header */
        .header { display: flex; justify-content: space-between; margin-bottom: 50px; }
        .company-info h2 { font-family: 'Sora', sans-serif; font-size: 1.5rem; color: var(--primary); margin-bottom: 10px; letter-spacing: -1px; }
        .company-info p { font-size: 0.85rem; color: var(--text-light); max-width: 350px; margin-bottom: 4px; }
        
        .invoice-title { text-align: right; }
        .invoice-title h1 { font-size: 1.4rem; color: var(--primary); margin-bottom: 10px; }
        .invoice-details { display: grid; grid-template-columns: auto auto; gap: 8px 20px; text-align: right; font-size: 0.9rem; }
        .invoice-details dt { color: var(--text-light); font-weight: 400; }
        .invoice-details dd { font-weight: 600; }

        /* Buyer/Seller Row */
        .info-row { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px; padding: 30px; background: #f8fafc; border-radius: 12px; }
        .info-box h3 { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-light); margin-bottom: 12px; }
        .info-box p { font-size: 0.9rem; line-height: 1.6; }
        .info-box strong { display: block; margin-bottom: 4px; font-size: 1rem; }

        /* Table */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .items-table th { padding: 12px 15px; text-align: left; background: var(--primary); color: #fff; font-size: 0.8rem; text-transform: uppercase; }
        .items-table td { padding: 15px; border-bottom: 1px solid var(--border); font-size: 0.9rem; }
        .items-table .num { width: 40px; text-align: center; }
        .items-table .qty { width: 60px; text-align: center; }
        .items-table .price { width: 120px; text-align: right; }
        .items-table .amount { width: 120px; text-align: right; font-weight: 600; }

        /* Totals */
        .totals-section { display: flex; justify-content: flex-end; }
        .totals-table { width: 300px; border-collapse: collapse; }
        .totals-table td { padding: 8px 15px; font-size: 0.95rem; }
        .totals-table .label { text-align: right; color: var(--text-light); }
        .totals-table .val { text-align: right; width: 120px; }
        .totals-table .grand-total { font-size: 1.25rem; font-weight: 700; color: var(--primary); border-top: 2px solid var(--primary); padding-top: 15px; margin-top: 10px; }

        .footer-note { margin-top: 80px; padding-top: 30px; border-top: 1px solid var(--border); color: var(--text-light); font-size: 0.8rem; display: flex; justify-content: space-between; }
        
        @media print {
            body { background: #fff; padding: 0; }
            .invoice-actions { display: none; }
            .invoice-paper { box-shadow: none; width: 100%; }
        }
    </style>
</head>
<body>
    <?php renderComponent('Navbar'); ?>
    
    <div class="invoice-actions">
        <a href="invoices.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> ย้อนกลับ</a>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn btn-print"><i class="fas fa-print"></i> พิมพ์</button>
            <a href="api/invoices/download.php?id=<?php echo $invoice['id']; ?>" class="btn btn-pdf"><i class="fas fa-file-pdf"></i> ดาวน์โหลด PDF</a>
        </div>
    </div>

    <div class="invoice-paper">
        <div class="header">
            <div class="company-info">
                <h2>ICONNEX</h2>
                <p><strong><?php echo $company['name']; ?></strong></p>
                <p><?php echo $company['address']; ?></p>
                <p>เลขประจำตัวผู้เสียภาษี: <?php echo $company['tax_id']; ?> (<?php echo $company['branch']; ?>)</p>
                <p>โทร: <?php echo $company['phone']; ?> | อีเมล: <?php echo $company['email']; ?></p>
            </div>
            <div class="invoice-title">
                <h1>ใบกำกับภาษี / TAX INVOICE</h1>
                <div class="invoice-details">
                    <dt>เลขที่ใบกำกับภาษี</dt>
                    <dd><?php echo $invoice['invoice_no']; ?></dd>
                    <dt>วันที่ออก</dt>
                    <dd><?php echo date('d/m/Y', strtotime($invoice['created_at'])); ?></dd>
                    <dt>อ้างอิงใบสั่งซื้อ</dt>
                    <dd><?php echo getOrderNoById($invoice['order_id']); ?></dd>
                </div>
            </div>
        </div>

        <div class="info-row">
            <div class="info-box">
                <h3>ผู้ออกใบกำกับภาษี (Seller)</h3>
                <p><strong><?php echo $company['name']; ?></strong></p>
                <p><?php echo $company['address']; ?></p>
                <p>เลขประจำตัวผู้เสียภาษี: <?php echo $company['tax_id']; ?></p>
            </div>
            <div class="info-box">
                <h3>ผู้ซื้อ (Buyer)</h3>
                <p><strong><?php echo htmlspecialchars($invoice['customer_name']); ?></strong></p>
                <p><?php echo nl2br(htmlspecialchars($invoice['customer_address'])); ?></p>
                <?php if($invoice['customer_tax_id']): ?>
                    <p>เลขประจำตัวผู้เสียภาษี: <?php echo htmlspecialchars($invoice['customer_tax_id']); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th class="num">#</th>
                    <th>รายการสินค้า / บริการ</th>
                    <th class="qty">จำนวน</th>
                    <th class="price">ราคาต่อหน่วย</th>
                    <th class="amount">จำนวนเงิน</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoice['items'] as $index => $item): ?>
                    <tr>
                        <td class="num"><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($item['course_name']); ?></td>
                        <td class="qty"><?php echo $item['qty']; ?></td>
                        <td class="price"><?php echo number_format($item['unit_price'], 2); ?></td>
                        <td class="amount"><?php echo number_format($item['total'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="label">รวมราคาสินค้า (Subtotal)</td>
                    <td class="val"><?php echo number_format($invoice['subtotal'], 2); ?></td>
                </tr>
                <tr>
                    <td class="label">ภาษีมูลค่าเพิ่ม (VAT 7%)</td>
                    <td class="val"><?php echo number_format($invoice['vat_amount'], 2); ?></td>
                </tr>
                <tr>
                    <td class="label grand-total">ราคาสุทธิ (Total)</td>
                    <td class="val grand-total"><?php echo number_format($invoice['total'], 2); ?></td>
                </tr>
            </table>
        </div>

        <div class="footer-note">
            <div>
                <p>* ชำระเงินสมบูรณ์แล้ว</p>
                <p>เอกสารนี้จัดทำขึ้นด้วยระบบอัตโนมัติ</p>
            </div>
            <div style="text-align: right;">
                <p>ผู้ออกเอกสาร: ฝ่ายบัญชี</p>
                <p>วันที่พิมพ์: <?php echo date('d/m/Y H:i'); ?></p>
            </div>
        </div>
    </div>
    <script>
        const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    </script>
    <script src="src/assets/js/notification/notificationService.js?v=1.1"></script>
    <script src="src/assets/js/notification/notificationUI.js?v=1.1"></script>
    <script src="src/assets/js/script.js?v=1.1"></script>
</body>
</html>
