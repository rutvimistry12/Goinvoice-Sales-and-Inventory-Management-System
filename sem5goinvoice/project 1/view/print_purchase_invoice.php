<?php
// Printable Purchase Invoice
require_once '../config/database.php';
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../front/login.html'); exit; }
$user_id = (int)$_SESSION['user_id'];
$invoice_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($invoice_id <= 0) { http_response_code(400); echo 'Missing invoice id'; exit; }

// Fetch purchase invoice, vendor, items, user profile (logo)
$stmt = $pdo->prepare("SELECT pi.*, v.name AS vendor_name, v.address AS vendor_address, v.gst_number AS vendor_gst, v.mobile AS vendor_phone
                       FROM purchase_invoices pi INNER JOIN customers v ON v.id = pi.vendor_id
                       WHERE pi.id = ? AND pi.user_id = ? LIMIT 1");
$stmt->execute([$invoice_id, $user_id]);
$inv = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$inv) { http_response_code(404); echo 'Purchase invoice not found'; exit; }

$stmt = $pdo->prepare("SELECT pii.*, p.name AS product_name, p.hsn_sac AS hsn
                       FROM purchase_invoice_items pii LEFT JOIN products p ON p.id = pii.product_id
                       WHERE pii.invoice_id = ? ORDER BY pii.id ASC");
$stmt->execute([$invoice_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$profile = ['logo_path'=>null];
try{
  $ps = $pdo->prepare("SELECT logo_path FROM user_profiles WHERE user_id = ? LIMIT 1");
  $ps->execute([$user_id]);
  $row = $ps->fetch(PDO::FETCH_ASSOC);
  if ($row) $profile = $row;
}catch(Exception $e){}
$logo = $profile['logo_path'] ?: '../images/invoicelogo.png';

function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Purchase Invoice #<?php echo e($inv['invoice_number']); ?></title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; font-family: Arial, sans-serif; font-size:10px; line-height:1.2; }
    body { background:#f0f2f5; display:flex; justify-content:center; align-items:center; min-height:100vh; padding:10px; }
    .invoice-container { width: 800px; background:#fff; padding:15px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
    .header { display:flex; justify-content:space-between; margin-bottom:10px; padding-bottom:8px; border-bottom:1px solid #000; }
    .company-info { flex:2; }
    .logo-container { flex:1; text-align:right; }
    .logo-img { height:60px; width:auto; object-fit:contain; }
    h1 { color:#000; margin-bottom:4px; font-size:14px; }
    h2 { margin:8px 0 4px; color:#000; font-size:12px; border-bottom:1px solid #eee; padding-bottom:2px; }
    .invoice-title { text-align:center; margin:10px 0; font-size:14px; font-weight:bold; text-decoration:underline; }
    .form-row { display:flex; gap:8px; margin-bottom:6px; }
    .form-group { flex:1; }
    label { display:block; margin-bottom:2px; font-weight:bold; color:#333; font-size:9px; }
    input, textarea { width:100%; padding:4px; border:1px solid #ddd; border-radius:2px; font-size:9px; }
    textarea { height:35px; resize:vertical; }
    .customer-details { background:#f9f9f9; padding:8px; margin-bottom:8px; border-left:2px solid #000; }
    table { width:100%; border-collapse:collapse; margin:8px 0; font-size:9px; }
    th, td { border:1px solid #ddd; padding:4px; text-align:left; }
    th { background:#f2f2f2; color:#000; font-weight:bold; }
    .summary-table { width:100%; font-size:9px; }
    .summary-table td { border:none; padding:3px; }
    .bank-details { background:#f9f9f9; padding:8px; margin:8px 0; }
    .terms { margin-top:10px; font-size:9px; color:#333; }
    .btn { background:#4a6cf7; color:#fff; border:none; padding:6px 10px; border-radius:2px; cursor:pointer; font-size:9px; margin-top:8px; }
    .btn-print { background:#28a745; } .btn-download { background:#ff5722; } .btn-reset { background:#6c757d; }
    .action-buttons { display:flex; gap:8px; justify-content:flex-end; margin-top:10px; }
    .total-in-words { background:#f9f9f9; padding:6px; margin:8px 0; border-left:2px solid #000; font-size:9px; }
    @media print { body{background:#fff;padding:0;} .invoice-container{box-shadow:none;padding:10px;width:100%;margin:0;} .no-print{display:none;} }
  </style>
</head>
<body>
  <div class="invoice-container" id="invoice">
    <div class="header">
      <div class="company-info">
        <h1><?php echo e($_SESSION['business_name'] ?? 'Business Name'); ?></h1>
        <div class="form-row">
          <div class="form-group">
            <label>Address</label>
            <textarea><?php echo e($_SESSION['address'] ?? ''); ?></textarea>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Phone</label>
            <input value="<?php echo e($_SESSION['mobile'] ?? ''); ?>" />
          </div>
          <div class="form-group">
            <label>GSTIN</label>
            <input value="<?php echo e($_SESSION['gst_number'] ?? ''); ?>" />
          </div>
        </div>
      </div>
      <div class="logo-container">
        <img class="logo-img" src="<?php echo e($logo); ?>" alt="Logo" onerror="this.style.display='none'">
      </div>
    </div>

    <div class="invoice-title">PURCHASE INVOICE</div>

    <div class="customer-details">
      <h2>Vendor Details</h2>
      <div class="form-row">
        <div class="form-group">
          <label>M/S</label>
          <input value="<?php echo e($inv['vendor_name']); ?>">
        </div>
        <div class="form-group">
          <label>GSTIN</label>
          <input value="<?php echo e($inv['vendor_gst'] ?? ''); ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Address</label>
          <textarea><?php echo e($inv['vendor_address'] ?? ''); ?></textarea>
        </div>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Invoice No.</label>
        <input value="<?php echo e($inv['invoice_number']); ?>">
      </div>
      <div class="form-group">
        <label>Invoice Date</label>
        <input value="<?php echo e($inv['invoice_date']); ?>">
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th>St. No.</th>
          <th>Name of Product / Service</th>
          <th>HSM / SAC</th>
          <th>Qty</th>
          <th>Rate</th>
          <th>Taxable Value</th>
          <th>CGST</th>
          <th>SGST</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <?php $i=1; $taxable=0; $gst_total=0; $grand=0; foreach($items as $it):
          $qty = (float)$it['quantity'];
          $rate = (float)$it['unit_price'];
          $line_taxable = $qty * $rate;
          $cgst = $sgst = ($line_taxable * ((float)$it['gst_rate']/100))/2;
          $line_total = $line_taxable + $cgst + $sgst;
          $taxable += $line_taxable; $gst_total += ($cgst+$sgst); $grand += $line_total;
        ?>
        <tr>
          <td><?php echo $i++; ?></td>
          <td><?php echo e($it['product_name'] ?? ''); ?></td>
          <td><?php echo e($it['hsn'] ?? ''); ?></td>
          <td><?php echo number_format($qty,2); ?></td>
          <td><?php echo number_format($rate,2); ?></td>
          <td><?php echo number_format($line_taxable,2); ?></td>
          <td><?php echo number_format($cgst,2); ?></td>
          <td><?php echo number_format($sgst,2); ?></td>
          <td><?php echo number_format($line_total,2); ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="total-in-words">
      <p><strong>Total in words:</strong> <span><?php echo strtoupper('RUPEES '.number_format($grand,2).' ONLY'); ?></span></p>
    </div>

    <div class="form-row">
      <div class="form-group">
        <div class="bank-details">
          <h2>Bank Details</h2>
          <div class="form-row"><div class="form-group"><label>Bank Name</label><input></div></div>
          <div class="form-row"><div class="form-group"><label>Branch</label><input></div></div>
          <div class="form-row"><div class="form-group"><label>Account Name</label><input></div></div>
          <div class="form-row">
            <div class="form-group"><label>Account Number</label><input></div>
            <div class="form-group"><label>IFSC Code</label><input></div>
          </div>
        </div>
      </div>
      <div class="form-group">
        <table class="summary-table">
          <tr><td>Taxable Amount</td><td><?php echo number_format($taxable,2); ?></td></tr>
          <tr><td>Add : CGST + SGST</td><td><?php echo number_format($gst_total,2); ?></td></tr>
          <tr><td><strong>Total Amount After Tax</strong></td><td><strong><?php echo number_format($grand,2); ?></strong></td></tr>
        </table>
      </div>
    </div>

    <div class="terms">
      <h2>Terms and Conditions</h2>
      <p>Subject to our home Jurisdiction.</p>
      <p>Our Responsibility Ceases as soon as goods leaves our Premises.</p>
      <p>Goods once sold will not taken back.</p>
      <p>Delivery Ex-Premises.</p>
      <p>Certified that the particulars given above are true and correct.</p>
    </div>

    <div class="action-buttons no-print">
      <button class="btn btn-print" onclick="window.print()">Print Invoice</button>
      <!-- <button class="btn btn-download" onclick="generatePDF()">Download as PDF</button> -->
    </div>
  </div>
  <script>
    function generatePDF(){
      const invoice = document.getElementById('invoice');
      const opt = { margin:[5,5,5,5], filename:'purchase-<?php echo e($inv['invoice_number']); ?>.pdf', image:{type:'jpeg',quality:0.98}, html2canvas:{scale:2, useCORS:true, width:800, windowWidth:800}, jsPDF:{unit:'mm', format:'a4', orientation:'portrait'} };
      html2pdf().set(opt).from(invoice).save();
    }
  </script>
</body>
</html>
