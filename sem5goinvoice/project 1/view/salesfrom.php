<?php
$page_title = 'Create Sales Invoice';
$additional_css = ['main.css'];
include '../includes/header.php';

if (!$is_logged_in) {
    header('Location: ../front/login.html');
    exit;
}
?>

<!-- Header -->
<div id="header">
  <h3>✏️ Create Sale Invoice</h3>
  <button id="create-invoice-btn">+ Create Another Invoice</button>
</div>

<!-- Layout Wrapper -->
<div class="layout">
  <!-- Customer Information -->
  <div class="card">
    <div class="card-header">
      <h3>Customer Information</h3>
      <button>+ Add Customer</button>
    </div>

    <div class="form-row">
      <label for="ms">M/S.<span style="color:red">*</span></label>
      <input type="text" id="ms" placeholder="M/S.*">
    </div>

    <div class="form-row">
      <label for="address">Address</label>
      <textarea id="address" placeholder="Address"></textarea>
    </div>

    <div class="form-row">
      <label for="contact">Contact Person</label>
      <input type="text" id="contact" placeholder="Contact Person">
    </div>

    <div class="form-row">
      <label for="phone">Phone No</label>
      <input type="text" id="phone" placeholder="Phone No">
    </div>

    <div class="form-row">
      <label for="gstin">GSTIN / PAN</label>
      <input type="text" id="gstin" placeholder="GSTIN / PAN">
    </div>

    <div class="form-row">
      <label for="rev">Rev. Charge</label>
      <select id="rev">
        <option>No</option>
        <option>Yes</option>
      </select>
    </div>

    <div class="form-check">
      <input type="checkbox" id="shipping" checked>
      <label for="shipping">Use Same Shipping Address</label>
    </div>

    <div class="form-row">
      <label for="supply">Place of Supply<span style="color:red">*</span></label>
      <input type="text" id="supply">
    </div>
  </div>

  <!-- Invoice Detail -->
  <div class="info-card" id="invoice-detail-card">
    <div class="info-card-header">
      <h3>Invoice Detail</h3>
    </div>
    <form class="info-grid">
      <label>Invoice Type</label>
      <select><option>Regular</option></select>

      <label>Invoice Prefix</label>
      <input type="text" placeholder="Inv Pre." />

      <label>Invoice Number</label>
      <input type="text" placeholder="1" />

      <label>Invoice Postfix</label>
      <input type="text" placeholder="Inv Post." />

      <label>Date<span style="color:red">*</span></label>
      <input type="text" placeholder="Date*" value="03-Aug-2025" />

      <label>Challan No.</label>
      <input type="text" placeholder="Challan No." />

      <label>Challan Date</label>
      <input type="text" placeholder="dd/mm/yy" />

      <label>P.O. No.</label>
      <input type="text" placeholder="P.O. No." />

      <label>P.O. Date</label>
      <input type="text" placeholder="dd/mm/yy" />

      <label>L.R. No.</label>
      <input type="text" placeholder="L.R. No." />

      <label>E-Way No.</label>
      <input type="text" placeholder="E-Way No." />

      <label>Delivery Mode</label>
      <select><option>Select Delivery Mode</option></select>
    </form>
  </div>
</div>

<div class="top-buttons">
  <h3>Products Items</h3>
  <button id="btnAddItem">+ Add Product</button>
  <button>+ Add Additional Charges</button>
</div>
<table id="itemsTable">
  <thead>
    <tr>
      <th>SR.</th>
      <th>PRODUCT / OTHER CHARGES</th>
      <th>HSN/SAC CODE</th>
      <th>QTY. / STOCK</th>
      <th>UOM</th>
      <th>PRICE (RS)</th>
      <th>DISCOUNT</th>
      <th>IGST</th>
      <th>CESS</th>
      <th>TOTAL</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>1</td>
      <td>
        <select class="product-select"><option value="">Select product...</option></select><br>
        <input type="number" class="product-id" placeholder="Product ID (DB)">
        <textarea placeholder="Item Note..."></textarea>
      </td>
      <td><input class="hsn-input" placeholder="HSN/SAC"></td>
      <td><input class="qty-input" placeholder="Qty."></td>
      <td><input placeholder="UOM"></td>
      <td><input class="price-input" placeholder="Price"></td>
      <td><input class="discount-input" value="0"></td>
      <td><input class="gst-input" value="18"></td>
      <td><input class="cess-input" value="0.00"></td>
      <td><input class="line-total" placeholder="Total" readonly></td>
    </tr>
  </tbody>
