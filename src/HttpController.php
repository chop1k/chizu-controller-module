<?php

namespace Chizu\Controller;

use Chizu\Http\Response\Response;
use InvalidArgumentException;
use JsonException;

/**
 * Class HttpController represents shortcut class for returning responses.
 *
 * @package Chizu\Controller
 */
class HttpController
{
    /**
     * Returns http response with given parameters.
     *
     * @param string $data
     * Response body.
     *
     * @param int $status
     * Response status.
     *
     * @param array $headers
     * Response headers.
     *
     * @return Response
     */
    public static function response(string $data, int $status, array $headers = []): Response
    {
        $response = new Response();

        $response->setStatus($status);
        $response->setBody($data);

        $response->getHeaders()->putAll($headers);

        return $response;
    }

    /**
     * Returns response with file.
     *
     * @param string $path
     * Path to file
     *
     * @param int $length
     * File length.
     *
     * @param int $status
     * Response status.
     *
     * @param array $headers
     * Response headers. If no content-type is set, it will set type returned by mime_content_type.
     *
     * @return Response
     */
    public static function file(string $path, int $length = -1, int $status = 200, array $headers = []): Response
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

        return static::response($file, $status, $headers);
    }

    /**
     * Returns json response.
     *
     * @param $data
     * Response data which will be encoded to json.
     *
     * @param int $status
     * Response status.
     *
     * @param array $headers
     * Response headers. By default sets content-type to application/json.
     *
     * @return Response
     *
     * @throws JsonException
     */
    public static function json($data, int $status, array $headers = []): Response
    {
        $json = json_encode($data);

        if ($json === false)
        {
            throw new JsonException('Cannot convert given data to json');
        }

        $headers['content-type'] = 'application/json';

        return static::response($json, $status, $headers);
    }
}