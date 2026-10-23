<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$allowedOrigins = ['https://mahnoor-wasif.netlify.app'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if ($origin !== '' && in_array($origin, $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Vary: Origin');
}
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

function reply(int $status, array $payload): never
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($origin !== '' && !in_array($origin, $allowedOrigins, true)) {
    reply(403, ['error' => 'Origin not allowed']);
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'], true)) {
    reply(405, ['error' => 'Method not allowed']);
}

$configPath = __DIR__ . '/config.php';
if (!is_file($configPath)) {
    reply(503, ['error' => 'Database configuration is missing']);
}
require $configPath;

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $statement = $pdo->query('SELECT name, message, created_at FROM wedding_guestbook ORDER BY id DESC LIMIT 30');
        $comments = array_map(static function (array $row): array {
            return [
                'name' => $row['name'],
                'message' => $row['message'],
                'createdAt' => date('M j, Y', strtotime($row['created_at']))
            ];
        }, $statement->fetchAll());
        reply(200, ['comments' => $comments]);
    }

    $payload = json_decode(file_get_contents('php://input') ?: '', true);
    $name = is_array($payload) ? trim((string)($payload['name'] ?? '')) : '';
    $message = is_array($payload) ? trim((string)($payload['message'] ?? '')) : '';
    $nameLength = function_exists('mb_strlen') ? mb_strlen($name, 'UTF-8') : strlen($name);
    $messageLength = function_exists('mb_strlen') ? mb_strlen($message, 'UTF-8') : strlen($message);

    if ($name === '' || $nameLength > 50 || $message === '' || $messageLength > 500) {
        reply(422, ['error' => 'Enter a name up to 50 characters and a message up to 500 characters']);
    }

    $statement = $pdo->prepare('INSERT INTO wedding_guestbook (name, message) VALUES (:name, :message)');
    $statement->execute(['name' => $name, 'message' => $message]);
    reply(201, ['saved' => true]);
} catch (Throwable $error) {
    error_log('Wedding guestbook API error: ' . $error->getMessage());
    reply(500, ['error' => 'The guestbook is temporarily unavailable']);
}
