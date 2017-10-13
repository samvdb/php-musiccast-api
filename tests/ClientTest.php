<?php

namespace MusicCastTests;

use MusicCast\Client;
use Symfony\Component\Yaml\Yaml;

class ClientTest extends LiveTest
{

    /**
     * @var []
     */
    protected $options;

    /**
     * @test
     */
    public function shouldNotHaveToPassHttpClientToConstructor()
    {
        $client = new Client($this->options);
        self::assertInstanceOf(\Http\Client\HttpClient::class, $client->getHttpClient());
    }

    protected function setUp()
    {
        parent::setUp();
        $this->options = Yaml::parse(file_get_contents(__DIR__ . '/env.yml'));
    }
}
