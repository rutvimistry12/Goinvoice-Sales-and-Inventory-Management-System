<?php
$page_title = 'Dashboard - GoInvoice';
$additional_css = ['../view/main.css','../view/dashbord.css'];
include '../includes/header.php';

// Check if user is logged in
if (!$is_logged_in) {
    header('Location: ../front/login.html');
    exit;
}
?>


<div class="profile-card">
  <h2>Complete your profile</h2>
  <div class="profile-section">
    <div>
      <strong>Add Your Business Logo</strong>
      <p>Print your business logo on your invoice to impress your customer with a beautiful invoice.</p>
    </div>
    <div style="display:flex; align-items:center; gap:12px;">
      <img id="logoPreview" src="" alt="Logo" style="display:none; width:64px; height:64px; object-fit:contain; border:1px solid #eee; border-radius:6px;"/>
      <button id="btnAddLogo" class="action-button" type="button">Add Logo</button>
      <input id="logoInput" type="file" accept="image/*" style="display:none" />
    </div>
  </div>
  <div class="profile-section">
    <div>
      <strong>Verify Email!</strong>
      <p>Please check your email and follow the link to verify your email address.</p>
    </div>
    <button class="action-button">Send Email</button>
  </div>
  <div class="profile-section">
    <div>
      <strong>Add Your Bank & UPI Details</strong>
      <p>Get a faster payment with a UPI QR code. These UPI & Bank details will be printed on your invoice.</p>
    </div>
    <button class="action-button">Add Bank</button>
  </div>
</div>

<div class="top-bar-action">
  <span class="last-updated">Last Updated <span id="lastUpdated">14 minute ago</span></span>
  
  <div class="controls">
    <button class="date-button">
      <span>02-02-2025 To 02-08-2025</span>
      <i class="calendar-icon">&#128197;</i>
    </button>
    <button class="refresh-button" onclick="refreshDashboard()">
      Refresh <i class="refresh-icon">&#10227;</i>
    </button>
  </div>
</div>

<div class="dashboard-container11" id="dashboardStats">
  <!-- Stats will be loaded here -->
</div>

<div class="outstanding-container" id="outstandingStats">
  <!-- Outstanding amounts will be loaded here -->
</div>

<!-- Banner -->
<div class="banner">
  <div class="banner-left">
    <h2>Confused where to start?<br>Let's take a quick tour</h2>
    <button class="demo-btn">
      <span class="play-icon">▶</span> Watch Demo Video
    </button>
  </div>

  <div class="banner-right">
    <img src="../images/headerlogo.png" alt="Dashboard preview" class="device-img" />
    <div class="call-info">
      <p><i class="phone-icon">📞</i> or Call Us For Demo</p>
      <p class="number">+91 704-314-6478</p>
      <p class="timing">(10 AM To 7 PM / Everyday)</p>
    </div>
  </div>
</div>

<!-- Dashboard Analytics -->
<div class="dashboard" id="dashboardAnalytics">
  <!-- Analytics will be loaded here -->
</div>

<script>
// Load dashboard data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
    // Initialize logo preview from profile
    initLogoSection();
});

async function loadDashboardData() {
    try {
        showLoading(document.getElementById('dashboardStats'));
        
        const response = await makeAPICall('../api/dashboard.php');
        
        if (response.success) {
            displayDashboardStats(response.data.stats);
            displayOutstandingAmounts(response.data.outstanding_amounts);
            displayAnalytics(response.data);
        } else {
            showError('Failed to load dashboard data');
        }
    } catch (error) {
        showError('Error loading dashboard: ' + error.message);
    }
}

function displayDashboardStats(stats) {
    const container = document.getElementById('dashboardStats');
    container.innerHTML = `
        <div class="card">
            <p class="title">Sale - ${new Date().toLocaleDateString('en-US', { month: 'long', year: 'numeric' })}</p>
            <p class="amount">₹ ${stats.sales.total.toLocaleString()}</p>
            <p class="gst">+GST ${stats.sales.gst.toLocaleString()}</p>
            <div class="line"></div>
        </div>

        <div class="card">
            <p class="title">Purchase - ${new Date().toLocaleDateString('en-US', { month: 'long', year: 'numeric' })}</p>
            <p class="amount">₹ ${stats.purchases.total.toLocaleString()}</p>
            <p class="gst">+GST ${stats.purchases.gst.toLocaleString()}</p>
            <div class="line"></div>
        </div>

        <div class="card">
            <p class="title">Expense - ₹ ${stats.purchases.total.toLocaleString()}</p>
            <p class="title">Income - ₹ ${stats.sales.total.toLocaleString()}</p>
        </div>
    `;
}

