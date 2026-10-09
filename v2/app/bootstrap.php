<?php
declare(strict_types=1);

const V2_ROOT = __DIR__ . '/..';
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', V2_ROOT . '/storage/php-errors.log');
date_default_timezone_set('UTC');

function config(?string $key = null): mixed
{
    static $config;
    if ($config === null) {
        $config = require V2_ROOT . '/config/defaults.php';
        if (is_file(V2_ROOT . '/config/local.php')) {
            $config = array_replace($config, require V2_ROOT . '/config/local.php');
        }
        foreach (['V2_ORIGIN'=>'origin','V2_DB_DSN'=>'db_dsn','V2_DB_USER'=>'db_user','V2_DB_PASSWORD'=>'db_password','V2_ENV'=>'environment','TMDB_TOKEN'=>'tmdb_token'] as $env=>$name) {
            if (getenv($env) !== false) $config[$name] = getenv($env);
        }
    }
    return $key === null ? $config : ($config[$key] ?? null);
}

final class AppError extends RuntimeException
{
    public function __construct(public string $errorCode, string $message, public int $status = 400)
    {
        parent::__construct($message);
    }
}

function db(): PDO
{
    static $pdo;
    if (!$pdo) {
        if (!str_contains((string)config('db_dsn'), 'dbname=tryq8flix_v2') && !str_starts_with((string)config('db_dsn'), 'sqlite:')) {
            throw new AppError('unsafe_database', 'V2 requires an isolated tryq8flix_v2 database.', 503);
        }
        $pdo = new PDO(config('db_dsn'), config('db_user'), config('db_password'), [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false]);
    }
    return $pdo;
}

function query(string $sql, array $params = []): PDOStatement
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function jsonResponse(mixed $data, int $status = 200, ?AppError $error = null): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: private, no-store');
    echo json_encode(['ok'=>$error === null, 'error'=>$error ? ['code'=>$error->errorCode,'msg'=>$error->getMessage()] : null, 'data'=>$error ? ['msg'=>$error->getMessage()] : $data, 'api_version'=>2], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR);
    exit;
}

function securityHeaders(): void
{
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: no-referrer');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' https: data:; connect-src 'self' https:; media-src 'self' https: blob:; frame-src https:; object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'self'");
    header('Cache-Control: private, no-store');
}

function input(string $name, mixed $default = null): mixed
{
    return $_POST[$name] ?? $_GET[$name] ?? $default;
}

function textInput(string $name, int $max = 255, string $default = ''): string
{
    $value = input($name, $default);
    if (!is_string($value) || mb_strlen($value) > $max) throw new AppError('invalid_input', "Invalid {$name}.");
    return trim($value);
}

function positiveInt(string $name, int $default = 1, int $max = 500): int
{
    $value = filter_var(input($name, $default), FILTER_VALIDATE_INT);
    if ($value === false || $value < 1 || $value > $max) throw new AppError('invalid_input', "Invalid {$name}.");
    return $value;
}

function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function logEvent(string $event, array $safeFields = []): void
{
    // Callers must never include cookies, tokens, full URLs, or credentials.
    error_log(json_encode(['event'=>$event,'time'=>gmdate('c')] + $safeFields, JSON_INVALID_UTF8_SUBSTITUTE));
}

function basePath(): string
{
    return rtrim(parse_url((string)config('origin'), PHP_URL_PATH) ?: '', '/');
}

function appUrl(string $path = ''): string
{
    return rtrim((string)config('origin'), '/') . '/' . ltrim($path, '/');
}

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/http.php';
require_once __DIR__ . '/catalog.php';
