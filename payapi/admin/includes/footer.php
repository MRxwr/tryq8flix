</div>
    <!-- /#wrapper -->
	<!-- Modal -->
  <div class="modal fade" id="changeTime" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><?=$lang['change time']?></h4>
        </div>
        <div class="modal-body">
         <div id="timeField"></div>
        </div>
        <div class="modal-footer">
          <button type="button" onclick="changeTimeSlot()" disable class="btn btn-default">Submit</button>
        </div>
      </div>
    </div> 
  </div>
	<!-- JavaScript -->
	
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="<?php echo SITEURL; ?>admin/assets/vendors/bower_components/bootstrap/dist/js/bootstrap.js"></script>
	
	<!-- wysuhtml5 Plugin JavaScript -->
	<script src="<?php echo SITEURL; ?>admin/assets/vendors/bower_components/wysihtml5x/dist/wysihtml5x.js"></script>
	
  <script src="<?php echo SITEURL; ?>admin/assets/vendors/bower_components/bootstrap3-wysihtml5-bower/dist/bootstrap3-wysihtml5.all.js"></script>
  
  <script src="<?php echo SITEURL; ?>admin/assets/vendors/bower_components/moment/min/moment.min.js"></script>
	<script src="<?php echo SITEURL; ?>admin/assets/vendors/jquery-ui.min.js"></script>
 
 
  

	<!-- Fancy Dropdown JS -->
	<script src="<?php echo SITEURL; ?>admin/assets/style/dist/js/dropdown-bootstrap-extended.js"></script>
	
	<!-- Bootstrap Wysuhtml5 Init JavaScript -->
	<!-- <script src="<?php echo SITEURL; ?>admin/assets/style/dist/js/bootstrap-wysuhtml5-data.js?v=12"></script> -->
	
	<!-- Slimscroll JavaScript -->
  <script src="<?php echo SITEURL; ?>admin/assets/style/dist/js/jquery.slimscroll.js"></script>
  
  <!-- Progressbar Animation JavaScript -->
	<script src="<?php echo SITEURL; ?>admin/assets/vendors/bower_components/waypoints/lib/jquery.waypoints.min.js"></script>
	<script src="<?php echo SITEURL; ?>admin/assets/vendors/bower_components/jquery.counterup/jquery.counterup.min.js"></script>
	

	<!-- Switchery JavaScript -->
	<script src="<?php echo SITEURL; ?>admin/assets/vendors/bower_components/switchery/dist/switchery.min.js"></script>
	
	<!-- Init JavaScript -->
	<script src="<?php echo SITEURL; ?>admin/assets/style/dist/js/init.js"></script>
    
    <!-- Bootstrap Daterangepicker JavaScript -->
	<script src="<?php echo SITEURL; ?>admin/assets/vendors/bower_components/dropify/dist/js/dropify.min.js"></script>
	
    <!-- Form Flie Upload Data JavaScript -->
	<script src="<?php echo SITEURL; ?>admin/assets/style/dist/js/form-file-upload-data.js"></script>
        
        	<!-- Data table JavaScript -->
<!-- jQuery Library -->


<!-- Datatable JS -->
<script src="<?php echo SITEURL; ?>admin/assets/vendors/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="<?php echo SITEURL; ?>admin/assets/style/dist/js/dataTables-data.js"></script>
  <script src="<?php echo SITEURL; ?>admin/assets/js/custom.js"></script>   

  <script type="text/javascript">
    $(document).ready(function(){
    $('#datable_ex').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'excel',
            'print'
        ],
        "order": [[ 0, 'desc' ]],
        "pageLength": 50,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
            'url':'moduls/ajaxHistories.php'
        },
        'columns': [
          { data: 'hid' },
          { data: 'api_key' },
          { data: 'endpoint' },
          { data: 'status' },
          { data: 'msg' },
          { data: 'created_at' },
          { data: 'action' },
        ]
    });
  });
 
  </script>


   <script type="text/javascript">//<![CDATA[



    $(function() {
      $('#api_provider').on('change', function() {
              alert( this.value );
              if( this.value =='myfatoorah'){
                $('#myfatoorah-div').css('display','block');
                $('#upayment-div').css('display','none');
                $('#bookeey-div').css('display','none');
              }
              if( this.value =='upayment'){
                $('#myfatoorah-div').css('display','none');
                $('#upayment-div').css('display','block');
                $('#bookeey-div').css('display','none');
              }
              if( this.value =='bookeey'){
                $('#myfatoorah-div').css('display','none');
                $('#upayment-div').css('display','none');
                $('#bookeey-div').css('display','block');
              }
              
      }); 
    // Multiple images preview in browser
    var imagesPreview = function(input, placeToInsertImagePreview) {

        if (input.files) {
            var filesAmount = input.files.length;

            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();

                reader.onload = function(event) {
                    $($.parseHTML('<img>')).css({"height":"100px","width":"100px",'margin':'10px'}).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                }

                reader.readAsDataURL(input.files[i]);
            }
        }

    };

    $('#input-file-max-fs').on('change', function() {
        imagesPreview(this, 'div.dropify-preview');
    });
   
});
    var sendSms = function(id){
     var r = confirm("<?=$lang['sms_confirm']?>");
      if (r == true) {
           $.ajax({
				type:'POST',
				url:'moduls/smsAjax.php',
				data: {id:id},
				success:function(res){
                if(res=='ok'){
                    alert("<?=$lang['sms_success']?>");
                 var cancel_url = "<?php echo SITEURL; ?>admin/index.php?page=booking-success";
                  window.location.href = cancel_url;
                 }
				}
			 }); 
      } 
    }
  
  </script>  


<style>
    .datepicker-inline{
       width: 100%;
    }
    .datepicker table {
    margin: 0;
    
    width: 100%;
    }
  </style>
 
</body>
</html>
