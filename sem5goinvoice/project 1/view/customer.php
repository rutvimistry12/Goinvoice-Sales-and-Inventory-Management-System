<?php
$page_title = 'Customer/Vendor - GoInvoice';
$additional_css = ['main.css'];
include '../includes/header.php';

// Require login like dashboard
if (!$is_logged_in) {
    header('Location: ../front/login.html');
    exit;
}
?>
<div class="container">
  <div class="card">
    <h2>Customer / Vendor</h2>
    <div class="stats">
      <p>Total</p>
      <strong>1</strong>
    </div>
    <div class="stat-box">
      <p>Customer</p>
      <strong>1</strong>
    </div>
    <div class="stat-box">
      <p>Vendor</p>
      <strong>0</strong>
    </div>
    <div class="stat-box">
      <p>Customer Vendor</p>
      <strong>0</strong>
    </div>
    <div class="actions">
      <button class="btn" id="btnSearch">Search</button>
      <button class="btn add" type="button" data-bs-toggle="modal" data-bs-target="#addCustomerModal">+ Add New</button>
    </div>
  </div>
</div>

<div class="card table-card">
  <table>
    <thead>
      <tr>
        <th><input type="checkbox"></th>
        <th>Name</th>
        <th>Get Outstanding</th>
        <th>Phone</th>
        <th>Type</th>
        <th>State</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody id="customersTbody">
      <!-- Filled by JS -->
    </tbody>
  </table>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addCustomerLabel">Add Customer/Vendor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Company Name</label>
              <input type="text" class="form-control" id="c_company_name" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Name</label>
              <input type="text" class="form-control" id="c_name" required />
            </div>
            <div class="col-md-6">
              <label class="form-label">Type</label>
              <select class="form-select" id="c_type">
                <option value="customer">Customer</option>
                <option value="vendor">Vendor</option>
              </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="c_is_registered">
                <label class="form-check-label" for="c_is_registered">Registered (GST)</label>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" id="c_email" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Mobile</label>
              <input type="text" class="form-control" id="c_mobile" />
            </div>
            <div class="col-md-6">
              <label class="form-label">GST Number</label>
              <input type="text" class="form-control" id="c_gst" />
            </div>
            <div class="col-md-6">
              <label class="form-label">PAN Number</label>
              <input type="text" class="form-control" id="c_pan" />
            </div>
            <div class="col-md-6">
              <label class="form-label">State</label>
              <input type="text" class="form-control" id="c_state" />
            </div>
            <div class="col-md-6">
              <label class="form-label">City</label>
              <input type="text" class="form-control" id="c_city" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Pincode</label>
              <input type="text" class="form-control" id="c_pincode" />
            </div>
            <div class="col-12">
              <label class="form-label">Address</label>
              <textarea class="form-control" id="c_address" rows="2"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">Opening Balance</label>
              <input type="number" step="0.01" class="form-control" id="c_opening_balance" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Credit Limit</label>
              <input type="number" step="0.01" class="form-control" id="c_credit_limit" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Credit Due Date</label>
              <input type="date" class="form-control" id="c_credit_due_date" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Fax</label>
              <input type="text" class="form-control" id="c_fax" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Website</label>
              <input type="url" class="form-control" id="c_website" placeholder="https://example.com" />
            </div>
            <div class="col-12">
              <label class="form-label">Custom Fields (JSON)</label>
              <textarea class="form-control" id="c_custom_fields" rows="2" placeholder='{"field1":"value","field2":"value"}'></textarea>
            </div>
            <div class="col-12">
              <label class="form-label">Note</label>
              <textarea class="form-control" id="c_note" rows="2"></textarea>
            </div>
          </div>
          <div id="c_msg" class="mt-2" style="display:none;"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="btnSaveCustomer">Save</button>
      </div>
    </div>
  </div>
  </div>

