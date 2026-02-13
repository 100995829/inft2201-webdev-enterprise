<?php
namespace Application;

use PDO;

class Mail {
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createMail(string $subject = "default_subject", string $body = "default_body"): ?int
    {
        $stmt = $this->pdo->prepare("INSERT INTO mail (subject, body) VALUES (:subject, :body) RETURNING id");
        $stmt->execute(['subject' => $subject, 'body' => $body]);
        $id = $stmt->fetchColumn();
        return $id !== false ? (int)$id : null;
    }

    public function getMail(int $id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM mail WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllMail(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM mail ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateMail(int $id, string $subject, string $body): bool
    {
        $stmt = $this->pdo->prepare("UPDATE mail SET subject = :subject, body = :body WHERE id = :id");
        $stmt->execute(['subject' => $subject, 'body' => $body, 'id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function deleteMail(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM mail WHERE id = :id");

        return $stmt->execute(['id' => $id]);

    }
}
