<?php
$page_title = 'Create Payment';
$additional_css = ['main.css'];
include '../includes/header.php';

if (!$is_logged_in) {
    header('Location: ../front/login.html');
    exit;
}
?>

<div class="container-product" style="max-width:980px;margin:20px auto;">
  <div class="top-buttons" style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
    <h3 style="margin:0;">Create Inward Payment</h3>
    <div>
      <a class="btn btn-secondary" href="payment.php">Close</a>
      <button id="btnSavePayment" class="btn btn-primary">Save</button>
    </div>
  </div>

  <div class="card" style="padding:16px;margin-top:12px;">
    <div class="form-row" style="max-width:420px;">
      <label for="payment_customer">Customer<span style="color:red">*</span></label>
      <select id="payment_customer"><option value="">Loading...</option></select>
      <input type="hidden" id="payment_customer_id" />
    </div>

    <div class="form-row" style="max-width:420px;">
      <label for="payment_date">Payment Date<span style="color:red">*</span></label>
      <input type="date" id="payment_date">
    </div>

    <div class="form-row" style="max-width:420px;">
      <label for="payment_amount">Amount<span style="color:red">*</span></label>
      <input type="number" id="payment_amount" placeholder="Enter amount" step="0.01" min="0">
    </div>

    <div class="form-row" style="max-width:420px;">
      <label for="payment_method">Payment Method<span style="color:red">*</span></label>
      <select id="payment_method">
        <option value="cash">Cash</option>
        <option value="credit">Credit</option>
        <option value="cheque">Cheque</option>
        <option value="online">Online</option>
      </select>
    </div>

    <div class="form-row" style="max-width:420px;">
      <label for="reference_number">Reference No (optional)</label>
      <input type="text" id="reference_number" placeholder="Cheque/Txn/Ref No">
    </div>

    <div class="form-row" style="max-width:600px;">
      <label for="payment_notes">Payment Note</label>
      <textarea id="payment_notes" rows="3" placeholder="Enter payment note (optional)"></textarea>
    </div>
  </div>
</div>

<script>
(function(){
  // default today's date
  var dEl = document.getElementById('payment_date');
  if(dEl && !dEl.value){
    var d=new Date(), yyyy=d.getFullYear(), mm=('0'+(d.getMonth()+1)).slice(-2), dd=('0'+d.getDate()).slice(-2);
    dEl.value = yyyy+'-'+mm+'-'+dd;
  }

  // load customers
  async function loadCustomers(){
    try{
      var sel = document.getElementById('payment_customer');
      var hid = document.getElementById('payment_customer_id');
      if(!sel) return;
      sel.innerHTML = '<option value="">Loading...</option>';
      var isLiveServer = /^(127\.0\.0\.1|localhost):55\d{2}$/.test(window.location.host);
      var apiUrl = isLiveServer
        ? 'http://localhost/sem5goinvoice/project%201/api/customers.php?type=customer'
        : '../api/customers.php?type=customer';
      var res = await fetch(apiUrl, { credentials: 'include' });
      var raw = await res.text(); var data={};
      try{ data = JSON.parse(raw);}catch(e){ throw new Error('Non-JSON from customers API'); }
      if(!res.ok || data.success !== true){
        var msg = (data && (data.error||data.message)) || 'Failed to load customers';
        if(/Authentication required/i.test(msg) || res.status === 401){
          var loginUrl = isLiveServer
            ? 'http://localhost/sem5goinvoice/project%201/front/login.html'
            : '../front/login.html';
          alert('Please login to continue. Redirecting to login...');
          window.location.href = loginUrl; return;
        }
        throw new Error(msg);
      }
      var list = (data.data && data.data.customers) ? data.data.customers : [];
      sel.innerHTML = '<option value="">Select customer</option>' + list.map(function(c){
        var name = (c.company_name && c.company_name.trim().length>0) ? c.company_name : c.name;
        return '<option value="'+ c.id +'">'+ name +' (ID:'+ c.id +')</option>';
      }).join('');
      sel.addEventListener('change', function(){ if(hid) hid.value = sel.value; });
      if(list.length === 1){ sel.value = String(list[0].id); if(hid) hid.value = sel.value; }
    }catch(err){
      var sel = document.getElementById('payment_customer'); if(sel){ sel.innerHTML = '<option value="">Load failed</option>'; }
    }
  }
  loadCustomers();

  // save payment
  var saveBtn = document.getElementById('btnSavePayment');
  if(saveBtn){
    saveBtn.addEventListener('click', async function(){
      try{
        var customerId = parseInt((document.getElementById('payment_customer_id')||{}).value || (document.getElementById('payment_customer')||{}).value || '');
        var paymentDate = (document.getElementById('payment_date')||{}).value;
        var amount = parseFloat((document.getElementById('payment_amount')||{}).value || '0');
        var method = (document.getElementById('payment_method')||{}).value || 'cash';
        var reference = (document.getElementById('reference_number')||{}).value || '';
        var notes = (document.getElementById('payment_notes')||{}).value || '';

        if(!customerId){ alert('Please select a customer.'); return; }
        if(!paymentDate){ alert('Please select payment date.'); return; }
        if(!(amount>0)){ alert('Please enter amount > 0'); return; }

        var payload = {
          customer_id: customerId,
          payment_date: paymentDate,
          amount: amount,
          payment_method: method,
          reference_number: reference,
          notes: notes
        };

        var isLiveServer = /^(127\.0\.0\.1|localhost):55\d{2}$/.test(window.location.host);
        var apiUrl = isLiveServer
          ? 'http://localhost/sem5goinvoice/project%201/api/payments.php'
          : '../api/payments.php';

        var res = await fetch(apiUrl, {
          method: 'POST', headers: {'Content-Type': 'application/json'}, credentials: 'include',
          body: JSON.stringify(payload)
        });
        var raw = await res.text(); var data={}; try{ data = JSON.parse(raw);}catch(e){ throw new Error('Server returned non-JSON: '+ raw.slice(0,200)); }
        if(!res.ok || data.success !== true){
          var msg = (data && (data.error||data.message)) || 'Failed to save payment';
          if(/Authentication required/i.test(msg) || res.status === 401){
            var loginUrl = isLiveServer
              ? 'http://localhost/sem5goinvoice/project%201/front/login.html'
              : '../front/login.html';
            alert('Please login to save. Redirecting to login...');
            window.location.href = loginUrl; return;
          }
          throw new Error(msg);
        }
        alert('Payment saved');
        window.location.href = 'payment.php';
      }catch(err){ alert('Error: '+ err.message); }
    });
  }
})();
</script>

<?php include '../includes/footer.php'; ?>
