<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 12/10/17
 * Time: 19:47
 */

namespace MusicCastTests;

use MusicCast\Client;
use MusicCast\Network;
use Symfony\Component\Yaml\Yaml;

abstract class LiveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Network
     */
    protected $network;

    protected $client;

    protected $options;

    protected function setUp()
    {
        $this->network = new Network();

        if (empty($_ENV["MUSICCAST_LIVE_TESTS"])) {
            $this->markTestSkipped("Ignoring live tests 
            (these can be run setting the MUSICCAST_LIVE_TESTS environment variable)");
            return;
        }

        try {
            $this->network->getSpeakers();
        } catch (\Exception $e) {
            $this->markTestSkipped("No speakers found on the current network");
        }
        $this->options = Yaml::parse(file_get_contents(__DIR__ . '/env.yml'));
        $this->client = new Client($this->options);
    }
}
