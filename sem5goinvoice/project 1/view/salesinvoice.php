<?php
$page_title = 'Sales Invoice - GoInvoice';
$additional_css = ['main.css'];
include '../includes/header.php';

if (!$is_logged_in) {
    header('Location: ../front/login.html');
    exit;
}
?>
<div class="section1">
  <div class="title">Sales Invoice</div>
  <div class="button-group">
    <button class="btn light">
      <span class="invoice-icon"><i class="fa-solid fa-magnifying-glass"></i></span> Search
    </button>
    <button class="btn green">
      <a href="salesfrom.php"><span class="invoice-icon"><i class="fa-solid fa-plus color: white"></i></span> Add New</a>
    </button>
  </div>
</div>

<div class="invoice-table">
  <table>
    <thead>
      <tr>
        <th><input type="checkbox"></th>
        <th>Invoice No ⯆</th>
        <th>Company Name</th>
        <th>Date</th>
        <th>Total</th>
        <th>Action </th>
      </tr>
    </thead>
    <tbody id="salesBody">
      <tr><td colspan="6">Loading...</td></tr>
    </tbody>
  </table>
</div>

<script>
(function(){
  async function loadSales(){
    var body = document.getElementById('salesBody'); if(!body) return;
    try{
      var isLiveServer = /^(127\.0\.0\.1|localhost):55\d{2}$/.test(window.location.host);
      var apiUrl = isLiveServer
        ? 'http://localhost/sem5goinvoice/project%201/api/invoices.php?type=sales&limit=50'
        : '../api/invoices.php?type=sales&limit=50';
      var res = await fetch(apiUrl, { credentials: 'include' });
      var raw = await res.text(); var data={};
      try{ data = JSON.parse(raw);}catch(e){ throw new Error('Non-JSON from invoices API'); }
      if(!res.ok || data.success !== true){
        var msg = (data && (data.error||data.message)) || 'Failed to load invoices';
        if(/Authentication required/i.test(msg) || res.status === 401){
          var loginUrl = isLiveServer ? 'http://localhost/sem5goinvoice/project%201/front/login.html' : '../front/login.html';
          alert('Please login to view sales. Redirecting to login...'); window.location.href = loginUrl; return;
        }
        throw new Error(msg);
      }
      var list = (data.data && data.data.invoices) ? data.data.invoices : [];
      if(list.length === 0){ body.innerHTML = '<tr><td colspan="6">No sales invoices yet</td></tr>'; return; }
      body.innerHTML = list.map(function(r){
        var id = r.id; var inv = r.invoice_number || ''; var name = r.customer_name || ''; var d = r.invoice_date || ''; var total = (Number(r.total_amount||0)).toFixed(2);
        var link = 'print_sales_invoice.php?id='+ encodeURIComponent(id);
        return '<tr>'+
          '<td><input type="checkbox"></td>'+
          '<td>'+ inv +'</td>'+
          '<td>'+ name +'</td>'+
          '<td>'+ d +'</td>'+
          '<td>'+ total +'</td>'+
          '<td><a class="btn view" href="'+link+'" target="_blank"><span><i class="fa-regular fa-eye"></i></span> View / Print</a></td>'+
        '</tr>';
      }).join('');
    }catch(err){ body.innerHTML = '<tr><td colspan="6">Load failed</td></tr>'; }
  }
  loadSales();
})();
</script>

<?php include '../includes/footer.php'; ?>
