<?php
use PHPUnit\Framework\TestCase;
use Application\Mail;

class MailTest extends TestCase {
    protected PDO $pdo;

    protected function setUp(): void
    {
        $dsn = "pgsql:host=" . getenv('DB_TEST_HOST') . ";dbname=" . getenv('DB_TEST_NAME');
        $this->pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'));
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Reset table before each test
        $this->pdo->exec("DROP TABLE IF EXISTS mail;");
        $this->pdo->exec("
            CREATE TABLE mail (
                id SERIAL PRIMARY KEY,
                subject TEXT NOT NULL,
                body TEXT NOT NULL
            );
        ");
    }

    public function testCreateMail() {
        $mail = new Mail($this->pdo);
        $id = $mail->createMail("Welcome", "Hello");
        $this->assertIsInt($id);
        $this->assertEquals(1, $id);

        $mail->createMail("Prem Patel", "Message");
        $stmt = $this->pdo->query("SELECT * FROM mail WHERE id = 2");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals("Prem Patel", $row['subject']);
        $this->assertEquals("Message", $row['body']);
    }

    // chat gpt
    public function testGetMail() {
        $mail = new Mail($this->pdo);
        $id = $mail->createMail("Test", "Content");

        $result = $mail->getMail($id);
        $this->assertEquals("Test", $result[0]['subject']);
        $this->assertEquals("Content", $result[0]['body']);
    }

    public function testListMail() {
        $mail = new Mail($this->pdo);
        $mail->createMail("Mail A", "Body A");
        $mail->createMail("Mail B", "Body B");

        $list = $mail->getAllMail();
        $this->assertIsArray($list);
        $this->assertCount(2, $list);
        $this->assertEquals("Mail A", $list[0]['subject']);
        $this->assertEquals("Mail B", $list[1]['subject']);
    }


    // chat gpt
    public function testUpdateMail() {
        $mail = new Mail($this->pdo);
        $id = $mail->createMail("Old", "Data");

        $rowsAffected = $mail->updateMail($id, "Updated", "Body");
        $this->assertEquals(1, $rowsAffected);

        $result = $mail->getMail($id);
        $this->assertEquals("Updated", $result[0]['subject']);
        $this->assertEquals("Body", $result[0]['body']);
    }

    public function testDeleteMail() {
        $mail = new Mail($this->pdo);
        $id = $mail->createMail("To Delete", "Me");

        $rowsAffected = $mail->deleteMail($id);
        $this->assertTrue($rowsAffected);

        $result = $mail->getMail($id);
        $this->assertCount(0, $result);    
    }
}