<?php
$page_title = 'Products / Services - GoInvoice';
$additional_css = ['main.css'];
include '../includes/header.php';

if (!$is_logged_in) {
    header('Location: ../front/login.html');
    exit;
}
?>
<div class="section1">
  <div class="title">Product / Services</div>
  <div class="button-group">
    <button class="btn light" id="btnProdSearch"><span class="invoice-icon"><i class="fa-solid fa-magnifying-glass"></i></span> Search</button>
    <button class="btn green" type="button" data-bs-toggle="modal" data-bs-target="#addProductModal"><span class="invoice-icon"><i class="fa-solid fa-plus color: white"></i></span> Add New</button>
  </div>
</div>

<div class="invoice-table">
  <table>
    <thead>
      <tr>
        <th><input type="checkbox"></th>
        <th>Name</th>
        <th>Price</th>
        <th></th>
      </tr>
    </thead>
    <tbody id="productsTbody">
      <!-- Filled by JS -->
    </tbody>
  </table>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addProductLabel">Add Product / Service</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Product Code</label>
              <input type="text" class="form-control" id="p_code" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Type</label>
              <select class="form-select" id="p_type">
                <option value="product">Product</option>
                <option value="service">Service</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Name</label>
              <input type="text" class="form-control" id="p_name" required />
            </div>
            <div class="col-12">
              <label class="form-label">Description</label>
              <textarea class="form-control" id="p_description" rows="2"></textarea>
            </div>
            <div class="col-md-4">
              <label class="form-label">HSN / SAC</label>
              <input type="text" class="form-control" id="p_hsn" />
            </div>
            <div class="col-md-4">
              <label class="form-label">Unit</label>
              <input type="text" class="form-control" id="p_unit" placeholder="pcs" />
            </div>
            <div class="col-md-4">
              <label class="form-label">SKU / Batch No.</label>
              <input type="text" class="form-control" id="p_sku" placeholder="SKU or Batch No." />
            </div>
            <div class="col-md-6">
              <label class="form-label">GST Rate (%)</label>
              <input type="number" step="0.01" class="form-control" id="p_gst" value="18" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Stock Qty</label>
              <input type="number" step="1" class="form-control" id="p_stock" value="0" />
            </div>

            <div class="col-md-6">
              <label class="form-label">Tax Type</label>
              <select class="form-select" id="p_tax_type">
                <option value="exclusive">Exclusive</option>
                <option value="inclusive">Inclusive</option>
              </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="p_itc">
                <label class="form-check-label" for="p_itc">Eligible for ITC</label>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Manage Stock</label>
              <select class="form-select" id="p_stock_mode">
                <option value="normal">Normal</option>
                <option value="batch">Batch No</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Available Quantity</label>
              <input type="number" step="1" class="form-control" id="p_available_qty" value="0" />
            </div>

            <div class="col-md-4">
              <label class="form-label">MRP</label>
              <input type="number" step="0.01" class="form-control" id="p_mrp" />
            </div>
            <div class="col-md-4">
              <label class="form-label">Purchase Price</label>
              <input type="number" step="0.01" class="form-control" id="p_purchase" />
            </div>
            <div class="col-md-4">
              <label class="form-label">Purchase Price (Incl. Tax)</label>
              <input type="number" step="0.01" class="form-control" id="p_purchase_incl" readonly />
            </div>

            <div class="col-md-6">
              <label class="form-label">Sale Price</label>
              <input type="number" step="0.01" class="form-control" id="p_sale_price" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Product Group</label>
              <input type="text" class="form-control" id="p_group" />
            </div>

            <div class="col-md-6">
              <label class="form-label">Discount Type</label>
              <select class="form-select" id="p_discount_type">
                <option value="none">None</option>
                <option value="percent">Percent</option>
                <option value="amount">Amount</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Attachment</label>
              <input type="file" class="form-control" id="p_attachment" />
            </div>

            <div class="col-md-6 d-flex align-items-end">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="p_visible_docs" checked>
                <label class="form-check-label" for="p_visible_docs">Visible in all documents</label>
              </div>
            </div>
            <div class="col-md-6 d-flex align-items-end">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="p_track_inventory" checked>
                <label class="form-check-label" for="p_track_inventory">Track Inventory</label>
              </div>
            </div>
          </div>
          <div id="p_msg" class="mt-2" style="display:none;"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="btnSaveProduct">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- View Product Modal -->
