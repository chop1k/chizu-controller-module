<?php

namespace Tests;

use Chizu\Controller\HttpController;
use Chizu\DI\Container;
use Chizu\Http\Response\Response;
use Ds\Map;
use Exception;

class TestHttpController extends HttpController
{
    protected Map $context;

    public function __construct(Map $context, Container $container)
    {
        $this->context = $context;
    }

    public function testResponse(string $data, int $status, array $headers): Response
    {
        return $this->response($data, $status, $headers);
    }

    public function testFile(string $path, int $length, int $status, array $headers): Response
    {
        return $this->file($path, $length, $status, $headers);
    }

    public function testJson($data, int $status, array $headers): Response
    {
        return $this->json($data, $status, $headers);
    }

    public function testDependency(Container $container): Response
    {
        return $this->response('test', 200);
    }

    public function test(): Response
    {
        return $this->response('test', 200);
    }

    public function testException(): Response
    {
        throw new Exception('test');
    }
}