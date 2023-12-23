

<!-- Footer -->
  <footer class="footer text-center " >  
    <div class="container">
    <a href="javascript:" id="return-to-top"><img src="<?php echo SITEURL; ?>assets/images/scrolltotop.png" /></a>
      <div class="row">
        <div class="col-12 h-100 text-center my-auto">
          <ul class="list-inline mb-5">
            <li class="list-inline-item">
              <a href="https://www.instagram.com/hbq.photography/" target=_blank>
                <i class="fab fa-instagram fa-fw fa-2x"></i>
              </a>
            </li>
            <!-- <li class="list-inline-item mr-3">
              <a href="#">
                <i class="fab fa-twitter-square fa-fw fa-2x"></i>
              </a>
            </li>
            <li class="list-inline-item mr-3">
              <a href="#">
                <i class="fab fa-facebook fa-fw fa-2x"></i>
              </a>
            </li> -->
            
          </ul>

          <p class="mb-3 text-center" style="text-align: center !important;"><a href="mailto:info@hbqphoto.com"><i class="far fa-envelope mr-1"></i>info@hbqphoto.com</a></p>
          
          <p class="text-muted mb-5 text-uppercase text-center" style="text-align: center !important;">COPYRIGHT 2020 - HAYAPHOTOGRAPHY- KUWAIT</p>
          <p class="theme-color text-center" style="text-align: center !important;">Powered by <a href="http://www.create-kw.com/" target="_blank" class="text-muted">Create-kw.com</a></p>

        </div>
        

        
      </div>
    </div>
  </footer>

  <!-- Bootstrap core JavaScript -->
  <script src="<?php echo SITEURL; ?>assets/vendor/jquery/jquery.js"></script>
  <script src="<?php echo SITEURL; ?>assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
 
  <!--<script src="<?php echo SITEURL; ?>assets/vendor/jquery/owl.carousel.js"></script>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flexslider/2.7.0/jquery.flexslider.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.9/slick.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>


<!-- Bootstrap Date-Picker Plugin -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/> 
<style>
    .datepicker-inline{
       width: 100%;
    }
    .datepicker table {
    margin: 0;
    
    width: 100%;
}
  </style>
  <?php
  $disabledDates = get_disabledDate();
 if(!empty($disabledDates)){
  foreach($disabledDates as $key=>$disabledDate){
           $disabledDateArr[] =date("d-m-Y", strtotime($disabledDate['disabled_date'])); 	
       
  }
 }
  // Function call with passing the start date and end date 
   $daterange = getDatesFromRange('2021-07-01', '2021-12-31'); 
   @$blocked_dates_array = array_merge($disabledDateArr,$daterange);
   $blocked_date=stripslashes(json_encode($blocked_dates_array));
   //$disabledDate = implode(',', $disabledDateArr);
  ?>
  <script>
  $(document).ready(function(){
      var date_input=$('#bookingdate'); //our date input has the name "date"
      var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
      var options={
        format: "dd-mm-yyyy",
	      inline:true,
        sideBySide: true,
        container: container,
        todayHighlight: true,
        daysOfWeekDisabled: [5],
        datesDisabled: <?=$blocked_date?>,
        autoclose: true,
        //startDate: truncateDate(new Date()),
        startDate: new Date(<?=(get_setting('open_date')!='')?str_replace('-',',',get_setting('open_date')):''?>),
	      endDate: new Date(<?=(get_setting('close_date')!='')?str_replace('-',',',get_setting('close_date')):''?>),
        icons: {
                    time: "fa fa-clock-o",
                    date: "fa fa-calendar",
                    up: "fa fa-arrow-up",
                    down: "fa fa-arrow-down"
                },
        beforeShowDay: function(date) {
            var offday = date.getDay();
             if (date.getDay()== 5 ) {
                 return [true, 'closed'];
             }else{
                 return [true, 'onday'];
             }
            
         },
      
      };
      date_input.datepicker(options).on('changeDate', showTestDate);
      function showTestDate(){
      var value = $('#bookingdate').datepicker('getFormattedDate');
          $("#date").val(value);
          
      }
    })
