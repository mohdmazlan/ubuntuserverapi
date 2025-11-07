<?php

declare(strict_types=1);

namespace UbuntuServerAPI\Core;

class Response
{
    private array $headers = ['Content-Type' => 'application/json'];

    public function __construct(
        private array $data = [],
        private int $statusCode = 200
    ) {
    }

    public function setHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        echo json_encode($this->data, JSON_PRETTY_PRINT);
        exit;
    }

    public static function success(array $data = [], string $message = 'Success'): self
    {
        return new self(
            data: [
                'success' => true,
                'message' => $message,
                'data' => $data
            ],
            statusCode: 200
        );
    }

    public static function error(string $message, int $statusCode = 400, array $errors = []): self
    {
        return new self(
            data: [
                'success' => false,
                'message' => $message,
                'errors' => $errors
            ],
            statusCode: $statusCode
        );
    }
}

