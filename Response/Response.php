<?php

namespace Response;

class Response
{
    const HTTP_SUCCESS = 200;
    const HTTP_NOT_FOUND = 404;
    const HTTP_SERVER_ERROR = 500;

    protected int $code;
    protected string $content;

    public function __construct(string $content, int $code = Response::HTTP_SUCCESS)
    {
        $this->code = $code;
        $this->content = $content;
    }

    public function setCode(int $code): Response
    {
        $this->code = $code;
        return $this;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function setContent(string $content): Response
    {
        $this->content = $content;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function out()
    {
        http_response_code($this->getCode());
        echo $this->getContent();
    }
}