<div class="modal fade" id="viewProductModal" tabindex="-1" aria-labelledby="viewProductLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewProductLabel">Product / Service Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div><strong>Name:</strong> <span id="pv_name"></span></div>
        <div><strong>Type:</strong> <span id="pv_type"></span></div>
        <div><strong>Description:</strong> <span id="pv_desc"></span></div>
        <div><strong>HSN/SAC:</strong> <span id="pv_hsn"></span></div>
        <div><strong>Unit:</strong> <span id="pv_unit"></span></div>
        <div><strong>Price:</strong> <span id="pv_price"></span></div>
        <div><strong>GST %:</strong> <span id="pv_gst"></span></div>
        <div><strong>Stock Qty:</strong> <span id="pv_stock"></span></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function(){
    loadProducts();
    const saveBtn = document.getElementById('btnSaveProduct');
    if (saveBtn) {
      saveBtn.addEventListener('click', onSaveProduct);
    }

    // Auto-calc purchase incl tax
    ['p_purchase','p_gst','p_tax_type'].forEach(id=>{
      const el = document.getElementById(id);
      if (el) el.addEventListener('input', recalcPurchaseInclTax);
      if (el) el.addEventListener('change', recalcPurchaseInclTax);
    });
    recalcPurchaseInclTax();
  });

  async function loadProducts(){
    try {
      const res = await apiHandler.getProducts();
      const list = res && res.success ? (res.data?.products || []) : [];
      const tbody = document.getElementById('productsTbody');
      if (!tbody) return;
      if (!list.length) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No products found</td></tr>';
        return;
      }
      window.productsCache = list;
      tbody.innerHTML = list.map(p => `
        <tr>
          <td><input type=\"checkbox\" /></td>
          <td>${escapeHtml(p.name || '')}</td>
          <td>${formatMoney(pickDisplayPrice(p))}</td>
          <td class=\"btn-group\">
            <button class=\"btn btn-edit\" data-id=\"${p.id}\">Edit</button>
            <button class=\"btn btn-view\" data-id=\"${p.id}\">View</button>
          </td>
        </tr>
      `).join('');
      // Delegate click handlers
      tbody.onclick = function(e){
        const t = e.target;
        if (!(t instanceof HTMLElement)) return;
        const id = t.getAttribute('data-id');
        if (t.classList.contains('btn-edit') && id){
          openEditProduct(id);
        } else if (t.classList.contains('btn-view') && id){
          openViewProduct(id);
        }
      };
    } catch (err) {
      const tbody = document.getElementById('productsTbody');
      if (tbody) tbody.innerHTML = `<tr><td colspan="4" class="text-danger">${escapeHtml(err.message || 'Failed to load')}</td></tr>`;
    }
  }

  async function onSaveProduct(){
    const msg = document.getElementById('p_msg');
    if (msg) { msg.style.display = 'none'; msg.className = ''; msg.textContent = ''; }
    const payload = {
      type: document.getElementById('p_type')?.value || 'product',
      name: document.getElementById('p_name')?.value?.trim() || '',
      description: document.getElementById('p_description')?.value?.trim() || '',
      hsn_sac: document.getElementById('p_hsn')?.value?.trim() || '',
      unit: document.getElementById('p_unit')?.value?.trim() || 'pcs',
      // Save inclusive purchase price as the display price. Fallback to purchase price.
      price: (function(){
        const incl = parseFloat(document.getElementById('p_purchase_incl')?.value || '');
        const excl = parseFloat(document.getElementById('p_purchase')?.value || '');
        const v = !isNaN(incl) && incl>0 ? incl : (!isNaN(excl) ? excl : 0);
        return v || 0;
      })(),
      gst_rate: parseFloat(document.getElementById('p_gst')?.value || '0') || 0,
      stock_quantity: parseInt(document.getElementById('p_stock')?.value || '0') || 0
    };
    if (!payload.name) {
      if (msg) { msg.style.display='block'; msg.className='text-danger'; msg.textContent='Name is required'; }
      return;
    }
    try {
      const btn = document.getElementById('btnSaveProduct');
      if (btn) { btn.disabled = true; btn.textContent = 'Saving...'; }
      let res;
      if (window.currentProductEditId) {
        // API allows updating these core fields
        const up = {
          name: payload.name,
          description: payload.description,
          hsn_sac: payload.hsn_sac,
          unit: payload.unit,
          price: payload.price,
          gst_rate: payload.gst_rate,
          stock_quantity: payload.stock_quantity,
          type: payload.type
        };
        res = await apiHandler.updateProduct(window.currentProductEditId, up);
      } else {
        res = await apiHandler.createProduct(payload);
      }
      if (res && res.success) {
        if (msg) { msg.style.display='block'; msg.className='text-success'; msg.textContent='Saved successfully'; }
        // reset
        ['p_name','p_description','p_hsn','p_unit','p_mrp','p_purchase','p_purchase_incl','p_sale_price','p_gst','p_stock'].forEach(id=>{ const el=document.getElementById(id); if(el) el.value='';});
        document.getElementById('p_type').value = 'product';
        setTimeout(()=>{
          const modalEl = document.getElementById('addProductModal');
          if (modalEl) {
            const m = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            m.hide();
          }
          window.currentProductEditId = null;
          const titleEl = document.getElementById('addProductLabel');
          if (titleEl) titleEl.textContent = 'Add Product / Service';
          const btnEl = document.getElementById('btnSaveProduct');
          if (btnEl) btnEl.textContent = 'Save';
          loadProducts();
        }, 600);
      } else {
        if (msg) { msg.style.display='block'; msg.className='text-danger'; msg.textContent = (res && res.error) ? res.error : 'Save failed'; }
      }
    } catch (e) {
      if (msg) { msg.style.display='block'; msg.className='text-danger'; msg.textContent = e.message || 'Save failed'; }
    } finally {
      const btn = document.getElementById('btnSaveProduct');
      if (btn) { btn.disabled = false; btn.textContent = 'Save'; }
    }
  }

  function openEditProduct(id){
    const item = (window.productsCache||[]).find(x=> String(x.id) === String(id));
    if (!item) return;
    const set = (k,v)=>{ const el=document.getElementById(k); if(el!=null){ if(el.type==='checkbox'){ el.checked = !!v; } else { el.value = v ?? ''; } } };
    set('p_code', item.product_code||'');
    set('p_type', item.type||'product');
    set('p_name', item.name||'');
    set('p_description', item.description||'');
    set('p_hsn', item.hsn_sac||'');
    set('p_unit', item.unit||'');
    set('p_sku', item.sku||'');
    set('p_gst', item.gst_rate);
    set('p_stock', item.stock_quantity);
    set('p_tax_type', item.tax_type||'exclusive');
    set('p_itc', item.eligible_itc?1:0);
    set('p_stock_mode', item.stock_mode||'normal');
    set('p_available_qty', item.available_qty);
    set('p_mrp', item.mrp);
    set('p_purchase', item.purchase_price);
    set('p_purchase_incl', item.purchase_price_incl_tax);
    set('p_sale_price', item.sale_price);
    set('p_group', item.product_group||'');
    set('p_discount_type', item.discount_type||'none');
    set('p_visible_docs', item.visible_all_docs?1:0);
    set('p_track_inventory', item.track_inventory?1:0);
    window.currentProductEditId = item.id;
    const titleEl = document.getElementById('addProductLabel');
    if (titleEl) titleEl.textContent = 'Edit Product / Service';
    const btnEl = document.getElementById('btnSaveProduct');
    if (btnEl) btnEl.textContent = 'Update';
    recalcPurchaseInclTax();
    const modalEl = document.getElementById('addProductModal');
    if (modalEl) { (bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl)).show(); }
  }

  function openViewProduct(id){
    const item = (window.productsCache||[]).find(x=> String(x.id) === String(id));
    if (!item) return;
    const setText = (k,v)=>{ const el=document.getElementById(k); if(el) el.textContent = (v??''); };
    setText('pv_name', item.name||'');
    setText('pv_type', item.type||'');
    setText('pv_desc', item.description||'');
    setText('pv_hsn', item.hsn_sac||'');
    setText('pv_unit', item.unit||'');
    setText('pv_price', formatMoney(pickDisplayPrice(item)));
    setText('pv_gst', (item.gst_rate??'') );
    setText('pv_stock', (item.stock_quantity??''));
    const modalEl = document.getElementById('viewProductModal');
    if (modalEl) { (bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl)).show(); }
  }

  function escapeHtml(str){
    return (str||'').toString()
      .replace(/&/g,'&amp;')
      .replace(/</g,'&lt;')
      .replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;')
      .replace(/'/g,'&#39;');
  }

  // Choose the best price to display in the listing table
  function pickDisplayPrice(p){
    const candidates = [p.price, p.purchase_price_incl_tax, p.purchase_price, p.sale_price, p.mrp];
    for (let v of candidates){
      const n = parseFloat(v);
      if (!isNaN(n) && n > 0) return n;
    }
    return 0;
  }

  function formatMoney(n){
    const num = parseFloat(n||0);
    return isNaN(num) ? '-' : num.toFixed(2);
  }

  // Calculate "Purchase Price (Incl. Tax)"
  // If Tax Type = Exclusive: Inclusive = Purchase * (1 + GST/100)
  // If Tax Type = Inclusive: Inclusive = Purchase (already includes tax)
  function recalcPurchaseInclTax(){
    try {
      const purchaseVal = parseFloat(document.getElementById('p_purchase')?.value || '');
      const gstVal = parseFloat(document.getElementById('p_gst')?.value || '');
      const taxType = document.getElementById('p_tax_type')?.value || 'exclusive';
      const dest = document.getElementById('p_purchase_incl');
      if (!dest) return;

      const purchase = isNaN(purchaseVal) ? 0 : purchaseVal;
      const gst = isNaN(gstVal) ? 0 : gstVal;

      let inclusive = 0;
      if (taxType === 'exclusive') {
        inclusive = purchase * (1 + (gst/100));
      } else {
        inclusive = purchase;
      }

      if (!purchase && !gst) {
        dest.value = '';
      } else {
        dest.value = inclusive.toFixed(2);
      }
    } catch (e) {
      // no-op
    }
  }

</script>
<?php include '../includes/footer.php'; ?>
