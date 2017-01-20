<?php
namespace MusicCast\Tests;

use MusicCast\Client;
use Symfony\Component\Yaml\Yaml;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var []
     */
    protected $options;

    protected function setUp()
    {
        $this->options = Yaml::parse(file_get_contents(__DIR__ . '/../../env.yml'));
    }

    public function test()
    {
        $client = new Client($this->options);
//        var_dump($client->api('system')->functionStatus());die;
//        var_dump($client->api('system')->sendIrCode());
    }

    /**
     * @test
     */
    public function shouldNotHaveToPassHttpClientToConstructor()
    {
        $client = new Client($this->options);

        self::assertInstanceOf(\Http\Client\HttpClient::class, $client->getHttpClient());
    }
}