function truncateDate(date) {
  return new Date(date.getFullYear(), date.getMonth(), date.getDate());
 }
	 $(document).ready(function(){
		  $('#booknow').click(function(){
		 var date = $("#date").val();
		 if(date != ""){
      //alert($('input[name=theme]:checked').val()); 
      localStorage.setItem("theme", $('input[name=theme]:checked', '#th_result').val());
         var cid = $("#package_category").val();
         var level = $("#package_level").val();
         var level_price = $("#package_level").val();
                localStorage.setItem("level", level);
                var level_price = $('select[name=package_level] option').filter(':selected').attr('data-price');
                localStorage.setItem("level_price",level_price);

		  window.location.href = "<?php echo SITEURL; ?>index.php?page=personal-information&id=<?php echo @($id?$id:0); ?>&date="+date+"&cid="+cid;
		 } else{
			alert("Please select date!"); 
			return false;
		 }
	  });
    // get your select element and listen for a change event on it
 
$('#package_category').change(function() {
  var cid = $("#package_category").val();
  // set the window's location property to the value of the option the user has selected
 // window.location.href = "<?php echo SITEURL; ?>index.php?page=reservations&id=<?php echo @($id?$id:0); ?>&cid="+cid;
  $.post( "pages/get_themes.php?cid="+cid, function( data ) {
    $( "#th_result" ).html( data );
        $('.res_carousel').slick({
        speed: 500,
        slidesToShow: 4,
        cssEase: 'linear',
        slidesToScroll: 1,
        autoplay: false,
        autoplaySpeed: 2000,
        dots:false,
        centerMode: true,
        responsive: [{
          breakpoint: 1024,
          settings: {
            slidesToShow: 3,
            slidesToScroll: 1,
            // centerMode: true,

          }
        }, {
          breakpoint: 800,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 1,
            dots: false,
            infinite: true,

          }
        },  {
          breakpoint: 480,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: false,
            infinite: true,
            autoplay: true,
            autoplaySpeed: 2000,
          }
        }]
      });
  });
  //window.location = $(this).val();
});
	  })
  </script>


  <script>
    // Get the elements with class="column"
    var elements = document.getElementsByClassName("column");
    
    // Declare a loop variable
    var i;
    
    // Full-width images
    function one() {
        for (i = 0; i < elements.length; i++) {
        elements[i].style.msFlex = "100%";  // IE10
        elements[i].style.flex = "100%";
      }
    }
    
    // Two images side by side
    function two() {
      for (i = 0; i < elements.length; i++) {
        elements[i].style.msFlex = "50%";  // IE10
        elements[i].style.flex = "50%";
      }
    }
    
    // Four images side by side
    function four() {
      for (i = 0; i < elements.length; i++) {
        elements[i].style.msFlex = "25%";  // IE10
        elements[i].style.flex = "25%";
      }
    }
  </script>

