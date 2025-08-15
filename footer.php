<footer class="py-5">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="footer-menu">
              <img src="images/logo.png" alt="logo">
              <div class="social-links mt-5">
                <ul class="d-flex list-unstyled gap-2">
                  <li><a href="#" class="btn btn-outline-light">FB</a></li>
                  <li><a href="#" class="btn btn-outline-light">TW</a></li>
                  <li><a href="#" class="btn btn-outline-light">YT</a></li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-md-2 col-sm-6">
            <div class="footer-menu">
              <h5 class="widget-title">Ultras</h5>
              <ul class="menu-list list-unstyled">
                <li class="menu-item"><a href="#" class="nav-link">About us</a></li>
                <li class="menu-item"><a href="#" class="nav-link">Conditions</a></li>
              </ul>
            </div>
          </div>
          <div class="col-md-2 col-sm-6">
            <div class="footer-menu">
              <h5 class="widget-title">Customer Service</h5>
              <ul class="menu-list list-unstyled">
                <li class="menu-item"><a href="#" class="nav-link">FAQ</a></li>
                <li class="menu-item"><a href="#" class="nav-link">Contact</a></li>
              </ul>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="footer-menu">
              <h5 class="widget-title">Subscribe Us</h5>
              <p>Subscribe to our newsletter for updates.</p>
              <form class="d-flex mt-3 gap-0" role="newsletter">
                <input class="form-control rounded-start rounded-0 bg-light" type="email" placeholder="Email Address">
                <button class="btn btn-dark rounded-end rounded-0" type="submit">Subscribe</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </footer>
    <div id="footer-bottom">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-6 copyright">
            <p>© 2025 Foodmart. All rights reserved.</p>
          </div>
          <div class="col-md-6 credit-link text-start text-md-end">
            <p>Free HTML Template by <a href="https://templatesjungle.com/">TemplatesJungle</a></p>
          </div>
        </div>
      </div>
    </div>
    
    <script src="js/jquery-1.11.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/plugins.js"></script>
    <script src="js/script.js"></script>

    <script>
    $(document).ready(function() {
        $('.add-to-cart-btn').on('click', function(e) {
            e.preventDefault();

            var button = $(this);
            var productItem = button.closest('.product-item');
            
            var productId = button.data('id');
            var productName = button.data('name');
            var productPrice = button.data('price');
            var quantity = productItem.find('.input-number').val();

            $.ajax({
                url: 'cart_manager.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'add',
                    product_id: productId,
                    name: productName,
                    price: productPrice,
                    quantity: quantity
                },
                success: function(response) {
                    if (response.success) {
                        $('.cart-count').text(response.cart_count);
                        alert(response.message); 
                        // For a better user experience, consider replacing alert with a nicer notification
                        // For now, we will refresh the page to update the offcanvas cart content
                        // location.reload(); // You can uncomment this line if you want the page to refresh
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while connecting to the server.');
                }
            });
        });
    });
    </script>

  </body>
</html>