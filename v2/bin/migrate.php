<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../app/bootstrap.php';
$dsn=(string)config('db_dsn');
if (!preg_match('/^mysql:host=([^;]+);port=(\d+);dbname=tryq8flix_v2;charset=utf8mb4$/D',$dsn,$parts)) throw new RuntimeException('Migration requires the isolated tryq8flix_v2 MySQL database.');
$pdo=new PDO('mysql:host='.$parts[1].';port='.$parts[2].';charset=utf8mb4',config('db_user'),config('db_password'),[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$pdo->exec('CREATE DATABASE IF NOT EXISTS tryq8flix_v2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
$pdo->exec('USE tryq8flix_v2');
foreach(explode(';',file_get_contents(__DIR__.'/../app/schema.sql')) as $sql) if(trim($sql)!=='') $pdo->exec($sql);
echo "V2 schema ready. The reference tryq8flix database was not changed.\n";
