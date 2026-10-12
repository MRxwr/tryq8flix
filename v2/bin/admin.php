<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require __DIR__.'/../app/bootstrap.php';
$name=$argv[1] ?? '';
$user=query('SELECT id FROM users WHERE username_small=? AND active=1',[mb_strtolower($name)])->fetch();
if(!$user){fwrite(STDERR,"Usage: php v2/bin/admin.php existing-v2-username\n");exit(1);}
query("UPDATE users SET role='admin' WHERE id=?",[$user['id']]);
echo "Existing V2 account promoted. Legacy users were not modified.\n";
