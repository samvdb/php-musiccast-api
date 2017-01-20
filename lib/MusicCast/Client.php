<?php
namespace MusicCast;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\StreamFactoryDiscovery;
use Http\Client\Common\Plugin;
use Http\Discovery\UriFactoryDiscovery;
use Http\Message\MessageFactory;
use Http\Message\StreamFactory;
use MusicCast\Api\ApiInterface;
use MusicCast\Exception\BadMethodCallException;
use MusicCast\Exception\InvalidArgumentException;
use MusicCast\HttpClient\Plugin\History;
use MusicCast\HttpClient\Plugin\MusicCastExceptionThrower;
use Symfony\Component\OptionsResolver\OptionsResolver;
use MusicCast\HttpClient\Plugin\AddBasePath;

class Client
{
    /**
     * @var string
     */
    private $apiVersion;

    /**
     * The object that sends HTTP messages
     *
     * @var HttpClient
     */
    private $httpClient;
    /**
     * A HTTP client with all our plugins
     *
     * @var PluginClient
     */
    private $pluginClient;
    /**
     * @var MessageFactory
     */
    private $messageFactory;
    /**
     * @var StreamFactory
     */
    private $streamFactory;
    /**
     * @var Plugin[]
     */
    private $plugins = [];
    /**
     * True if we should create a new Plugin client at next request.
     * @var bool
     */
    private $httpClientModified = true;
    /**
     * Http headers
     * @var array
     */
    private $headers = [];
    /**
     * @var History
     */
    private $responseHistory;

    /**
     * Store options
     *
     * @var array
     */
    private $options = [];

    /**
     * Instantiate a new Yamaha MusicCast client.
     *
     * @param array $options
     * @param HttpClient|null $httpClient
     * @param string|null $apiVersion
     */
    public function __construct($options = [], HttpClient $httpClient = null, $apiVersion = null)
    {
        $this->configureOptions((array)$options);

        $this->httpClient = $httpClient ?: HttpClientDiscovery::find();
        $this->messageFactory = MessageFactoryDiscovery::find();
        $this->streamFactory = StreamFactoryDiscovery::find();
        $this->responseHistory = new History();
        $this->apiVersion = $apiVersion ?: 'v1';
        $this->addPlugin(new MusicCastExceptionThrower());
        $this->addPlugin(new Plugin\HistoryPlugin($this->responseHistory));
        $this->addPlugin(new Plugin\RedirectPlugin());
        $this->addPlugin(
            new Plugin\AddHostPlugin(
                UriFactoryDiscovery::find()->createUri($this->options['base_url'])
            )
        );
        $this->addPlugin(new AddBasePath(['version' => $this->apiVersion]));
        $this->addPlugin(
            new Plugin\HeaderDefaultsPlugin(
                [
                    'User-Agent' => 'php-yamaha-api',
                ]
            )
        );


        $this->addHeaders(['Accept' =>  sprintf('application/vnd.musiccast.%s+json', $this->getApiVersion())]);
    }

    /**
     * @param string $name
     *
     * @throws InvalidArgumentException
     *
     * @return ApiInterface
     */
    public function api($name)
    {
        switch (strtolower($name)) {
            case 'zone':
                $api = new Api\Zone($this);
                break;
            case 'system':
                $api = new Api\System($this);
                break;
            case 'tuner':
                $api = new Api\Tuner($this);
                break;
            case 'network':
                $api = new Api\Network($this);
                break;
            case 'usb':
                $api = new Api\Usb($this);
                break;
            case 'cd':
                $api = new Api\CD($this);
                break;
            case 'events':
                $api = new Api\Event($this);
                break;
            default:
                throw new InvalidArgumentException(sprintf('Undefined api instance called: "%s"', $name));
        }

        return $api;
    }

    /**
     * @return string
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * Add a new plugin to the end of the plugin chain.
     *
     * @param Plugin $plugin
     */
    public function addPlugin(Plugin $plugin)
    {
        $this->plugins[] = $plugin;
        $this->httpClientModified = true;
    }

    /**
     * Remove a plugin by its fully qualified class name (FQCN).
     *
     * @param string $fqcn
     */
    public function removePlugin($fqcn)
    {
        foreach ($this->plugins as $idx => $plugin) {
            if ($plugin instanceof $fqcn) {
                unset($this->plugins[$idx]);
                $this->httpClientModified = true;
            }
        }
    }


    /**
     * You can use the Http client directly to perform your calls
     *
     * @return HttpMethodsClient
     */
    public function getHttpClient()
    {
        if ($this->httpClientModified) {
            $this->httpClientModified = false;
            $this->pluginClient = new HttpMethodsClient(
                new PluginClient($this->httpClient, $this->plugins),
                $this->messageFactory
            );
        }

        return $this->pluginClient;
    }

    /**
     * Clears used headers.
     */
    public function clearHeaders()
    {
        $this->headers = [
            'Accept' => sprintf('application/vnd.musiccast.%s+json', $this->getApiVersion()),
        ];
        $this->removePlugin(Plugin\HeaderAppendPlugin::class);
        $this->addPlugin(new Plugin\HeaderAppendPlugin($this->headers));
    }

    /**
     * @param array $headers
     */
    public function addHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
        $this->removePlugin(Plugin\HeaderAppendPlugin::class);
        $this->addPlugin(new Plugin\HeaderAppendPlugin($this->headers));
    }

    /**
     * @param HttpClient $httpClient
     */
    public function setHttpClient(HttpClient $httpClient)
    {
        $this->httpClientModified = true;
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $name
     *
     * @throws BadMethodCallException
     *
     * @return ApiInterface
     */
    public function __call($name, $args)
    {
        try {
            return $this->api($name);
        } catch (InvalidArgumentException $e) {
            throw new BadMethodCallException(sprintf('Undefined method called: "%s"', $name));
        }
    }

    /**
     *
     * @return null|\Psr\Http\Message\ResponseInterface
     */
    public function getLastResponse()
    {
        return $this->responseHistory->getLastResponse();
    }

    private function configureOptions($options = [])
    {
        $resolver = new OptionsResolver();

        $resolver->setDefaults(
            [

                'port' => 80,

            ]
        );

        $resolver->setRequired(
            [
                'host',
                'port',
            ]
        );

        $resolver->setAllowedTypes('host', ['string']);
        $resolver->setAllowedTypes('port', ['integer']);

        $this->options = $resolver->resolve($options);
        $this->postResolve($options);

        return $this->options;
    }

    /**
     * Post resolve
     *
     * @param array $options
     */
    protected function postResolve(array $options = [])
    {
        $this->options['base_url'] = sprintf(
            'http://%s:%s',
            $this->options['host'],
            $this->options['port']
        );
    }
}
