<?php
namespace MusicCast\HttpClient\Plugin;

use Http\Client\Common\Plugin;
use MusicCast\Enum\ResponseCodes;
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
            $content = ResponseMediator::getContent($response);
            if (is_array($content) && isset($content['response_code'])) {
                $code = $content['response_code'];

                if ($code === ResponseCodes::SUCCESSFUL_REQUEST) {
                    return $response;
                }

                throw new ErrorException(ResponseCodes::getMessage($code), 400);
            }

            throw new RuntimeException(
                isset($content['message']) ? $content['message'] : $content,
                $response->getStatusCode()
            );
        });
    }
}
