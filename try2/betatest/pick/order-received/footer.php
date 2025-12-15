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

    $(document).ready(function(){
      $("#profile_pic").click(function(){
        $(".profile_menu").toggleClass('show');
      });
    });
   
</script>
</body>
</html> 