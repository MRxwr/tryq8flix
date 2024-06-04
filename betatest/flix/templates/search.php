<h2 style=""><a href="/betatest/flix">Home</a> -> Search</h2>
<input id="search" class="form-control" type="text" value="" autofocus />

<div id="txtHint">Please type something to search for.</div>

<script>
$(document).ready(function(){
	
<?php
if ( isset($_GET["query"]) ){
	?>
	$.ajax({
		url:"templates/searchResult.php",
		method:"GET",
		data:{
			query:'<?php echo $_GET["query"] ?>',
			cast:1,
			},
		success:function(data){
			$('#txtHint').html(data);
			$('#search').val('<?php echo $_GET["query"] ?>');
		}
	});
	<?php
}
?>
	$('#search').keyup(function(){
		var search = $(this).val();
		console.log(search);
		if( search != '' ){
			$.ajax({
				url:"templates/searchResult.php",
				method:"GET",
				data:{
					query:search
					},
				success:function(data){
					$('#txtHint').html(data);
				}
			});
		}else{
			$('#txtHint').html('Please type something to search for.');
		}
	});
});
</script>