<?php
namespace MusicCast\HttpClient\Message;

use Psr\Http\Message\ResponseInterface;

class ResponseMediator
{
    /**
     * @param ResponseInterface $response
     *
     * @return array|string
     */
    public static function getContent(ResponseInterface $response)
    {
        $body = $response->getBody()->__toString();
        if (strpos($response->getHeaderLine('Content-Type'), 'application/json') === 0) {
            $content = json_decode($body, true);
            if (JSON_ERROR_NONE === json_last_error()) {
                return $content;
            }
        }
        return $body;
    }

    /**
     * Get the value for a single header
     * @param ResponseInterface $response
     * @param string $name
     *
     * @return string|null
     */
    public static function getHeader(ResponseInterface $response, $name)
    {
        $headers = $response->getHeader($name);
        return array_shift($headers);
    }
}
