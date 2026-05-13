<?php
useService('db');
useService('lang');
useService('invoice_service');

// Simple Admin Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied: Admins Only");
}

$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$message = '';
if (isset($_POST['action']) && $_POST['action'] === 'complete' && isset($_POST['order_id'])) {
    $orderId = $_POST['order_id'];
    
    try {
        // 1. Get order data
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($order && $order['status'] === 'pending') {
            // 2. Update status to completed
            $pdo->prepare("UPDATE orders SET status = 'completed' WHERE id = ?")->execute([$orderId]);
            
            // 3. Generate Invoice Automatically
            $invoiceService = new InvoiceService($pdo);
            $customerData = [
                'name' => $order['customer_name'],
                'address' => $order['billing_address'] ?: 'No address provided',
                'tax_id' => $order['tax_id'] ?: null
            ];
            
            // Prepare items for invoice
            $items = json_decode($order['items_json'], true);
            $order['items'] = [];
            foreach($items as $it) {
                $order['items'][] = [
                    'course_id' => $it['id'] ?? 0,
                    'course_name' => $it['name'],
                    'qty' => 1,
                    'unit_price' => $it['price'],
                    'total_price' => $it['price']
                ];
            }
            
            $invoiceService->createInvoice($order, $customerData);
            $message = "Order #{$order['order_no']} completed and invoice generated!";
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Order Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="../../styles/style.css" />
    <style>
        body { background: #f8fafc; color: #1e293b; padding: 40px; }
        .admin-card { background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .btn-complete { background: #22c55e; color: #fff; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; }
        .alert { padding: 15px; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="admin-card">
        <h1>Order Management (Internal Admin)</h1>
        <?php if($message): ?><div class="alert"><?php echo $message; ?></div><?php endif; ?>
        
        <table>
            <thead>
                <tr>
                    <th>Order No.</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td><?php echo $o['order_no']; ?></td>
                        <td><?php echo $o['customer_name']; ?></td>
                        <td>฿<?php echo number_format($o['total_amount']); ?></td>
                        <td><?php echo strtoupper($o['status']); ?></td>
                        <td>
                            <?php if($o['status'] === 'pending'): ?>
                                <form method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                    <button type="submit" name="action" value="complete" class="btn-complete">Approve & Generate Invoice</button>
                                </form>
                            <?php else: ?>
                                <span style="color:#999;">Processed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