<script>
$(document).ready(function(){
    $('#plebel').val(localStorage.getItem("level"))
    $('#lebel_price').val(localStorage.getItem("level_price"))
    $('#theme').val(localStorage.getItem("theme"))
    $("#package_category").change();
	$('#book-btn').click(function(){

	var searchquery = $("input#bookingid").val();
    var dataString = 'searchquery='+searchquery;
   
		if(searchquery != ''){
    $('#bars1').show();
		$.ajax({
				type:'POST',
				url:'pages/getBookingDetailsAjax.php',
				data: dataString,
				success:function(html){
          setTimeout(() => {
            $('#bars1').hide();
            $('#bookingDataDiv').html(html);
            $('#datable_1').DataTable({
              "bFilter": true,
              "bLengthChange": false,
              "bPaginate": true,
              "bInfo": false,
              });
            }, 1000);
				}
			}); 
		} else {
			alert("Please enter reservation number!");
			return false;
		}
	});


// $("#package_level").change(function(){	
// 	var level = $("#package_level").val();
//     localStorage.setItem("level", level); 
// }); 	

$("#booking_time").change(function(){
    
    var date = $("#booking_date").val();
    var time = this.value;
    var dataString = 'time='+time+'&date='+date;
	$.ajax({
				type:'POST',
				url:'pages/checkBookingDateTimeAjax.php',
				data: dataString,
				success:function(result){
					if(result == 1){
             $('#continue_to_payment').prop('disabled', true);
             $('#booking_time').prop('selectedIndex',0);
						 alert("Please select other time!");
					} else{
						$('#continue_to_payment').prop('disabled', false);
          }
                
				}
			}); 
			
			function fetchdata(){
				$.ajax({
				type:'POST',
				url:'pages/sessionOutAjax.php',
				data: dataString,
				success:function(result){
					if(result == 1){
						 alert("Session Out!!!");
				window.location.href = '<?php echo SITEURL; ?>index.php?page=reservations&id=<?php echo @($id?$id:0); ?>';
					}
				   }
				}); 
			}
			setInterval(fetchdata,900000);
    
  });
  $( 'div.theme_li' ).on( 'click', function() {
            $( this ).parent().find( 'div' ).removeClass( 'active' );
            //$( this ).find( 'input' ).click();
            localStorage.setItem("theme", $( this ).attr( 'data-title' ));
            $( this ).addClass( 'active' );
            $( this ).children().find( 'input' ).prop("checked", true) 
      });
});

$('#business_type').on('change', function () {
       $('#x_business_type').hide();
      if(this.value==0){
        $('#x_business_type').show();
      }else{
        $('#x_business_type').hide();
      }
  }); 
$("#contactForm").submit(function(event){
    // cancels the form submission
    event.preventDefault();
    submitForm();
});
function submitForm(){
    var name = $("#name").val();
    var email = $("#email").val();
    var phone = $("#phone").val();
    var subject = $("#subject").val();
    var message = $("#message").val();
  $('#bars1').show();
  $.ajax({
        type: "POST",
        url: "pages/contactFormAjax.php",
        data: "name=" + name + "&email=" + email + "&phone=" + phone + "&subject=" + subject + "&message=" + message,
        success : function(text){
            if (text == "success"){
                formSuccess();
                setTimeout(() => {
                  $('#bars1').hide();
                  formSuccess();
                  }, 1000);
                
            }
        }
    });
}
function formSuccess(){
    $( "#msgSubmit" ).removeClass( "hidden" );
}


  
</script> 
<script>
	// ===== Scroll to Top ==== 
$(window).scroll(function() {
    if ($(this).scrollTop() >= 50) {        // If page is scrolled more than 50px
        $('#return-to-top').fadeIn(200);    // Fade in the arrow
    } else {
        $('#return-to-top').fadeOut(200);   // Else fade out the arrow
    }
});
$('#return-to-top').click(function() {      // When arrow is clicked
    $('body,html').animate({
        scrollTop : 0                       // Scroll to top of body
    }, 500);
});
</script>

<script>


</script>  
<?php
if(@$_GET['page'] == "booking-faild"){
	?>
    <script>
$(document).ready(function(){
	
 function fetchdata(){
	 
				$.ajax({
				type:'POST',
				url:'pages/sessionOutAjax.php',
				data: '',
				success:function(result){
					if(result == 1){
						// alert("Session Out!!!");
						
					}
				   }
				}); 
			}
			setInterval(fetchdata,1000);   
		});
		</script>	
    <?php
}
?>
 <script>
   $("body").on("click", ".slick-prev",".slick-next", function(e) {
      $(this).focus();
   });
