<?php
// db data connection
$servername = "127.0.0.1";
$username = "u905492195_mrnsr";
$password = "N@b$90949089";
$dbname = "u905492195_tryq8";

// Create connection
$dbconnect = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$dbconnect) {
  die("Connection failed: " . mysqli_connect_error());
}
date_default_timezone_set('Asia/Kuwait');
$date = date('Y-m-d H:i:s');

//tokens
$tvdbToken = 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI1MGJjZWE4ZDI5YmNlOTkzZjBiZDJjNzVjOGE0OGVjMiIsIm5iZiI6MTcyMDEzMjA4My4yOSwic3ViIjoiNjY4NzIxZjNhMTM1MjQyZWQ5MjY1ZmZhIiwic2NvcGVzIjpbImFwaV9yZWFkIl0sInZlcnNpb24iOjF9.l66VL660inwA8lNw9mzA7RBOvcWuVi_0f0fmw9FfhIM';
$scrappingBeeToken = "0K5RT5UBE82PSAHTQGPJW0XSFE4AR92XXK9YCXF9H0VHSUZT0P2XJQCDOO2N60S332YVUSRD5T2NDOM3";

//websites
$website = "https://shahiidd4u.net/";
$website2 = "https://web7.topcinema.cloud/";
$website3 = "https://mycima.horse/";
$website4 = "https://p42qg.sbs/";
$website5 = "https://shaheid4u.live/";
$website6 = "https://w6.shahidwbas.tv/";
$website7 = "https://topcinema.zone/";
$website8 = "https://tuk.tuktukarab.cfd/";
$website9 = "https://qesset.net/";
$website10 = "https://ser.q-ask.video/";
$website11 = "https://animeslayerweb.com/";
$website12 = "https://y0vx70khe8u.animepec.online/"; // anime4up
$website13 = "https://egydead.lat/";
$website14 = "https://witanime.you/";
$websiteLive = "https://yallasellit.com/";
$websiteLive2 = "https://beinmatch1.live/";