<?php
$mplayer = ( isset($vlclink) ? $vlclink : "" );
$iosplayer = "https://" . ( isset($vlclink) ? $vlclink : "" );
$mplayer = "intent://".$mplayer."#Intent;scheme=https;action=android.intent.action.VIEW;type=video/avi;end";
?>
<h4 style="color:white">Share On: </h4>
<!-- AddToAny BEGIN -->
<div class="a2a_kit a2a_kit_size_32 a2a_default_style" style="display: flex; justify-content: center;">
<a class="a2a_button_whatsapp"></a>
<a class="a2a_button_twitter"></a>
<a class="a2a_button_facebook"></a>
<a class="a2a_button_sms"></a>
<a class="a2a_button_facebook_messenger"></a>
<a class="a2a_button_skype"></a>
<a class="a2a_button_telegram"></a>
</div>
<script async src="https://static.addtoany.com/menu/page.js"></script>
<!-- AddToAny END -->

<hr>
<table style="width: 100%">
	<tr>
		<td>
			<b style="color: white">VLC Player</b>
		</td>
		<td>
			<b style="color: white">Download</b>
		</td>
	</tr>
	<tr>
		<td style="width: 50%">
			<a class="myButton" href="vlc-x-callback://x-callback-url/stream?url=<?php echo $iosplayer ?>">IOS</a>
		</td>
		<td style="width: 50%">
			<a class="myButton" href="download.php?title=<?php echo $categorytitle .".". $posttitle ?>&dl=<?php echo $downloadlink ?>">Video</a>
		</td>
	</tr>
	<tr>
		<td style="width: 50%">
			<a class="myButton" href="<?php echo $mplayer ?>">Android</a>
		</td>
		<td style="width: 50%">
			<a href="<?php echo $videosubtitle ?>" target="_blank" class="myButton">Subtitle</a>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="width: 100%">
			<a class="myButton">Views: <?php echo $postviews ?></a>
		</td>
	</tr>
</table>