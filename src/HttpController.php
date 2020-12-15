<?php

namespace Chizu\Controller;

use Chizu\Http\Response\Response;
use InvalidArgumentException;
use JsonException;

class HttpController
{
    protected function response(string $data, int $status, array $headers = []): Response
    {
        $response = new Response();

        $response->setStatus($status);
        $response->setBody($data);

        $response->getHeaders()->putAll($headers);

        return $response;
    }

    protected function file(string $path, int $length = -1, int $status = 200, array $headers = []): Response
    {
        if ($length >= 0)
        {
            $file = file_get_contents($path, false , null, 0, $length);
        }
        else
        {
            $file = file_get_contents($path);
        }

        if ($file === false)
        {
            throw new InvalidArgumentException(sprintf('Cannot access file with path "%s"', $path));
        }

        if (!isset($headers['content-type']))
        {
            $headers['content-type'] = mime_content_type($path);
        }

        return $this->response($file, $status, $headers);
    }

    protected function json($data, int $status, array $headers = []): Response
    {
        $json = json_encode($data);

        if ($json === false)
        {
            throw new JsonException('Cannot convert given data to json');
        }

        $headers['content-type'] = 'application/json';

        return $this->response($json, $status, $headers);
    }
}