</table>

<!-- Minimal DB fields to save invoice -->
<div class="save-meta" id="saveMeta" style="margin: 20px 0;">
  <h4 style="margin-bottom:10px;">Save Details</h4>
  <div class="form-row" style="max-width:420px;">
    <label for="customer_select">Customer<span style="color:red">*</span></label>
    <select id="customer_select"><option value="">Loading...</option></select>
    <input type="hidden" id="customer_id" />
  </div>
  <div class="form-row" style="max-width:420px;">
    <label for="invoice_date">Invoice Date<span style="color:red">*</span></label>
    <input type="date" id="invoice_date">
  </div>
  <div class="form-row" style="max-width:420px;">
    <label for="due_date">Due Date</label>
    <input type="date" id="due_date">
  </div>
  <div class="form-row" style="max-width:420px;">
    <label for="payment_method">Payment Method</label>
    <select id="payment_method">
      <option value="cash">Cash</option>
      <option value="credit">Credit</option>
      <option value="cheque">Cheque</option>
      <option value="online">Online</option>
    </select>
  </div>
  <div class="form-row" style="max-width:600px;">
    <label for="notes">Notes</label>
    <textarea id="notes" placeholder="Notes (optional)"></textarea>
  </div>
</div>

<div class="footer-buttons">
  <button class="btn-back"><a href="salesinvoice.php">&lt; Back</a></button>
  <div>
    <button class="btn-discard"><a href="">Discard</a></button>
    <button class="btn-save-print"><a href="">Save & Print</a></button>
    <button id="btnSaveSales" type="button" class="btn-save">Save</button>
  </div>
</div>

