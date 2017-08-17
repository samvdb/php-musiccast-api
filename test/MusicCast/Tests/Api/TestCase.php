<?php
/**
 * @author Damien SUROT <damien@toxeek.com>
 */

namespace MusicCast\Tests\Api;

use MusicCast\Client;
use Symfony\Component\Yaml\Yaml;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var []
     */
    protected $options;

    protected $client;


    protected function setUp()
    {
        $this->options = Yaml::parse(file_get_contents(__DIR__ . '/../../../env.yml'));
        $this->client = new Client($this->options);
    }
}
