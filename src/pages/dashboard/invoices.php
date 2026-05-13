<?php
useService('db');
useService('lang');
useService('invoice_service');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$invoiceService = new InvoiceService($pdo);
$invoices = $invoiceService->getUserInvoices($user_id);
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICONNEX – <?php echo __('nav_invoices'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="src/styles/style.css?v=1.1" />
    <link rel="stylesheet" href="src/styles/notification.css?v=1.2" />
    <style>
        :root {
            --sidebar-w: 280px;
            --dashboard-bg: #070b14;
        }
        body { background: var(--dashboard-bg); }
        .dashboard-wrapper { display: flex; min-height: 100vh; padding-top: 70px; }
        .dashboard-sidebar { width: var(--sidebar-w); padding: 20px; border-right: 1px solid var(--navy-border); position: sticky; top: 70px; height: calc(100vh - 70px); }
        .sidebar-menu { display: flex; flex-direction: column; gap: 8px; }
        .menu-item { display: flex; align-items: center; gap: 12px; padding: 14px 18px; border-radius: 12px; color: var(--text-muted); text-decoration: none; font-weight: 600; transition: 0.3s; }
        .menu-item:hover { background: var(--navy-light); color: var(--gold-soft); }
        .menu-item.active { background: var(--gold-pale); color: var(--gold); border: 1px solid var(--gold-line); }
        .dashboard-content { flex: 1; padding: 40px 60px; max-width: 1200px; }
        .content-header h1 { font-size: 2rem; font-family: 'Sora', sans-serif; font-weight: 800; color: #fff; margin-bottom: 8px; }
        
        .invoice-table-card { background: var(--navy-card); border: 1px solid var(--navy-border); border-radius: 20px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 18px 25px; background: rgba(255,255,255,0.02); color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid var(--navy-border); }
        td { padding: 20px 25px; border-bottom: 1px solid var(--navy-border); color: #fff; font-size: 0.95rem; }
        tr:last-child td { border-bottom: none; }
        .invoice-no { font-family: 'Sora', sans-serif; font-weight: 700; color: var(--gold-soft); }
        .btn-view { padding: 8px 16px; border-radius: 8px; background: var(--navy-light); color: #fff; text-decoration: none; font-size: 0.85rem; font-weight: 600; transition: 0.3s; display: inline-flex; align-items: center; gap: 8px; }
        .btn-view:hover { background: var(--gold); color: var(--navy-deep); }
        .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; }
        .status-active { background: rgba(34, 211, 160, 0.1); color: #22d3a0; }
        .status-cancelled { background: rgba(248, 113, 113, 0.1); color: #f87171; }
    </style>
    <script>
        const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    </script>
</head>
<body>
    <?php renderComponent('Navbar'); ?>
    <div class="dashboard-wrapper">
        <aside class="dashboard-sidebar">
            <div class="sidebar-menu">
                <a href="index.php" class="menu-item"><i class="fas fa-th-large"></i> Dashboard</a>
                <a href="index.php#courses" class="menu-item"><i class="fas fa-play-circle"></i> My Learning</a>
                <a href="my_orders.php" class="menu-item"><i class="fas fa-history"></i> Purchase History</a>
                <a href="invoices.php" class="menu-item active"><i class="fas fa-file-invoice-dollar"></i> Tax Invoices</a>
                <hr style="border: none; border-top: 1px solid var(--navy-border); margin: 15px 0;">
                <a href="logout.php" class="menu-item" style="color: var(--red);"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </aside>

        <main class="dashboard-content">
            <div class="content-header">
                <h1><?php echo __('nav_invoices'); ?></h1>
                <p>ดูและดาวน์โหลดใบกำกับภาษีเต็มรูปแบบของคุณ</p>
            </div>

            <div class="invoice-table-card">
                <?php if (empty($invoices)): ?>
                    <div style="padding: 60px; text-align: center; color: var(--text-muted);">
                        <i class="fas fa-file-invoice" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.3;"></i>
                        <p>ยังไม่มีรายการใบกำกับภาษี</p>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Invoice No.</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invoices as $inv): ?>
                                <tr>
                                    <td class="invoice-no"><?php echo htmlspecialchars($inv['invoice_no']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($inv['created_at'])); ?></td>
                                    <td>฿<?php echo number_format($inv['total'], 2); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $inv['status']; ?>">
                                            <?php echo strtoupper($inv['status']); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <a href="invoice_view.php?id=<?php echo $inv['id']; ?>" class="btn-view">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="api/invoices/download.php?id=<?php echo $inv['id']; ?>" class="btn-view">
                                            <i class="fas fa-download"></i> PDF
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <?php renderComponent('footer/footer'); ?>
    <script src="src/assets/js/notification/notificationService.js?v=1.1"></script>
    <script src="src/assets/js/notification/notificationUI.js?v=1.1"></script>
    <script src="src/assets/js/script.js?v=1.1"></script>
</body>
</html>
