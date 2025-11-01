<?php
$page_title = 'Payment - GoInvoice';
$additional_css = ['main.css'];
include '../includes/header.php';

if (!$is_logged_in) {
    header('Location: ../front/login.html');
    exit;
}
?>
<div class="section1">
  <div class="title">Payment</div>
  <div class="button-group">
    <button class="btn green"><a href="paymentform.php"><span class="invoice-icon"><i class="fa-solid fa-plus color: white"></i></span> Add New</a></button>
  </div>
</div>

<div class="invoice-table">
  <table>
    <thead>
      <tr>
        <th><input type="checkbox"></th>
        <th>Receipt No ⯆</th>
        <th>Company Name</th>
        <th>Payment Date</th>
        <th>Payment Type</th>
        <th>Amount </th>
      </tr>
    </thead>
    <tbody id="paymentsBody">
      <tr><td colspan="6">Loading...</td></tr>
    </tbody>
  </table>
</div>

<script>
(function(){
  async function loadPayments(){
    var body = document.getElementById('paymentsBody');
    if(!body) return;
    try{
      var isLiveServer = /^(127\.0\.0\.1|localhost):55\d{2}$/.test(window.location.host);
      var apiUrl = isLiveServer
        ? 'http://localhost/sem5goinvoice/project%201/api/payments.php'
        : '../api/payments.php';
      var res = await fetch(apiUrl, { credentials: 'include' });
      var raw = await res.text(); var data={};
      try{ data = JSON.parse(raw);}catch(e){ throw new Error('Non-JSON from payments API'); }
      if(!res.ok || data.success !== true){
        var msg = (data && (data.error||data.message)) || 'Failed to load payments';
        if(/Authentication required/i.test(msg) || res.status === 401){
          var loginUrl = isLiveServer
            ? 'http://localhost/sem5goinvoice/project%201/front/login.html'
            : '../front/login.html';
          alert('Please login to view payments. Redirecting to login...');
          window.location.href = loginUrl; return;
        }
        throw new Error(msg);
      }
      var list = (data.data && data.data.payments) ? data.data.payments : [];
      if(list.length === 0){ body.innerHTML = '<tr><td colspan="6">No payments yet</td></tr>'; return; }
      body.innerHTML = list.map(function(p){
        var payDate = p.payment_date || '';
        var method = p.payment_method ? (p.payment_method.charAt(0).toUpperCase()+p.payment_method.slice(1)) : '';
        var amount = (Number(p.amount || 0)).toFixed(2);
        var company = p.customer_name || '';
        return '<tr>'+
          '<td><input type="checkbox"></td>'+
          '<td>'+ (p.id) +'</td>'+
          '<td>'+ company +'</td>'+
          '<td>'+ payDate +'</td>'+
          '<td>'+ method +'</td>'+
          '<td>'+ amount +'</td>'+
        '</tr>';
      }).join('');
    }catch(err){
      body.innerHTML = '<tr><td colspan="6">Load failed</td></tr>';
    }
  }
  loadPayments();
})();
</script>

<?php include '../includes/footer.php'; ?>
