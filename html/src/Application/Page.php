<?php
namespace Application;

class Page {
    public function list(array $items): void
    {
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
