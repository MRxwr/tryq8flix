<?php
function sendMail($data){
	$site = $data["site"];
	$subject = $data["subject"];
	$body = $data["body"];
	$from = "noreply@tryq8flix.com";
	$to = $data["to"];
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://createid.link/api/v1/send/notify',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => array(
			'site' => $site,
			'subject' => $subject,
			'body' => $body,
			'from_email' => $from,
			'to_email' => $to
		),
	));
	$response = curl_exec($curl);
	curl_close($curl);
}
?>