function displayOutstandingAmounts(outstanding) {
    const container = document.getElementById('outstandingStats');
    container.innerHTML = `
        <div class="outstanding-card">
            <h5>Sales Outstanding</h5>
            <p class="subtext">Total Receivables ₹ ${(outstanding.sales.pending + outstanding.sales.partial).toLocaleString()}</p>
            <div class="progress-bar"></div>

            <div class="row">
                <div class="col">
                    <p><span class="dot green"></span> ₹ ${outstanding.sales.paid.toLocaleString()}</p>
                    <p class="label">PAID</p>
                </div>
                <div class="col">
                    <p><span class="dot yellow"></span> ₹ ${outstanding.sales.partial.toLocaleString()}</p>
                    <p class="label">PARTIAL</p>
                </div>
                <div class="col">
                    <p><span class="dot orange"></span> ₹ ${outstanding.sales.pending.toLocaleString()}</p>
                    <p class="label">PENDING</p>
                </div>
            </div>
        </div>

        <div class="outstanding-card">
            <h5>Purchase Outstanding</h5>
            <p class="subtext">Total Payables ₹ ${(outstanding.purchases.pending + outstanding.purchases.partial).toLocaleString()}</p>
            <div class="progress-bar"></div>

            <div class="row">
                <div class="col">
                    <p><span class="dot green"></span> ₹ ${outstanding.purchases.paid.toLocaleString()}</p>
                    <p class="label">PAID</p>
                </div>
                <div class="col">
                    <p><span class="dot yellow"></span> ₹ ${outstanding.purchases.partial.toLocaleString()}</p>
                    <p class="label">PARTIAL</p>
                </div>
                <div class="col">
                    <p><span class="dot orange"></span> ₹ ${outstanding.purchases.pending.toLocaleString()}</p>
                    <p class="label">PENDING</p>
                </div>
            </div>
        </div>
    `;
}

function displayAnalytics(data) {
    const container = document.getElementById('dashboardAnalytics');
    container.innerHTML = `
        <div class="row">
            <div class="box"><h5>Best Selling Products</h5><br><span>${data.top_products.length > 0 ? data.top_products[0].name : 'No records found'}</span></div>
            <div class="box"><h5>Least Selling Products</h5><br><span>No records found</span></div>
            <div class="box"><h5>Low Stock</h5><br><span>No records found</span></div>
        </div>

        <div class="row">
            <div class="box wide"><h5>Top Customers</h5><br><span>${data.top_customers.length > 0 ? data.top_customers[0].name : 'No records found'}</span></div>
            <div class="box wide"><h5>Top Vendors</h5><br><span>No records found</span></div>
        </div>

        <div class="row">
            <div class="box full"><h5>Sales Invoice Due</h5><br><span>No records found</span></div>
        </div>

        <div class="row">
            <div class="box full"><h5>Purchase Invoice Due</h5><br><span>No records found</span></div>
        </div>
    `;
}

// Initialize and wire logo upload/edit from dashboard
async function initLogoSection(){
    try {
        if (typeof window.apiHandler === 'object' && window.apiHandler && typeof window.apiHandler.getProfile === 'function') {
            const prof = await window.apiHandler.getProfile();
            const path = (prof && prof.success && prof.data && prof.data.logo_path) ? prof.data.logo_path : '';
            const img = document.getElementById('logoPreview');
            const btnLogoInit = document.getElementById('btnAddLogo');
            if (img && path) {
                img.src = path; img.style.display = 'inline-block';
                if (btnLogoInit) btnLogoInit.style.display = 'none';
            }
        }
    } catch(_) {}

    const btnLogo = document.getElementById('btnAddLogo');
    const inputLogo = document.getElementById('logoInput');
    const imgPrev = document.getElementById('logoPreview');
    if (btnLogo && inputLogo) {
        btnLogo.addEventListener('click', () => inputLogo.click());
        inputLogo.addEventListener('change', async (e) => {
            const file = e.target.files && e.target.files[0];
            if (!file) return;
            try {
                btnLogo.disabled = true; btnLogo.textContent = 'Uploading...';
                const res = await apiHandler.uploadLogo(file);
                if (res && res.success && res.data && res.data.logo_path) {
                    const path = res.data.logo_path;
                    if (imgPrev) { imgPrev.src = path; imgPrev.style.display = 'inline-block'; }
                    // Persist and reflect in header on all pages (per-user cache key)
                    try {
                        var uid = <?php echo isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0; ?>;
                        if (uid) localStorage.setItem('goinvoice_logo_path_' + uid, path);
                        try { localStorage.removeItem('goinvoice_logo_path'); } catch(_) {}
                    } catch(_) {}
                    const headerImg = document.getElementById('headerLogo');
                    if (headerImg) headerImg.src = path;
                    // Hide the Add Logo button once uploaded
                    btnLogo.style.display = 'none';
                } else {
                    alert((res && res.error) ? res.error : 'Upload failed');
                }
            } catch (err) {
                alert(err.message || 'Upload failed');
            } finally {
                btnLogo.disabled = false; btnLogo.textContent = 'Add Logo';
                inputLogo.value = '';
            }
        });
    }
}

function refreshDashboard() {
    loadDashboardData();
    document.getElementById('lastUpdated').textContent = 'just now';
}

// Update last updated time
setInterval(function() {
    const lastUpdated = document.getElementById('lastUpdated');
    if (lastUpdated) {
        const now = new Date();
        lastUpdated.textContent = now.toLocaleTimeString();
    }
}, 60000); // Update every minute
</script>

<?php include '../includes/footer.php'; ?>
