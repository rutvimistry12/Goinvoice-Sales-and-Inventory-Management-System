<?php
$page_title = 'Create Purchase Invoice';
$additional_css = ['main.css'];
include '../includes/header.php';

if (!$is_logged_in) {
    header('Location: ../front/login.html');
    exit;
}
?>

<div id="header">
  <h3>✏️ Create Purchase Invoice</h3>
  <button id="create-invoice-btn">+ Create Another Invoice</button>
</div>

<div class="layout">
  <div class="card">
    <div class="card-header">
      <h3>Vendor Information</h3>
      <button>+ Add Vendor</button>
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

  <div class="info-card" id="invoice-detail-card">
    <div class="info-card-header">
      <h3>Purchase Invoice Detail</h3>
    </div>
    <form class="info-grid">
      <label>Purchase Invoice Type</label>
      <select><option>Regular</option></select>

      <label>Invoice Number</label>
      <input type="text" placeholder="1" />

      <label>Date<span style="color:red">*</span></label>
      <input type="text" placeholder="Date*" value="03-Aug-2025" />

      <label>Challan No.</label>
      <input type="text" placeholder="Challan No." />

      <label>Challan Date</label>
      <input type="text" placeholder="dd/mm/yy" />

      <label>L.R. No.</label>
      <input type="text" placeholder="L.R. No." />

      <label>E-Way No.</label>
      <input type="text" placeholder="E-Way No." />

      <label>Enter Date</label>
      <input type="text" placeholder="dd/mm/yy" />

      <label>Delivery Mode</label>
    </form>
  </div>
</div>

<div class="container-product">
  <div class="top-buttons">
    <h3>Products Items</h3>
    <button id="btnAddItemPurchase">+ Add Product</button>
    <button>+ Add Additional Charges</button>
  </div>
  <table id="itemsTablePurchase">
    <thead>
      <tr>
        <th>SR.</th>
        <th>PRODUCT / OTHER CHARGES</th>
        <th>HSN/SAC CODE</th>
        <th>PRODUCT ID (DB)</th>
        <th>QTY.</th>
        <th>UOM</th>
        <th>PRICE (RS)</th>
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
        <td><input class="cess-input" value="0"></td>
        <td>
          <div style="display:flex;gap:6px;align-items:center;">
            <input class="line-total" placeholder="Total" readonly>
            <button type="button" class="btn-calc-row" style="padding:4px 8px;">Calc</button>
          </div>
        </td>
        </tr>
      <tr class="highlight-row">
        <td colspan="3">Total Inv. Val</td>
        <td>0</td>
        <td></td>
        <td>0</td>
        <td>0</td>
        <td>0</td>
        <td>0</td>
        <td>0</td>
      </tr>
    </tbody>
  </table>

  <!--div class="section">
    <div class="left">
      <div class="the-row">
        <label>Due Date</label>
        <input type="text" placeholder="18-Aug-2025">
        </br>
      </div>
      <label>Title</label>
      <input type="text" value="Terms and Conditions">

      <label>Detail</label>
      <textarea rows="4">Subject to our home Jurisdiction.
