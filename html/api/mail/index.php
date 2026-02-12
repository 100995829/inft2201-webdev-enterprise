<?php
require '../../../vendor/autoload.php';

use Application\Mail;
use Application\Page;

$dsn = "pgsql:host=" . getenv('DB_PROD_HOST') . ";dbname=" . getenv('DB_PROD_NAME');
try {
    $pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo "Database connection failed: " . $e->getMessage();
    exit;
}

$uri = $_SERVER['REQUEST_URI'];
$parts = explode('/', trim($uri, '/'));
$id = end($parts);

$mail = new Mail($pdo);
$page = new Page();

// GET /api/mail/{id}
if ($_SERVER['REQUEST_METHOD'] === "GET") {
    $item = $mail->getMail($id);
    if ($item) {
        $page->item($item);
    } else {
        $page->notFound();
    }
    exit;
}

// PUT /api/mail/{id}
if ($_SERVER['REQUEST_METHOD'] === "PUT") {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    if (!isset($data['subject']) || !isset($data['body'])) {
        $page->badRequest();
        exit;
    }

    $success = $mail->updateMail($id, $data['subject'], $data['body']);
    if ($success) {
        $page->item(['id' => $id]);
    } else {
        $page->notFound();
    }
    exit;
}

// DELETE /api/mail/{id}
if ($_SERVER['REQUEST_METHOD'] === "DELETE") {
    $success = $mail->deleteMail($id);
    if ($success) {
        $page->deleted();
    } else {
        $page->notFound();
    }
    exit;
}

$page->badRequest();
