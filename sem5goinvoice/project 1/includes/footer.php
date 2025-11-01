    <?php if (!$is_logged_in): ?>
        <!-- Public Footer -->
        <footer class="footer">
          <div class="footer-container">
            <div class="footer-column">
              <h2>Get in touch</h2>
              <p><strong>Sales & Support</strong></p>
              <p class="whatsapp"><img src="https://img.icons8.com/color/48/000000/whatsapp--v1.png" alt="WhatsApp Icon" width="20"> 012–345–6789</p>
              <p>(10 AM To 7 PM - Everyday)</p>
              <p><b>Email</b></p>
              <p>help@Goinvoice.com</p>
              <p><b>Follow us</b></p>
              <div class="social-icons">
                <a href="#"><img src="https://img.icons8.com/color/48/facebook-new.png" alt="Facebook" /></a>
                <a href="#"><img src="https://img.icons8.com/color/48/instagram-new--v1.png" alt="Instagram" /></a>
                <a href="#"><img src="https://img.icons8.com/color/48/linkedin.png" alt="LinkedIn" /></a>
                <a href="#"><img src="https://img.icons8.com/color/48/twitterx.png" alt="Twitter" /></a>
                <a href="#"><img src="https://img.icons8.com/color/48/youtube-play.png" alt="YouTube" /></a>
              </div>
            </div>

            <div class="footer-column">
              <h3>Document Format</h3>
              <ul>
                <li>GST Invoice</li>
                <li>Delivery Challan</li>
                <li>Quotation</li>
                <li>Purchase Order</li>
                <li>Proforma Invoice</li>
                <li>Credit & Debit Note</li>
                <li>Export Invoice</li>
              </ul>
            </div>

            <div class="footer-column">
              <h3>Resources</h3>
              <ul>
                <li>About Us</li>
                <li>Blog & News</li>
                <li>Knowledge Base</li>
                <li>Feature Request</li>
                <li>Testimonials</li>
                <li>Partner Program</li>
                <li>Industries We Serve</li>
                <li>Data Migration</li>
              </ul>
            </div>

            <div class="footer-column">
              <h3>Features</h3>
              <ul>
                <li>E-Way Bill</li>
                <li>E-Invoice</li>
                <li>Accounting Software</li>
                <li>Comparison</li>
                <li>Smart Search & Filters </li>
                <li>Product/Service Catalog </li>
                <li>Sales & Purchase Reports </li>
              </ul>
            </div>
          </div>
        </footer>
    <?php endif; ?>

    <script>
        // Global JavaScript functions for API calls
        function makeAPICall(url, method = 'GET', data = null) {
            const options = {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                }
            };
            
            if (data) {
                options.body = JSON.stringify(data);
            }
            
            return fetch(url, options)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        throw new Error(data.error);
                    }
                    return data;
                });
        }
        
        // Show loading spinner
        function showLoading(element) {
            if (element) {
                element.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Loading...';
            }
        }
        
        // Hide loading spinner
        function hideLoading(element, originalText) {
            if (element) {
                element.innerHTML = originalText;
            }
        }
        
        // Show success message
        function showSuccess(message) {
            alert('Success: ' + message);
        }
        
        // Show error message
        function showError(message) {
            alert('Error: ' + message);
        }
    </script>
    
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