Our Responsibility Ceases as soon as goods leaves our Premises.</textarea>

      <button class="notes-button">+ Add Notes</button>

      <label>Document Note / Remarks</label>
      <textarea></textarea>
      <small><em>Not Visible on Print</em></small>
    </div>

    <div class="right">
      <div class="the-row">
        <label>Total Taxable</label>
        <input type="text" value="0">
      </div>
      <div class="the-row">
        <label>Total Tax</label>
        <input type="text" value="0"> 
      </div>

      <div class="toggle-group">
        <label>TCS</label>
        <span>Rs</span>
        <span>%</span>
        <button>-</button>
        <button>+</button>
        <div class="input-group">
          <input type="text" value="0">
        </div>
      </div>

      <div class="toggle-group">
        <label>Discount</label>
        <span>Rs</span>
        <span>%</span>
        <button>-</button>
        <button>+</button>
        <input type="text" value="0">
      </div>

      <label>Round Off</label>
      <div class="toggle-group">
        <button style="background:#00c37e; color:white;">Yes</button>
        <button>No</button>
      </div>

      <div class="total-box">Grand Total 0</div>

      <p>Total in words<br><strong>ZERO RUPEES ONLY</strong></p>

      <label>Payment Type<span style="color:red">*</span></label>
      <div class="payment-type">
        <button class="CREDIT">CREDIT</button>
        <button class="CASH">CASH</button>
        <button class="CHEQUE">CHEQUE</button>
        <button class="ONLINE">ONLINE</button>
      </div>

      <div class="smart-suggestion">
        <span>Smart Suggestion</span>
        <button>+</button>
      </div>
    </div>
  </div-->

  <div class="footer-buttons">
    <button class="btn-back"><a href="purchase.php">&lt; Back</a></button>
    <div>
      <button class="btn-discard"><a href="">Discard</a></button>
      <button class="btn-save-print"><a href="">Save & Print</a></button>
      <button id="btnSavePurchase" type="button" class="btn-save">Save</button>
    </div>
  </div>

  <!-- Minimal DB fields to save purchase invoice -->
  <div class="save-meta" id="saveMetaPurchase" style="margin: 20px 0;">
    <h4 style="margin-bottom:10px;">Save Details (purchase)</h4>
    <div class="form-row" style="max-width:420px;">
      <label for="vendor_select">Vendor<span style="color:red">*</span></label>
      <select id="vendor_select"><option value="">Loading...</option></select>
      <input type="hidden" id="customer_id_purchase" />
    </div>
    <div class="form-row" style="max-width:420px;">
      <label for="invoice_date_purchase">Invoice Date<span style="color:red">*</span></label>
      <input type="date" id="invoice_date_purchase">
    </div>
    <div class="form-row" style="max-width:420px;">
      <label for="due_date_purchase">Due Date</label>
      <input type="date" id="due_date_purchase">
    </div>
    <div class="form-row" style="max-width:420px;">
      <label for="payment_method_purchase">Payment Method</label>
      <select id="payment_method_purchase">
        <option value="cash">Cash</option>
        <option value="credit">Credit</option>
        <option value="cheque">Cheque</option>
        <option value="online">Online</option>
      </select>
    </div>
    <div class="form-row" style="max-width:600px;">
      <label for="notes_purchase">Notes</label>
      <textarea id="notes_purchase" placeholder="Notes (optional)"></textarea>
    </div>
  </div>
</div>

