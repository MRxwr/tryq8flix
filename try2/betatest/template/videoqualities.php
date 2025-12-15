<table style="width: 100%">
	<?php
	/*
	<tr>
	<td style="width:100%" colspan="2">
	<select class="select-css" style="width: 100%" onchange="location = this.options[this.selectedIndex].value;">
        <option value="" >Choose Server:</option>	
		<?php if ( !empty($uptobox) )
		{
			?>
			<option value="watch.php?postid=<?php echo $postid ?>&catid=<?php echo $id ?>&srv=u2b" >Source Quality</option>
			<option value="preparevideo.php?postid=<?php echo $postid ?>&catid=<?php echo $id ?>&srv=lu2b&q=1" >Multiple qualities</option>
			<?php
			$i = 0;
			while ( $i < (sizeof($videoquality)) )
			{
				?>
				<option value="preparevideo.php?postid=<?php echo $postid ?>&catid=<?php echo $id ?>&srv=lu2b&q=<?php echo $i ?>" ><?php echo $videoquality[$i] ?></option>
				<?php
				$i++;
			}
		}
		?>
		<?php if ( !empty($youtube) )
		{
			?>
			<option value="watch.php?postid=<?php echo $postid ?>&catid=<?php echo $id ?>&srv=utb" >YouTube</option>
			<?php
		}
		?>
		<?php if ( !empty($mycima) )
		{
			?>
			<option value="watch.php?postid=<?php echo $postid ?>&catid=<?php echo $id ?>&srv=myc" >Server 2</option>
			<?php
		}
		?>
	</select>
	</td>
	</tr>
	*/
	?>
	<tr>
	<?php
	if ( !isset($_GET["srv"]) OR (isset($_GET["srv"]) AND $_GET["srv"] == "u2b") ){
		?>
		<td style="width:33.33%"><a class="myButton" href="preparevideo.php?postid=<?php echo $postid ?>&catid=<?php echo $id ?>&srv=lu2b&q=1">Multiple Qualities</a>
		</td>
		<?php
	}else{
		?>
		<td style="width:33.33%"><a class="myButton" href="watch.php?postid=<?php echo $postid ?>&catid=<?php echo $id ?>&srv=u2b">Source Quality</a>
		</td>
		<?php
	}
	?>
		<td style="width:33.33%"><a class="myButton" href="request.php?postid=<?php echo $postid ?>&catid=<?php echo $id ?>&q=hq">Request HQ</a>
		</td>
		<td style="width:33.33%"><a class="myButtonred" href="report.php?postid=<?php echo $postid ?>&catid=<?php echo $id ?>">Report</a>
		</td>
	</tr>
</table>