<?php
if ( !isset($_POST["page"]) || (isset($_POST["page"]) && $_POST["page"] == "home") ){
	$output = '<div class="row w-100 p-0 m-0">
		  <div class="col-12 p-2 pb-1 type" id="movie">
			<image src="https://i.imgur.com/Ym7K5ST.png" class="rounded" style="width:100%; height:150px">
		  </div>
		  <div class="col-12 p-2 pb-1 type" id="tv-show">
			<image src="https://i.imgur.com/nvLsNZ3.png" class="rounded" style="width:100%; height:150px">
		  </div>
		  <div class="col-12 p-2 pb-1 type" id="anime">
			<image src="https://i.imgur.com/Ep0cgq5.png" class="rounded" style="width:100%; height:150px">
		  </div>
		  <div class="col-12 p-2 pb-1 type" id="animov">
			<image src="https://i.imgur.com/gXLvA9l.png" class="rounded" style="width:100%; height:150px">
		  </div>
		  <div class="col-12 p-2 pb-1 type" id="wrestling">
			<image src="https://i.imgur.com/OUcqYhu.png" class="rounded" style="width:100%; height:150px">
		  </div>
		</div>
		';
}else{
	if ( $_POST["page"] == "likes" ){
		
	}elseif( $_POST["page"] == "watching" ){
		
	}elseif( $_POST["page"] == "profile" ){
		
	}
}
echo $output;

?>