	<h2 style=""><a href="/betatest/flix">Home</a> -> Notifications</h2>

<div class="txtHint">

</div>

<script>
$(document).ready(function(){
	$.ajax({
			url: "templates/notificationResult.php",
			success:function(result){
				$('.txtHint').html(result);
			}
		});
});
</script>
