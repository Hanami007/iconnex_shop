<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/tax_service.php';

class InvoiceService {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Generate a new unique invoice number
     * Format: INV-YYYY-000001
     */
    public function generateInvoiceNumber() {
        $year = date('Y');
        $prefix = "INV-$year-";
        
        $stmt = $this->pdo->prepare("SELECT invoice_no FROM invoices WHERE invoice_no LIKE ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$prefix . '%']);
        $lastInvoice = $stmt->fetch();

        if ($lastInvoice) {
            $lastNum = (int)substr($lastInvoice['invoice_no'], strlen($prefix));
            $newNum = str_pad($lastNum + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $newNum = '000001';
        }

        return $prefix . $newNum;
    }

    /**
     * Create an invoice for an order
     */
    public function createInvoice($orderData, $customerData) {
        try {
            $this->pdo->beginTransaction();

            // Check if invoice already exists for this order
            $stmt = $this->pdo->prepare("SELECT id FROM invoices WHERE order_id = ?");
            $stmt->execute([$orderData['id']]);
            if ($stmt->fetch()) {
                throw new Exception("Invoice already exists for this order.");
            }

            $invoiceNo = $this->generateInvoiceNumber();
            $taxInfo = TaxService::calculateFromTotal($orderData['total_amount']);

            $stmt = $this->pdo->prepare("
                INSERT INTO invoices (
                    invoice_no, user_id, order_id, subtotal, vat_amount, total, 
                    customer_name, customer_tax_id, customer_address
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $invoiceNo,
                $orderData['user_id'],
                $orderData['id'],
                $taxInfo['subtotal'],
                $taxInfo['vat_amount'],
                $taxInfo['total'],
                $customerData['name'],
                $customerData['tax_id'] ?? null,
                $customerData['address']
            ]);

            $invoiceId = $this->pdo->lastInsertId();

            // Insert items
            $stmtItem = $this->pdo->prepare("
                INSERT INTO invoice_items (
                    invoice_id, course_id, course_name, qty, unit_price, total
                ) VALUES (?, ?, ?, ?, ?, ?)
            ");

            foreach ($orderData['items'] as $item) {
                // In Thai tax laws, if the price includes VAT, we usually show the unit price before VAT in the items list,
                // but many systems show the inclusive price and separate VAT at the bottom.
                // We'll follow the requirement: Course name, Category, Qty, Unit price, Total amount.
                $stmtItem->execute([
                    $invoiceId,
                    $item['course_id'],
                    $item['course_name'],
                    $item['qty'] ?? 1,
                    $item['unit_price'],
                    $item['total_price']
                ]);
            }

            $this->pdo->commit();
            return $invoiceId;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getInvoice($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM invoices WHERE id = ?");
        $stmt->execute([$id]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($invoice) {
            $stmtItems = $this->pdo->prepare("SELECT * FROM invoice_items WHERE invoice_id = ?");
            $stmtItems->execute([$id]);
            $invoice['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
        }

        return $invoice;
    }

    public function getInvoiceByOrder($orderId) {
        $stmt = $this->pdo->prepare("SELECT id FROM invoices WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $result = $stmt->fetch();
        return $result ? $this->getInvoice($result['id']) : null;
    }

    public function getUserInvoices($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM invoices WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
