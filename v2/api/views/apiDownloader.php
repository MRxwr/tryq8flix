<?php
http_response_code(503);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['ok'=>false,'error'=>['code'=>'integration_pending','msg'=>'The legacy downloader is not enabled in V2.']]);
