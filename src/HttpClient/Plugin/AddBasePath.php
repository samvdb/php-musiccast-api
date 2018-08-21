<?php

namespace MusicCast\HttpClient\Plugin;

use Http\Client\Common\Plugin;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddBasePath implements Plugin
{
    /**
     * @var string
     */
    protected $version;

    /**
     * @param array        $config
     */
    public function __construct(array $config = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $options = $resolver->resolve($config);

        $this->version = $options['version'];
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first)
    {
        $path = $request->getUri()->getPath();

        $uri = $request->getUri()
                ->withPath(sprintf('/YamahaExtendedControl/%s', $this->version) . $path)
            ;

        $request = $request->withUri($uri);


        return $next($request);
    }

    /**
     * @param OptionsResolver $resolver
     */
    private function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['version']);
        $resolver->setAllowedTypes('version', 'string');
    }
}
