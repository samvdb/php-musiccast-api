<?php
namespace MusicCast\HttpClient\Plugin;

use Http\Client\Common\Plugin;
use MusicCast\Exception\ErrorException;
use MusicCast\Exception\RuntimeException;
use MusicCast\HttpClient\Message\ResponseMediator;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class MusicCastExceptionThrower implements Plugin
{
    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first)
    {
        return $next($request)->then(function (ResponseInterface $response) use ($request) {
            if ($response->getStatusCode() < 400 || $response->getStatusCode() > 600) {
                return $response;
            }

            $content = ResponseMediator::getContent($response);
            if (is_array($content) && isset($content['message'])) {
                if (400 == $response->getStatusCode()) {
                    throw new ErrorException($content['message'], 400);
                }
            }

            throw new RuntimeException(
                isset($content['message']) ? $content['message'] : $content,
                $response->getStatusCode()
            );
        });
    }
}
