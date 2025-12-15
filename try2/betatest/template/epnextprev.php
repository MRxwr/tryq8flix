<table style="width: 100%">
	<tr>
	<?php 
		if ( $previd >= 0 ){ 
			$sql = "SELECT `postId`
					FROM `history`
					WHERE
					`postId` = '".$allids[$previd]."'
					AND
					`userId` = '".$userIds[0]["id"]."'
					";
			$result = $dbconnect->query($sql);
			if ( $result->num_rows == 1 ){
				$preEp = 0;
			}
		?>
		<td style="width: 50%">
			<a class="<?php if ( isset($preEp) ) {echo 'myButtongreen';} else { echo 'myButton'; } ?>" href="watch.php?postid=<?php echo $allids[$previd] ?>&catid=<?php echo $id ?>"><?php echo $prevepidosetitle ?></a>
		</td>
		<?php 
		}
		
		if ( $nextid < sizeof($allids) ){ 
			$sql = "SELECT `postId`
					FROM `history`
					WHERE
					`postId` = '".$allids[$nextid]."'
					AND
					`userId` = '".$userIds[0]["id"]."'
					";
			$result = $dbconnect->query($sql);
			if ( $result->num_rows == 1 ){
				$nextEp = 0;
			}
		?>
		<td style="width: 50%">
			<a class="<?php if ( isset($nextEp) ) {echo 'myButtongreen';} else { echo 'myButton'; } ?>" href="watch.php?postid=<?php echo $allids[$nextid] ?>&catid=<?php echo $id ?>"><?php echo $nextepisodetitle ?></a>
		</td>
		<?php 
		} ?>
	</tr>
</table>