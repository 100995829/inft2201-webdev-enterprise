<?php
require __DIR__ . '/../../../autoload.php';

use Application\Mail;
use Application\Database;
use Application\Page;
use Application\Verifier;

$database = new Database('prod');
$page = new Page();
$mail = new Mail($database->getDb());

//  AUTHORIZATION CHECK
if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
    http_response_code(401);
    echo json_encode(["error" => "Missing Authorization header"]);
    exit;
}

$authHeader = $_SERVER['HTTP_AUTHORIZATION'];

// Expect: Bearer <token>
$parts = explode(" ", $authHeader);

if (count($parts) !== 2 || $parts[0] !== "Bearer") {
    http_response_code(401);
    echo json_encode(["error" => "Invalid Authorization format"]);
    exit;
}

$token = $parts[1];

//  VERIFY TOKEN
try {
    $verifier = new Verifier();
    $verifier->decode($authHeader);

    $userId = $verifier->userId;
    $role = $verifier->role;

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid token"]);
    exit;
}

//  POST /api/mail/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['name']) || !isset($data['message'])) {
        $page->badRequest();
        exit;
    }

    // RBAC logic
    if ($role === "admin") {
        // admin can set userId or default to self
        $insertUserId = $data['userId'] ?? $userId;
    } else {
        // normal user → force their own ID
        $insertUserId = $userId;
    }

    $id = $mail->createMail($data['name'], $data['message'], $insertUserId);

    $page->item(["id" => $id]);
    exit;
}

//  GET /api/mail/
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if ($role === "admin") {
        // Admin → all mail
        $result = $mail->listMail();
    } else {
        // User → only their mail
        $result = $mail->listMailByUserId($userId);
    }

    $page->item($result);
    exit;
}

$page->badRequest();
