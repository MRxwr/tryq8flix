<div id="footer"></div>

<div class="modal search-modal" id="serch_popup">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-body">
            <a href="" type="button" class="arrow-icon" data-dismiss="modal"><span class="fa fa-long-arrow-left"></span></a>
            <div class="search-box d-flex align-items-center">
                <span class="cat">Clothing</span>
                <div class="form-group mb-0">
                    <input type="text" class="form-control" name="" placeholder="Search your products from here">
                </div>
            </div>
            <a href="" type="button" class="search-fa-icon" data-dismiss="modal"><span class="fa fa-search"></span></a>
        </div>
    </div>
  </div>
</div>

<div class="modal form-popup myModal--effect-zoomIn" id="reg_popup">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <div class="modal-body text-center">
                <div class="modal-box-padding">
                    <h4 class="title">Sign Up</h4>
                    <p class="mb-4">By signing up, you agree to Pickbazar's</p>
                    <div class="form-group">
                        <input type="text" class="form-control" name="" placeholder="demo@demo.com">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="" placeholder="demo">
                    </div>
                    <span class="small mb-4 mt-4">By signing up, you agree to Pickbazar's <a href="" class="text-primary">Terms & Condtion</a></span>
                    <button class="btn theme-btn w-100">Continue</button>
                    <div class="or-div"><span>or</span></div>
                    <button class="btn theme-btn facebook-btn w-100"><span class="fa fa-facebook ml-2 mr-2"></span>Continue with Facebook</button>
                    <button class="btn theme-btn google-btn w-100"><span class="fa fa-google ml-2 mr-2"></span>Continue with Google</button>
                    <p class="mt-4 mb-4">Already have an account? <a href="" class="link"  data-dismiss="modal" data-toggle="modal" data-target="#login_popup">Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal form-popup myModal--effect-zoomIn" id="login_popup">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <div class="modal-body text-center">
                <div class="modal-box-padding">
                    <h4 class="title">Welcome Back</h4>
                    <p class="mb-4">Login with your email & password</p>
                    <div class="form-group">
                        <input type="text" class="form-control" name="" placeholder="demo@demo.com">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="" placeholder="demo">
                    </div>
                    <a href="profile.html"><button class="btn theme-btn w-100">Continue</button></a>
                    <div class="or-div"><span>or</span></div>
                    <button class="btn theme-btn facebook-btn w-100"><span class="fa fa-facebook-square ml-2 mr-2"></span>Continue with Facebook</button>
                    <button class="btn theme-btn google-btn w-100"><span class="fa fa-google ml-2 mr-2"></span>Continue with Google</button>
                    <p class="mt-4 mb-4">Don't have any account? <a href="" class="link" data-dismiss="modal" data-toggle="modal" data-target="#reg_popup">Sign Up</a></p>
                </div>
                <div class="forgot-link">
                    <p>Forgot your password? <a href="" class="link" data-dismiss="modal" data-toggle="modal" data-target="#forgot_popup">Reset It</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal form-popup myModal--effect-zoomIn" id="forgot_popup">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <div class="modal-body text-center">
                <div class="modal-box-padding">
                    <h4 class="title">Forgot Password</h4>
                    <p class="mb-4">We'll send you a link to reset your password</p>
                    <div class="form-group">
                        <input type="text" class="form-control" name="" placeholder="demo@demo.com">
                    </div>
                    <button class="btn theme-btn w-100">Reset Password</button>
                    <p class="mt-4 mb-4">Back to <a href="" class="link" data-dismiss="modal" data-toggle="modal" data-target="#login_popup">Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="h-body ">
    <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-12 text-center">
            <div class="phone">
                </div>
                <div class="message">
                  Please rotate your device!
                </div>
        </div>
    </div>
</div>
<script src="../js/jquery-3.3.1.slim.min.js" ></script>
<script src="../js/jquery-1.11.1.js"></script>
<script src="../js/jquery.min.js"></script>
<script src="../js/jquery-ui.min.js"></script>
<script src="../js/popper.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/wow.min.js"></script>
<script src="../js/owl.carousel.min.js"></script>
<script src="../js/bootstrap-select.min.js"></script>


<script type="text/javascript">
    $( document ).ready(function() {
        new WOW().init();
    });

    $(function(){
        $('.selectpicker').selectpicker();
    });

    //change directory
    $(document).ready(function(){
      $('.en').click(function() {
         $("html[lang=he]").attr("dir", "ltr");
          $("#body").addClass("left-to-right");
      });

      $('.arab').click(function() {
         $("html[lang=he]").attr("dir", "rtl");
        $("#body").removeClass("left-to-right");
      });
        
    });

    $(window).scroll(function(){
      var sticky = $('.fixme'),
          scroll = $(window).scrollTop();

      if (scroll >= 500) sticky.addClass('fixed');
      else sticky.removeClass('fixed');
    });
   
</script>
</body>
</html> 