$("body").on("click", "#coupon_appy", function(e) {
 var total_price = localStorage.getItem("total_price");
 var coupon_code = $('#coupon_code').val();
 if(coupon_code!=''){
  //alert(total_price);
  $.ajax({
        type: "POST",
        url: "pages/checkCouponAjax.php",
        data: "coupon_code=" + coupon_code +'&total_price=' + total_price,
        success : function(res){

         var obj = jQuery.parseJSON(res);
        // alert(obj);
            if (obj.status == "success"){
                setTimeout(() => {
                 //alert(obj.discount)
                 var nerprice = total_price - obj.discount;
                 $('#totalprice').text(nerprice );
                 $('#discountprice').text('(Discount : '+ obj.discount + 'KD)');
                 $('#discount_price').val( obj.discount);
                 
                }, 1000); 
            }else{
              alert('invalid coupon code');
            }
        }
    });
 }else{
  alert('Please Enter Coupon Code');
 }
  //alert('okey')
  //calculate_price();
});


$("body").on("click", "#referral_appy", function(e) {
 var referral_code = $('#referral_code').val();
 if(referral_code!=''){
  $.ajax({
        type: "POST",
        url: "pages/checkReferralAjax.php",
        data: "referral_code=" + referral_code ,
        success : function(res){
         var obj = jQuery.parseJSON(res);
            if (obj.status == "success"){
                setTimeout(() => {
                  alert('Thank you for use gift code :'+obj.code);
                }, 1000); 
            }else{
              alert('invalid gift code');
              $('#referral_code').val('');
            }
        }
    });
 }else{
  alert('<?=$lang['Please enter gift code']?>');
 }
});

$("body").on("click", ".xprice", function(e) {
  //alert('okey')
  calculate_price();
});

var calculate_price = function(){
  var exprice = 0;
  setTimeout( function() 
    {
      $("input:checkbox[name='select_extra_item[]']:checked").each(function(){
          exprice = parseFloat(exprice) + parseFloat($(this).attr('data-exprice'));
        });
        var itemval=30.500;
        var plebel = localStorage.getItem("level_price");
        var total_price =  (parseFloat(plebel)+ parseFloat(exprice)); 
        localStorage.setItem("total_price",total_price);
        //alert(total_price);
        $('#totalprice').text(total_price);
    }, 2000);
  
}
$(document).ready(function(){

  $('.res_carousel').slick({
    speed: 500,
    slidesToShow: 4,
    cssEase: 'linear',
    slidesToScroll: 1,
    autoplay: false,
    autoplaySpeed: 2000,
    dots:false,
    centerMode: true,
    responsive: [{
      breakpoint: 1024,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 1,
        // centerMode: true,

      }
    }, {
      breakpoint: 800,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1,
        dots: false,
        infinite: true,

      }
    },  {
      breakpoint: 480,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: false,
        infinite: true,
        autoplay: true,
        autoplaySpeed: 2000,
      }
    }]
  });
  

  $('.work_carousel').slick({
    speed: 500,
    slidesToShow: 6,
    cssEase: 'linear',
    slidesToScroll: 1,
    autoplay: false,
    autoplaySpeed: 2000,
    dots:false,
    centerMode: true,
    responsive: [{
      breakpoint: 1024,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 1,
        // centerMode: true,

      }
    }, {
      breakpoint: 800,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 1,
        dots: false,
        infinite: true,

      }
    },  {
      breakpoint: 480,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: true,
        infinite: true,
        autoplay: true,
        autoplaySpeed: 2000,
      }
    }]
  });

  $('#slider2').flexslider({
        animation: "slide",
        controlNav: false,
        animationLoop: false,
        slideshow: false,
        itemWidth: 150,
        itemMargin: 4,
        asNavFor: '#slider1'
      });
      
      $('#slider1').flexslider({
        animation: "fade",
        controlNav: false,
        directionNav: true,
        animationLoop: true,
        slideshow: true,
        sync: "#slider2",
        start: function(slider){
          $('body').removeClass('loading');
        }
      });
     
  calculate_price();
  setTimeout(function(){ 
    $('.wrapper').find('.slick-track').css('left','0px'); 
  }, 1000);
});



</script>
<!-- The Modal -->
<?php if(get_setting('is_modal')==1){ ?>
  <script>
      $("#homeImageModal").modal('show');
  </script>
<?php	} ?>

</body>

</html>
