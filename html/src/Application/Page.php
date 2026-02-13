<?php
namespace Application;

class Page {
    public function list(array $items): void
    {
        if (count($items) === 0) {
            http_response_code(200);
            echo json_encode(["message" => "No mails yet!"]);
            return;
        }
        http_response_code(200);
        echo json_encode($items);
    }

    public function item($item = null): void
    {
        if ($item === null) {
            $this->notFound();
            return;
        }
        http_response_code(200);
        echo json_encode($item);
    }

    public function post($item) {
        if ($item === null) {
            $this->notFound();
            return;
        }
        http_response_code(201);
        echo json_encode($item);
    }

    public function notFound(): void
    {
        http_response_code(404);
        echo json_encode(["error" => "Not found"]);
    }

    public function badRequest(): void
    {
        http_response_code(400);
        echo json_encode(["error" => "Bad request"]);
    }

    public function deleted() {
    http_response_code(200);
    echo json_encode(["message" => "Item deleted successfully"]);
}

}
