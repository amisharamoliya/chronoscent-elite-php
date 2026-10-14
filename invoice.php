<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'vendor/autoload.php';
require_once 'config/database.php';
require_once 'includes/auth.php';
require_login();

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_GET['id'])) {
    die("Invalid request");
}

$order_id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    die("Order not found or unauthorized.");
}

$stmt_items = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$stmt_items->execute([$order_id]);
$items = $stmt_items->fetchAll();

$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #INV-' . str_pad($order_id, 6, "0", STR_PAD_LEFT) . '</title>
    <style>
        body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; font-size: 14px; color: #333; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); }
        .details { width: 100%; margin-bottom: 30px; border-collapse: collapse; }
        .details td { padding: 5px; vertical-align: top; }
        .details .title { font-weight: bold; color: #666; margin-bottom: 5px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th { background: #eee; border-bottom: 1px solid #ddd; font-weight: bold; padding: 10px; text-align: left; }
        .items-table td { border-bottom: 1px solid #eee; padding: 10px; }
        .items-table .text-center { text-align: center; }
        .items-table .text-right { text-align: right; }
        .totals { width: 100%; border-collapse: collapse; }
        .totals td { padding: 8px 10px; text-align: right; }
        .totals .label { font-weight: bold; width: 80%; }
        .totals .value { width: 20%; }
        .totals .grand-total { font-size: 18px; font-weight: bold; border-top: 2px solid #333; }
        .text-success { color: #28a745; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table width="100%">
            <tr>
                <td>
                    <h1 style="color: #333; margin:0;">ChronoScent Elite</h1>
                </td>
                <td style="text-align: right;">
                    <h3 style="margin:0;">INVOICE</h3>
                    <p style="margin:5px 0 0 0; color:#666;">#INV-' . str_pad($order_id, 6, '0', STR_PAD_LEFT) . '</p>
                </td>
            </tr>
        </table>

        <br><br>

        <table class="details">
            <tr>
                <td width="50%">
                    <div class="title">Billed To:</div>
                    <strong>' . htmlspecialchars($order['name'] ?? '') . '</strong><br>
                    ' . htmlspecialchars($order['address'] ?? '') . '<br>
                    ' . htmlspecialchars(($order['city'] ?? '') . ', ' . ($order['state'] ?? '') . ' ' . ($order['pincode'] ?? '')) . '<br>
                    ' . htmlspecialchars($order['email'] ?? '') . '<br>
                    ' . htmlspecialchars($order['phone'] ?? '') . '
                </td>
                <td width="50%" style="text-align: right;">
                    <div class="title">Order Details:</div>
                    <strong>Order ID:</strong> ' . $order['id'] . '<br>
                    <strong>Date:</strong> ' . date('F j, Y', strtotime($order['created_at'])) . '<br>
                    <strong>Payment Method:</strong> ' . htmlspecialchars($order['payment_method'] ?? '') . '
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>';

foreach ($items as $item) {
    $html .= '
                <tr>
                    <td>' . htmlspecialchars($item['name'] ?? '') . '</td>
                    <td class="text-center">' . $item['quantity'] . '</td>
                    <td class="text-right">$' . number_format($item['price'], 2) . '</td>
                    <td class="text-right">$' . number_format($item['price'] * $item['quantity'], 2) . '</td>
                </tr>';
}

$html .= '
            </tbody>
        </table>

        <table class="totals">
            <tr>
                <td class="label">Subtotal:</td>
                <td class="value">$' . number_format(($order['total_amount'] ?? 0) + ($order['discount'] ?? 0), 2) . '</td>
            </tr>';

if (($order['discount'] ?? 0) > 0) {
    $html .= '
            <tr>
                <td class="label">Discount Saved:</td>
                <td class="value text-success">-$' . number_format($order['discount'], 2) . '</td>
            </tr>';
}

$html .= '
            <tr>
                <td class="label grand-total">Total:</td>
                <td class="value grand-total">$' . number_format($order['total_amount'] ?? 0, 2) . '</td>
            </tr>
        </table>
    </div>
</body>
</html>';

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('Invoice_INV-' . str_pad($order_id, 6, '0', STR_PAD_LEFT) . '.pdf', ['Attachment' => 1]);