<script>
  (function(){
    // Default today's date
    var invDateEl = document.getElementById('invoice_date_purchase');
    if(invDateEl && !invDateEl.value){
      var d=new Date(), yyyy=d.getFullYear(), mm=('0'+(d.getMonth()+1)).slice(-2), dd=('0'+d.getDate()).slice(-2);
      invDateEl.value = yyyy+'-'+mm+'-'+dd;
    }

    // Load vendors into dropdown
    async function loadVendors(){
      try{
        var sel = document.getElementById('vendor_select');
        var hid = document.getElementById('customer_id_purchase');
        if(!sel) return;
        sel.innerHTML = '<option value="">Loading...</option>';
        // Pick correct API base depending on environment
        var isLiveServer = /^(127\.0\.0\.1|localhost):55\d{2}$/.test(window.location.host);
        var apiUrl = isLiveServer
          ? 'http://localhost/sem5goinvoice/project%201/api/customers.php?type=vendor'
          : '../api/customers.php?type=vendor';
        var res = await fetch(apiUrl, { credentials: 'include' });
        var raw = await res.text();
        var data = {};
        try{ data = JSON.parse(raw); }catch(e){ throw new Error('Non-JSON from customers API'); }
        if(!res.ok || data.success !== true){
          var msg = (data && (data.error||data.message)) || 'Failed to load vendors';
          // If not authenticated, send to login page
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
        sel.innerHTML = '<option value="">Select vendor</option>' + list.map(function(c){
          var name = (c.company_name && c.company_name.trim().length>0) ? c.company_name : c.name;
          return '<option value="'+ c.id +'">'+ name +' (ID:'+ c.id +')</option>';
        }).join('');
        sel.addEventListener('change', function(){ if(hid) hid.value = sel.value; });
        if(list.length === 1){ sel.value = String(list[0].id); if(hid) hid.value = sel.value; }
      }catch(err){
        var sel = document.getElementById('vendor_select'); if(sel){ sel.innerHTML = '<option value="">Load failed</option>'; }
      }
    }
    loadVendors();

    // Load products into all product dropdowns
    var productMap = {}; // id -> product
    async function loadProducts(){
      try{
        // Prefer global API handler when available
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
        productMap = {};
        list.forEach(function(p){ productMap[String(p.id)] = p; });
        var optionsHtml = ['<option value="">Select product...</option>']
          .concat(list.map(function(p){ return '<option value="'+p.id+'">'+ p.name +' (ID:'+ p.id +')</option>'; }))
          .join('');
        document.querySelectorAll('select.product-select').forEach(function(sel){
          sel.innerHTML = optionsHtml;
          sel.value = '';
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
        // Fill related fields if available
        var idEl = tr && tr.querySelector('.product-id'); if(idEl) idEl.value = pid;
        if(p){
          var hsnEl = tr.querySelector('.hsn-input'); if(hsnEl) hsnEl.value = p.hsn_sac || '';
          var priceEl = tr.querySelector('.price-input'); if(priceEl) priceEl.value = (p.purchase_price ?? p.price) || '';
          var gstEl = tr.querySelector('.gst-input'); if(gstEl) gstEl.value = (p.gst_rate != null ? p.gst_rate : '');
        }
        calcRowTotal(tr);
      }, { once: false });
    }

    function calcRowTotal(tr){
      var qty = parseFloat((tr.querySelector('.qty-input')||{}).value || '0');
      var price = parseFloat((tr.querySelector('.price-input')||{}).value || '0');
      var discount = parseFloat((tr.querySelector('.discount-input')||{}).value || '0');
      var gst = parseFloat((tr.querySelector('.gst-input')||{}).value || '0');
      var subtotal = qty * price;
      var taxable = Math.max(0, subtotal - (isNaN(discount) ? 0 : discount));
      var total = taxable + (taxable * (gst/100));
      var totalEl = tr.querySelector('.line-total');
      if(totalEl) totalEl.value = total.toFixed(2);
    }
    function wireRow(tr){
      ['.qty-input','.price-input','.discount-input','.gst-input'].forEach(function(sel){
        var el = tr.querySelector(sel); if(el){ el.addEventListener('input', function(){ calcRowTotal(tr); }); }
      });
      calcRowTotal(tr);
    }
    function addRow(){
      var tbody = document.querySelector('#itemsTablePurchase tbody'); if(!tbody) return;
      var rows = tbody.querySelectorAll('tr'); var tmpl = rows[0]; var clone = tmpl.cloneNode(true);
      Array.from(clone.querySelectorAll('input, textarea')).forEach(function(inp){
        if(inp.classList.contains('gst-input')){/*keep default*/}
        else if(inp.classList.contains('line-total')){ inp.value=''; }
        else { inp.value=''; }
      });
      var sr = clone.querySelector('td:first-child'); if(sr){ sr.textContent = String(rows.length + 1); }
      // Reset and bind product select
      var psel = clone.querySelector('select.product-select');
      if(psel){
        // If products already loaded, reuse; else placeholder
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
      tbody.appendChild(clone); wireRow(clone);
    }
    var firstRow = document.querySelector('#itemsTablePurchase tbody tr'); if(firstRow){
      wireRow(firstRow);
      var firstSelect = firstRow.querySelector('select.product-select');
      if(firstSelect) bindProductSelect(firstSelect);
    }
    var addBtn = document.getElementById('btnAddItemPurchase'); if(addBtn) addBtn.addEventListener('click', function(e){ e.preventDefault(); addRow(); });

    // Kick off product load
    loadProducts();

    // Save handler -> POST to invoices.php?type=purchase
    var btn = document.getElementById('btnSavePurchase'); if(!btn) return;
    btn.addEventListener('click', async function(){
      try{
        var customerId = parseInt((document.getElementById('customer_id_purchase')||{}).value || (document.getElementById('vendor_select')||{}).value || '');
        var invoiceDate = (document.getElementById('invoice_date_purchase')||{}).value;
        var dueDate = (document.getElementById('due_date_purchase')||{}).value || null;
        var paymentMethod = (document.getElementById('payment_method_purchase')||{}).value || 'cash';
        var notes = (document.getElementById('notes_purchase')||{}).value || '';

        if(!customerId || !invoiceDate){
          alert('Please enter required fields: Vendor and Invoice Date');
          return;
        }

        var items = [];
        var firstBadPriceEl = null;
        // Only data rows, skip summary rows
        var rows = document.querySelectorAll('#itemsTablePurchase tbody tr:not(.highlight-row)');
        rows.forEach(function(tr){
          var productIdStr = ((tr.querySelector('.product-id')||{}).value || '').trim();
          var productId = productIdStr === '' ? null : parseInt(productIdStr.replace(/,/g,''));
          var qtyStr = ((tr.querySelector('.qty-input')||{}).value || '0').toString().replace(/\s|,/g,'');
          var priceStr = ((tr.querySelector('.price-input')||{}).value || '0').toString().replace(/\s|,/g,'');
          var gstStr = ((tr.querySelector('.gst-input')||{}).value || '0').toString().replace(/\s|,/g,'');
          var qty = parseFloat(qtyStr);
          var price = parseFloat(priceStr);
          var gstRate = parseFloat(gstStr);
          // Allow missing Product ID; require Qty and Price
          if(qty>0 && price>0){
            items.push({product_id: productId, quantity: qty, unit_price: price, gst_rate: gstRate});
          } else if (qty>0 && (!price || price<=0)) {
            if(!firstBadPriceEl){ firstBadPriceEl = tr.querySelector('.price-input'); }
          }
        });
        if(items.length === 0){
          // Help the user by showing what we read from the first row
          var firstDataRow = document.querySelector('#itemsTablePurchase tbody tr:not(.highlight-row)');
          if(firstDataRow){
            var dbgPid = ((firstDataRow.querySelector('.product-id')||{}).value || '').trim();
            var dbgQty = ((firstDataRow.querySelector('.qty-input')||{}).value || '').trim();
            var dbgPrice = ((firstDataRow.querySelector('.price-input')||{}).value || '').trim();
            console.log('DEBUG purchase items row => product_id:', dbgPid, 'qty:', dbgQty, 'price:', dbgPrice);
            alert('Please set Qty (>0) and Price (>0).\n\nWe read: \nProduct ID: '+ (dbgPid||'(blank)') +'\nQty: '+ (dbgQty||'(blank)') +'\nPrice: '+ (dbgPrice||'(blank)'));
          } else {
            alert('Please enter at least one item: set Qty (>0) and Price (>0). Avoid commas in numbers.');
          }
          if(firstBadPriceEl && typeof firstBadPriceEl.focus === 'function'){
            setTimeout(function(){ firstBadPriceEl.focus(); }, 0);
          }
          return;
        }

        var payload = {
          customer_id: customerId, // API expects customer_id even for purchase; backend maps to vendor_id
          invoice_date: invoiceDate,
          due_date: dueDate,
          payment_method: paymentMethod,
          notes: notes,
          items: items
        };

        // Use absolute API URL when running via Live Server, else relative path on XAMPP
        var isLiveServer = /^(127\.0\.0\.1|localhost):55\d{2}$/.test(window.location.host);
        var apiUrl = isLiveServer
          ? 'http://localhost/sem5goinvoice/project%201/api/invoices.php?type=purchase'
          : '../api/invoices.php?type=purchase';

        var res = await fetch(apiUrl, {
          method: 'POST', headers: {'Content-Type': 'application/json'},
          body: JSON.stringify(payload), credentials: 'include'
        });
        var raw = await res.text(); let data; try{ data = JSON.parse(raw); }catch(e){ throw new Error('Server returned non-JSON: ' + raw.slice(0,200)); }
        if(!res.ok || data.success !== true){
          var msg = (data && (data.error||data.message)) || 'Failed to save purchase invoice';
          if(/Authentication required/i.test(msg) || res.status === 401){
            alert('Please login to save. Redirecting to login...');
            var loginUrl = isLiveServer
              ? 'http://localhost/sem5goinvoice/project%201/front/login.html'
              : '../front/login.html';
            window.location.href = loginUrl;
            return;
          }
          throw new Error(msg);
        }
        alert('Purchase invoice saved: #' + (data.data && data.data.invoice_number ? data.data.invoice_number : ''));
        // Redirect to the correct Purchase listing page
        var redirectUrl = isLiveServer
          ? 'http://localhost/sem5goinvoice/project%201/view/purchase.php'
          : 'purchase.php';
        window.location.href = redirectUrl;
      }catch(err){ alert('Error: ' + err.message); }
    });
  })();
  </script>

<?php include '../includes/footer.php'; ?>