<!-- View Customer Modal -->
<div class="modal fade" id="viewCustomerModal" tabindex="-1" aria-labelledby="viewCustomerLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewCustomerLabel">Customer Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="viewCustomerBody">
          <div><strong>Name:</strong> <span id="v_name"></span></div>
          <div><strong>Type:</strong> <span id="v_type"></span></div>
          <div><strong>Mobile:</strong> <span id="v_mobile"></span></div>
          <div><strong>Email:</strong> <span id="v_email"></span></div>
          <div><strong>State:</strong> <span id="v_state"></span></div>
          <div><strong>City:</strong> <span id="v_city"></span></div>
          <div><strong>Address:</strong> <span id="v_address"></span></div>
          <div><strong>GST:</strong> <span id="v_gst"></span></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
  </div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    loadCustomers();
    const saveBtn = document.getElementById('btnSaveCustomer');
    if (saveBtn) {
      saveBtn.addEventListener('click', async function(){
        const payload = {
          company_name: document.getElementById('c_company_name')?.value?.trim() || '',
          name: document.getElementById('c_name')?.value?.trim() || '',
          type: document.getElementById('c_type')?.value || 'customer',
          is_registered: document.getElementById('c_is_registered')?.checked ? 1 : 0,
          email: document.getElementById('c_email')?.value?.trim() || '',
          mobile: document.getElementById('c_mobile')?.value?.trim() || '',
          gst_number: document.getElementById('c_gst')?.value?.trim() || '',
          pan_number: document.getElementById('c_pan')?.value?.trim() || '',
          address: document.getElementById('c_address')?.value?.trim() || '',
          city: document.getElementById('c_city')?.value?.trim() || '',
          state: document.getElementById('c_state')?.value?.trim() || '',
          pincode: document.getElementById('c_pincode')?.value?.trim() || ''
        };
        // optional numeric/date/custom fields
        const opening = document.getElementById('c_opening_balance')?.value;
        if (opening !== undefined && opening !== '') payload.opening_balance = parseFloat(opening);
        const climit = document.getElementById('c_credit_limit')?.value;
        if (climit !== undefined && climit !== '') payload.credit_limit = parseFloat(climit);
        const cdate = document.getElementById('c_credit_due_date')?.value;
        if (cdate) payload.credit_due_date = cdate;
        const fax = document.getElementById('c_fax')?.value?.trim(); if (fax) payload.fax = fax;
        const web = document.getElementById('c_website')?.value?.trim(); if (web) payload.website = web;
        const note = document.getElementById('c_note')?.value?.trim(); if (note) payload.note = note;
        const cfieldsRaw = document.getElementById('c_custom_fields')?.value?.trim();
        if (cfieldsRaw) {
          try {
            payload.custom_fields = JSON.parse(cfieldsRaw);
          } catch (e) {
            if (msg) { msg.style.display='block'; msg.className='text-danger'; msg.textContent='Custom Fields JSON is invalid'; }
            return;
          }
        }
        const msg = document.getElementById('c_msg');
        if (msg) { msg.style.display = 'none'; msg.className = ''; msg.textContent=''; }
        if (!payload.name) {
          if (msg) { msg.style.display='block'; msg.className='text-danger'; msg.textContent='Name is required'; }
          return;
        }
        try {
          saveBtn.disabled = true; saveBtn.textContent = 'Saving...';
          let res;
          if (window.currentCustomerEditId) {
            // API update supports limited fields
            const up = {
              name: payload.name,
              email: payload.email,
              mobile: payload.mobile,
              gst_number: payload.gst_number,
              address: payload.address,
              city: payload.city,
              state: payload.state,
              pincode: payload.pincode,
              type: payload.type
            };
            res = await apiHandler.updateCustomer(window.currentCustomerEditId, up);
          } else {
            res = await apiHandler.createCustomer(payload);
          }
          if (res && res.success) {
            if (msg) { msg.style.display='block'; msg.className='text-success'; msg.textContent='Saved successfully'; }
            // reset form
            ['c_company_name','c_name','c_email','c_mobile','c_gst','c_pan','c_address','c_city','c_state','c_pincode','c_opening_balance','c_credit_limit','c_credit_due_date','c_fax','c_website','c_custom_fields','c_note']
              .forEach(id=>{ const el=document.getElementById(id); if(el) el.value='';});
            const reg = document.getElementById('c_is_registered'); if (reg) reg.checked = false;
            // close modal after short delay and reload list
            setTimeout(()=>{
              const modalEl = document.getElementById('addCustomerModal');
              if (modalEl) {
                const m = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                m.hide();
              }
              window.currentCustomerEditId = null;
              const titleEl = document.getElementById('addCustomerLabel');
              if (titleEl) titleEl.textContent = 'Add Customer/Vendor';
              const btnEl = document.getElementById('btnSaveCustomer');
              if (btnEl) btnEl.textContent = 'Save';
              loadCustomers();
            }, 600);
          } else {
            if (msg) { msg.style.display='block'; msg.className='text-danger'; msg.textContent=res.error || 'Save failed'; }
          }
        } catch (err) {
          if (msg) { msg.style.display='block'; msg.className='text-danger'; msg.textContent= err.message || 'Save failed'; }
        } finally {
          saveBtn.disabled = false; saveBtn.textContent = 'Save';
        }
      });
    }
  });

  async function loadCustomers(){
    try {
      const res = await apiHandler.getCustomers();
      const list = res && res.success ? (res.data?.customers || []) : [];
      const tbody = document.getElementById('customersTbody');
      if (!tbody) return;
      if (!list.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No records found</td></tr>';
        return;
      }
      window.customersCache = list;
      tbody.innerHTML = list.map(c => `
        <tr>
          <td><input type="checkbox" /></td>
          <td>${escapeHtml(c.name || '')}</td>
          <td><a href="#">Get Outstanding</a></td>
          <td>${escapeHtml(c.mobile || '')}</td>
          <td>${escapeHtml((c.type || '').toString().charAt(0).toUpperCase() + (c.type || '').toString().slice(1))}</td>
          <td>${escapeHtml(c.state || '')}</td>
          <td class="btn-group">
            <button class="btn btn-edit" data-id="${c.id}">Edit</button>
            <button class="btn btn-view" data-id="${c.id}">View</button>
            <button class="btn">▼</button>
          </td>
        </tr>`).join('');
      // Delegate click handlers
      tbody.onclick = function(e){
        const t = e.target;
        if (!(t instanceof HTMLElement)) return;
        const id = t.getAttribute('data-id');
        if (t.classList.contains('btn-edit') && id){
          openEditCustomer(id);
        } else if (t.classList.contains('btn-view') && id){
          openViewCustomer(id);
        }
      };
      // Update counts
      try {
        const total = list.length;
        const customers = list.filter(x=>x.type==='customer').length;
        const vendors = list.filter(x=>x.type==='vendor').length;
        const both = 0;
        document.querySelector('.stats strong')?.replaceChildren(document.createTextNode(total));
        const boxes = document.querySelectorAll('.stat-box strong');
        if (boxes[0]) boxes[0].textContent = customers;
        if (boxes[1]) boxes[1].textContent = vendors;
        if (boxes[2]) boxes[2].textContent = both;
      } catch(_) {}
    } catch (err) {
      const tbody = document.getElementById('customersTbody');
      if (tbody) tbody.innerHTML = `<tr><td colspan="7" class="text-danger">${escapeHtml(err.message || 'Failed to load')}</td></tr>`;
    }
  }

  function openEditCustomer(id){
    const item = (window.customersCache||[]).find(x=> String(x.id) === String(id));
    if (!item) return;
    // Prefill fields
    const set = (k,v)=>{ const el=document.getElementById(k); if(el!=null){ if(el.type==='checkbox'){ el.checked = !!v; } else { el.value = v ?? ''; } } };
    set('c_company_name', item.company_name||'');
    set('c_name', item.name||'');
    set('c_type', item.type||'customer');
    set('c_is_registered', item.is_registered?1:0);
    set('c_email', item.email||'');
    set('c_mobile', item.mobile||'');
    set('c_gst', item.gst_number||'');
    set('c_pan', item.pan_number||'');
    set('c_address', item.address||'');
    set('c_city', item.city||'');
    set('c_state', item.state||'');
    set('c_pincode', item.pincode||'');
    // Optional fields if present
    set('c_opening_balance', item.opening_balance);
    set('c_credit_limit', item.credit_limit);
    set('c_credit_due_date', item.credit_due_date||'');
    set('c_fax', item.fax||'');
    set('c_website', item.website||'');
    set('c_note', item.note||'');
    try{ document.getElementById('c_custom_fields').value = item.custom_fields ? (typeof item.custom_fields==='string'? item.custom_fields : JSON.stringify(item.custom_fields)) : ''; }catch(_){}
    window.currentCustomerEditId = item.id;
    const titleEl = document.getElementById('addCustomerLabel');
    if (titleEl) titleEl.textContent = 'Edit Customer/Vendor';
    const btnEl = document.getElementById('btnSaveCustomer');
    if (btnEl) btnEl.textContent = 'Update';
    const modalEl = document.getElementById('addCustomerModal');
    if (modalEl) { (bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl)).show(); }
  }

  function openViewCustomer(id){
    const item = (window.customersCache||[]).find(x=> String(x.id) === String(id));
    if (!item) return;
    const setText = (k,v)=>{ const el=document.getElementById(k); if(el) el.textContent = (v??''); };
    setText('v_name', item.name||'');
    setText('v_type', (item.type||'').toString());
    setText('v_mobile', item.mobile||'');
    setText('v_email', item.email||'');
    setText('v_state', item.state||'');
    setText('v_city', item.city||'');
    setText('v_address', item.address||'');
    setText('v_gst', item.gst_number||'');
    const modalEl = document.getElementById('viewCustomerModal');
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
</script>
<?php include '../includes/footer.php'; ?>