<script>
(function(){
  // Set today's date as default for invoice_date
  var invDateEl = document.getElementById('invoice_date');
  if (invDateEl && !invDateEl.value) {
    var today = new Date();
    var yyyy = today.getFullYear();
    var mm = String(today.getMonth()+1).padStart(2,'0');
    var dd = String(today.getDate()).padStart(2,'0');
    invDateEl.value = yyyy + '-' + mm + '-' + dd;
  }

  // Load customers into dropdown and keep hidden customer_id in sync
  async function loadCustomers(){
    try{
      var sel = document.getElementById('customer_select');
      var hid = document.getElementById('customer_id');
      if(!sel) return;
      sel.innerHTML = '<option value="">Loading...</option>';
      var isLiveServer = /^(127\.0\.0\.1|localhost):55\d{2}$/.test(window.location.host);
      var apiUrl = isLiveServer
        ? 'http://localhost/sem5goinvoice/project%201/api/customers.php?type=customer'
        : '../api/customers.php?type=customer';
      var res = await fetch(apiUrl, { credentials: 'include' });
      var raw = await res.text();
      var data = {};
      try{ data = JSON.parse(raw); }catch(e){ throw new Error('Non-JSON from customers API'); }
      if(!res.ok || data.success !== true){
        var msg = (data && (data.error||data.message)) || 'Failed to load customers';
        if(/Authentication required/i.test(msg)){
          var loginUrl = isLiveServer
            ? 'http://localhost/sem5goinvoice/project%201/front/login.html'
            : '../front/login.html';
          alert('Please login to continue. Redirecting to login...');
          window.location.href = loginUrl;
          return;
        }
        throw new Error(msg);
      }
      var list = (data.data && data.data.customers) ? data.data.customers : [];
      sel.innerHTML = '<option value="">Select customer</option>' + list.map(function(c){
        var name = (c.company_name && c.company_name.trim().length>0) ? c.company_name : c.name;
        return '<option value="'+ c.id +'">'+ name +' (ID:'+ c.id +')</option>';
      }).join('');
      sel.addEventListener('change', function(){ if(hid) hid.value = sel.value; });
      // If only one customer, preselect
      if(list.length === 1){ sel.value = String(list[0].id); if(hid) hid.value = sel.value; }
    }catch(err){
      var sel = document.getElementById('customer_select');
      if(sel){ sel.innerHTML = '<option value="">Load failed</option>'; }
    }
  }
  loadCustomers();

  function calcRowTotal(tr){
    var qty = parseFloat((tr.querySelector('.qty-input')||{}).value || '0');
    var price = parseFloat((tr.querySelector('.price-input')||{}).value || '0');
    var discount = parseFloat((tr.querySelector('.discount-input')||{}).value || '0');
    var gst = parseFloat((tr.querySelector('.gst-input')||{}).value || '0');
    var cess = parseFloat((tr.querySelector('.cess-input')||{}).value || '0');
    var subtotal = qty * price;
    var taxable = Math.max(0, subtotal - (isNaN(discount) ? 0 : discount));
    var total = taxable + (taxable * (gst/100)) + (isNaN(cess) ? 0 : cess);
    var totalEl = tr.querySelector('.line-total');
    if(totalEl) totalEl.value = total.toFixed(2);
  }

  // Products dropdown population (same behavior as purchase form)
  var productMap = {};
  async function loadProducts(){
    try{
      var data;
      if (window.apiHandler && typeof window.apiHandler.getProducts === 'function'){
        data = await window.apiHandler.getProducts();
      } else {
        var isLiveServer = /^(127\.0\.0\.1|localhost):55\d{2}$/.test(window.location.host);
        var apiUrl = isLiveServer
          ? 'http://localhost/sem5goinvoice/project%201/api/products.php'
          : '../api/products.php';
        var res = await fetch(apiUrl, { credentials: 'include' });
        data = await res.json();
      }
      if(!data || data.success !== true){ throw new Error((data && (data.error||data.message)) || 'Failed to load products'); }
      var list = (data.data && data.data.products) ? data.data.products : [];
      productMap = {}; list.forEach(function(p){ productMap[String(p.id)] = p; });
      var optionsHtml = ['<option value="">Select product...</option>']
        .concat(list.map(function(p){ return '<option value="'+p.id+'">'+ p.name +' (ID:'+ p.id +')</option>'; }))
        .join('');
      document.querySelectorAll('select.product-select').forEach(function(sel){
        sel.innerHTML = optionsHtml; sel.value = '';
        bindProductSelect(sel);
      });
    }catch(err){
      console.error('Products load failed:', err);
      document.querySelectorAll('select.product-select').forEach(function(sel){ sel.innerHTML = '<option value="">Load failed</option>'; });
    }
  }

  function bindProductSelect(sel){
    if(!sel) return;
    sel.addEventListener('change', function(){
      var tr = sel.closest('tr');
      var pid = sel.value || '';
      var p = productMap[pid];
      var idEl = tr && tr.querySelector('.product-id'); if(idEl) idEl.value = pid;
      if(p){
        var hsnEl = tr.querySelector('.hsn-input'); if(hsnEl) hsnEl.value = p.hsn_sac || '';
        var priceEl = tr.querySelector('.price-input'); if(priceEl) priceEl.value = (p.sale_price ?? p.price) || '';
        var gstEl = tr.querySelector('.gst-input'); if(gstEl) gstEl.value = (p.gst_rate != null ? p.gst_rate : '');
      }
      calcRowTotal(tr);
    }, { once: false });
  }

  function wireRow(tr){
    ['.qty-input','.price-input','.discount-input','.gst-input','.cess-input'].forEach(sel => {
      var el = tr.querySelector(sel);
      if(el){ el.addEventListener('input', function(){ calcRowTotal(tr); }); }
    });
    calcRowTotal(tr);
  }

  function addRow(){
    var tbody = document.querySelector('#itemsTable tbody');
    if(!tbody) return;
    var rows = tbody.querySelectorAll('tr');
    var tmpl = rows[0];
    var clone = tmpl.cloneNode(true);
    // clear inputs
    Array.from(clone.querySelectorAll('input, textarea')).forEach(function(inp){
      if(inp.classList.contains('gst-input')) { /* keep default 18 */ }
      else if(inp.classList.contains('cess-input')) { inp.value = '0.00'; }
      else if(inp.classList.contains('line-total')) { inp.value = ''; }
      else { inp.value = ''; }
    });
    // SR number
    var sr = clone.querySelector('td:first-child');
    if(sr){ sr.textContent = String(rows.length + 1); }
    // Reset product select and bind
    var psel = clone.querySelector('select.product-select');
    if(psel){
      if(Object.keys(productMap).length > 0){
        var list = Object.values(productMap);
        var optionsHtml = ['<option value="">Select product...</option>']
          .concat(list.map(function(p){ return '<option value="'+p.id+'">'+ p.name +' (ID:'+ p.id +')</option>'; }))
          .join('');
        psel.innerHTML = optionsHtml; psel.value = '';
      } else {
        psel.innerHTML = '<option value="">Select product...</option>';
      }
      bindProductSelect(psel);
    }
    tbody.appendChild(clone);
    wireRow(clone);
  }

  // Wire initial row and add-item button
  var firstRow = document.querySelector('#itemsTable tbody tr');
  if(firstRow){
    wireRow(firstRow);
    var firstSelect = firstRow.querySelector('select.product-select');
    if(firstSelect) bindProductSelect(firstSelect);
  }
  var addBtn = document.getElementById('btnAddItem');
  if(addBtn) addBtn.addEventListener('click', function(e){ e.preventDefault(); addRow(); });

  // Load products after DOM ready
  loadProducts();

  // Save handler
  var btn = document.getElementById('btnSaveSales');
  if(!btn) return;
  btn.addEventListener('click', async function(){
    try{
      var customerId = parseInt((document.getElementById('customer_id')||{}).value || (document.getElementById('customer_select')||{}).value || '');
      var invoiceDate = (document.getElementById('invoice_date')||{}).value;
      var dueDate = (document.getElementById('due_date')||{}).value || null;
      var paymentMethod = (document.getElementById('payment_method')||{}).value || 'cash';
      var notes = (document.getElementById('notes')||{}).value || '';

      if(!customerId || !invoiceDate){
        alert('Please enter required fields: Customer ID and Invoice Date');
        return;
      }

      var items = [];
      var rows = document.querySelectorAll('#itemsTable tbody tr');
      rows.forEach(function(tr){
        var productId = parseInt((tr.querySelector('.product-id')||{}).value || '');
        var qty = parseInt((tr.querySelector('.qty-input')||{}).value || '0');
        var price = parseFloat((tr.querySelector('.price-input')||{}).value || '0');
        var gstRate = parseFloat((tr.querySelector('.gst-input')||{}).value || '0');
        if(qty>0 && price>0){ // allow missing product id similar to purchase
          items.push({product_id: (isNaN(productId)? null : productId), quantity: qty, unit_price: price, gst_rate: gstRate});
        }
      });

      if(items.length === 0){
        alert('Please enter at least one valid item with Qty and Price.');
        return;
      }

      var payload = {
        customer_id: customerId,
        invoice_date: invoiceDate,
        due_date: dueDate,
        payment_method: paymentMethod,
        notes: notes,
        items: items
      };

      var isLiveServer = /^(127\.0\.0\.1|localhost):55\d{2}$/.test(window.location.host);
      var postUrl = isLiveServer
        ? 'http://localhost/sem5goinvoice/project%201/api/invoices.php?type=sales'
        : '../api/invoices.php?type=sales';
      var res = await fetch(postUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(payload),
        credentials: 'include'
      });
      var raw = await res.text();
      let data;
      try { data = JSON.parse(raw); }
      catch(e){ throw new Error("Server returned non-JSON: " + raw.slice(0,200)); }
      if(!res.ok || data.success !== true){
        var msg = (data && (data.error||data.message)) || 'Failed to save invoice';
        if(/Authentication required/i.test(msg) || res.status === 401){
          var loginUrl = isLiveServer
            ? 'http://localhost/sem5goinvoice/project%201/front/login.html'
            : '../front/login.html';
          alert('Please login to save. Redirecting to login...');
          window.location.href = loginUrl;
          return;
        }
        throw new Error(msg);
      }
      alert('Invoice saved: #' + (data.data && data.data.invoice_number ? data.data.invoice_number : ''));
      window.location.href = 'salesinvoice.php';
    }catch(err){
      alert('Error: ' + err.message);
    }
  });
})();
</script>

<?php include '../includes/footer.php'; ?>
