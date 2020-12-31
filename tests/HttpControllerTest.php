<?php

namespace Tests;

use Chizu\DI\Container;
use Ds\Map;
use PHPUnit\Framework\TestCase;

class HttpControllerTest extends TestCase
{
    protected TestHttpController $controller;

    protected function setUp(): void
    {
        $this->controller = new TestHttpController(new Map(), new Container());
    }

    public function getResponses(): array
    {
        return [
            ['ok', 'ok', 200, []],
            ['ok', 'ok', 500, ['ok'=>'ko']]
        ];
    }

    /**
     * @dataProvider getResponses
     *
     * @param string $data
     * @param string $expected
     * @param int $status
     * @param array $headers
     */
    public function testResponse(string $data, string $expected, int $status, array $headers): void
    {
        $response = $this->controller->testResponse($data, $status, $headers);

        self::assertEquals($status, $response->getStatus());
        self::assertEquals($expected, $response->getBody());
        self::assertEquals($headers, $response->getHeaders()->toArray());
    }

    public function getFiles(): array
    {
        $fileHeader = ['content-type' => 'text/plain'];

        $lorem = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum' . PHP_EOL;

        $file = '/tmp/test.txt';

        return [
            [$file, 'Lorem', 5, 200, $fileHeader],
            [$file, $lorem, -1, 200, $fileHeader]
        ];
    }

    /**
     * @dataProvider getFiles
     *
     * @param string $path
     * @param string $expected
     * @param int $length
     * @param int $status
     * @param array $headers
     */
    public function testFile(string $path, string $expected, int $length, int $status, array $headers): void
    {
        $response = $this->controller->testFile($path, $length, $status, $headers);

        self::assertEquals($expected, $response->getBody());

        if ($length >= 0)
        {
            self::assertEquals($length, strlen($response->getBody()));
        }

        self::assertEquals($headers, $response->getHeaders()->toArray());
    }

    public function getJson(): array
    {
        $testObject = new class() {

        };

        $testObject2 = new class() {
            public string $ok = 'ko';
        };

        $jsonHeader = ['content-type' => 'application/json'];

        return [
            [$testObject, '{}', 200, $jsonHeader],
            [[], '[]', 200, $jsonHeader],
            [$testObject2, '{"ok":"ko"}', 500, $jsonHeader]
        ];
    }

    /**
     * @dataProvider getJson
     *
     * @param $data
     * @param string $expected
     * @param int $status
     * @param array $headers
     */
    public function testJson($data, string $expected, int $status, array $headers): void
    {
        $response = $this->controller->testJson($data, $status, $headers);

        self::assertEquals($expected, $response->getBody());
        self::assertEquals($status, $response->getStatus());
        self::assertEquals($headers, $response->getHeaders()->toArray());
    }
}