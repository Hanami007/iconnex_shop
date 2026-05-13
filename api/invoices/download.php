<?php
// Since we don't have a PDF library like Dompdf installed,
// we'll serve a clean, printable HTML version that triggers the browser's print dialog.
// This is a common and effective approach for "PDF" downloads in simple PHP setups.

require_once __DIR__ . '/../../src/bootstrap.php';
useService('db');
useService('invoice_service');
useService('tax_service');

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$id = $_GET['id'] ?? null;
if (!$id) die("Missing ID");

$invoiceService = new InvoiceService($pdo);
$invoice = $invoiceService->getInvoice($id);

if (!$invoice || ($invoice['user_id'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'admin')) {
    die("Access Denied");
}

// Reuse the template logic from invoice_view.php but optimized for print
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice_<?php echo $invoice['invoice_no']; ?></title>
    <!-- Use html2pdf.js from CDN for direct client-side PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body { font-family: "Sarabun", "Tahoma", sans-serif; margin: 0; padding: 0; background: #f0f2f5; }
        #invoice-content { 
            width: 210mm; 
            min-height: 297mm; 
            padding: 20mm; 
            margin: 20px auto; 
            background: #fff; 
            box-sizing: border-box; 
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .company-info h2 { margin: 0 0 10px; color: #0b1221; font-size: 24px; }
        .company-info p { margin: 2px 0; font-size: 12px; color: #666; }
        
        .invoice-title { text-align: right; }
        .invoice-title h1 { margin: 0 0 10px; font-size: 28px; color: #0b1221; }
        .details-grid { display: grid; grid-template-columns: auto auto; gap: 5px 15px; font-size: 13px; text-align: right; }
        
        .info-section { display: flex; gap: 40px; margin-bottom: 30px; background: #f8fafc; padding: 25px; border-radius: 8px; border: 1px solid #e2e8f0; }
        .info-box { flex: 1; }
        .info-box h3 { font-size: 11px; text-transform: uppercase; color: #94a3b8; margin: 0 0 12px; letter-spacing: 1px; }
        .info-box p { margin: 2px 0; font-size: 13px; color: #334155; line-height: 1.6; }
        .info-box strong { color: #0f172a; font-size: 14px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #0b1221; color: #fff; padding: 12px 15px; text-align: left; font-size: 12px; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #334155; }
        .text-right { text-align: right; }
        
        .totals { display: flex; justify-content: flex-end; }
        .totals table { width: 280px; margin-bottom: 0; }
        .totals td { border: none; padding: 6px 15px; }
        .grand-total { border-top: 2px solid #0b1221 !important; font-weight: bold; font-size: 18px; color: #0b1221; padding-top: 15px !important; }

        .footer { margin-top: 60px; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 20px; }
        
        .loading-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255,255,255,0.9); display: flex; flex-direction: column;
            align-items: center; justify-content: center; z-index: 9999;
            font-family: sans-serif;
        }
        .spinner {
            width: 40px; height: 40px; border: 4px solid #f3f3f3;
            border-top: 4px solid #0b1221; border-radius: 50%;
            animation: spin 1s linear infinite; margin-bottom: 20px;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="loading-overlay" id="loader">
        <div class="spinner"></div>
        <p>กำลังเตรียมไฟล์ PDF กรุณารอสักครู่...</p>
    </div>

    <div id="invoice-content">
        <div class="header">
            <div class="company-info">
                <h2>ICONNEX</h2>
                <p><strong>ICONNEX CREATORS CLUB CO., LTD.</strong></p>
                <p>123 Creative Building, Sukhumvit Rd, Bangkok 10110</p>
                <p>Tax ID: 0105566000123 (Head Office)</p>
            </div>
            <div class="invoice-title">
                <h1>TAX INVOICE</h1>
                <div class="details-grid">
                    <span>Invoice No:</span> <strong><?php echo $invoice['invoice_no']; ?></strong>
                    <span>Date:</span> <strong><?php echo date('d/m/Y', strtotime($invoice['created_at'])); ?></strong>
                </div>
            </div>
        </div>

        <div class="info-section">
            <div class="info-box">
                <h3>Seller</h3>
                <p><strong>ICONNEX CREATORS CLUB CO., LTD.</strong></p>
                <p>123 Creative Building, Sukhumvit Rd, Bangkok 10110</p>
            </div>
            <div class="info-box">
                <h3>Buyer</h3>
                <p><strong><?php echo htmlspecialchars($invoice['customer_name']); ?></strong></p>
                <p><?php echo nl2br(htmlspecialchars($invoice['customer_address'])); ?></p>
                <?php if($invoice['customer_tax_id']): ?>
                    <p>Tax ID: <?php echo htmlspecialchars($invoice['customer_tax_id']); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right" style="width: 50px;">Qty</th>
                    <th class="text-right" style="width: 110px;">Unit Price</th>
                    <th class="text-right" style="width: 110px;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoice['items'] as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['course_name']); ?></td>
                        <td class="text-right"><?php echo $item['qty']; ?></td>
                        <td class="text-right"><?php echo number_format($item['unit_price'], 2); ?></td>
                        <td class="text-right"><?php echo number_format($item['total'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td class="text-right">Subtotal:</td>
                    <td class="text-right"><?php echo number_format($invoice['subtotal'], 2); ?></td>
                </tr>
                <tr>
                    <td class="text-right">VAT (7%):</td>
                    <td class="text-right"><?php echo number_format($invoice['vat_amount'], 2); ?></td>
                </tr>
                <tr>
                    <td class="text-right grand-total">Total:</td>
                    <td class="text-right grand-total">฿<?php echo number_format($invoice['total'], 2); ?></td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>This is a computer-generated document. No signature is required.</p>
            <p>Printed on: <?php echo date('d/m/Y H:i:s'); ?></p>
        </div>
    </div>

    <script>
        window.onload = function() {
            const element = document.getElementById('invoice-content');
            const opt = {
                margin:       0,
                filename:     'Invoice_<?php echo $invoice['invoice_no']; ?>.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            // New Promise-based usage:
            html2pdf().set(opt).from(element).save().then(() => {
                document.getElementById('loader').innerHTML = '<p>ดาวน์โหลดเสร็จสิ้น!</p><button onclick="window.close()" style="margin-top:10px; padding:8px 20px; cursor:pointer;">ปิดหน้าต่างนี้</button>';
            });
        };
    </script>
</body>
</html>
