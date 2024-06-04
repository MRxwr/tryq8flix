<?php

$body = '
	<table style="width:100%">
		<tr>
		<td colspan="2" style="text-align:center"><img src="https://i.imgur.com/M1IalQW.png" style="width:100px; height:100px"></td>
		</tr>
		<tr>
		<td colspan="2">
		You have a new order #000111<br>
		Name: NASER HATAB<br>
		Mobile: 96590949089<br>
		Address: Adan, Block 4,Street 10, House 51<br><br>
		Delivery Date: 12/07/2022<br>
		Delivery Time: 08:00 - 10-00
		</td>
		</tr>
		<tr>
		<td><hr>Item<hr></td>
		<td><hr>Price<hr></td>
		</tr>
		<tr>
		<td>1x Item 0</td>
		<td>25.00 KD</td>
		</tr>
		<tr>
		<td>2x Item 1</td>
		<td>50.00 KD</td>
		</tr>
		<tr>
		<td><hr>Total<hr></td>
		<td><hr>75.00 KD<hr></td>
		</tr>
	</table>
	';
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'myid.createkwservers.com/api/v1/send/notify',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array('site' => 'Create-kw.com','subject' => '#000111','body' => $body,'from_email' => 'noreply@create-kw.com','to_email' => 'nasserhatab@gmail.com'),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

?>