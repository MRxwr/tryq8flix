<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://turboscribe.ai/_htmx/NCN20gAEkZMBzQPXkQc',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{"url":"https://www.youtube.com/watch?v=9oRgazGrYsU"}',
  CURLOPT_HTTPHEADER => array(
    'referer: https://turboscribe.ai/downloader/youtube/video/free',
    'Content-Type: application/json',
    'Cookie: hwm-3frzffekSo3DTuuXXweESsIageR15zup1McuRXdzdHg=1780621711437325967.0000000000; lev=1; session-secret=f307ff605aea104b8aac951dc111e57c9d29; snowflake=HyDFRnjG7o4tt8zXZYnOcw%3D%3D'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
?>