<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 12/10/17
 * Time: 19:50
 */

namespace MusicCastTests;

use Cache\Adapter\Doctrine\DoctrineCachePool;
use Doctrine\Common\Cache\VoidCache;
use Mockery;
use MusicCast\Controller;
use MusicCast\Device;
use MusicCast\Network;
use MusicCast\Speaker;
use Psr\Log\NullLogger;
use ReflectionClass;

abstract class MockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Network
     */
    protected $network;

    private $client;

    public function tearDown()
    {
        Mockery::close();
    }

    public function setUp()
    {
        $this->network = Mockery::mock(Network::class);
        $this->network->shouldReceive("getSpeakers")->andReturn([]);
        $this->client = MockTest::mockCLient();
    }

    private static function mockCLient()
    {
        $client = Mockery::mock("MusicCast\Client");

        $systemApi = Mockery::mock("MusicCast\Api\System");
        MockTest::mockMethod($systemApi, 'system', "getDeviceInfo");
        MockTest::mockMethod($systemApi, 'system', "getDisklavierSettings");
        MockTest::mockMethod($systemApi, 'system', "getFeatures");
        MockTest::mockMethod($systemApi, 'system', "getFuncStatus");
        MockTest::mockMethod($systemApi, 'system', "getLocationInfo");
        MockTest::mockMethod($systemApi, 'system', "getMusicCastTreeInfo");
        MockTest::mockMethod($systemApi, 'system', "getNameText");
        MockTest::mockMethod($systemApi, 'system', "getNetworkStatus");
        MockTest::mockMethod($systemApi, 'system', "getTag");
        MockTest::mockMethod($systemApi, 'system', "isNewFirmwareAvailable");
        MockTest::mockMethod($systemApi, 'system', "sendIrCode");
        $client->shouldReceive("api")->with('system')->andReturn($systemApi);

        $distApi = Mockery::mock("MusicCast\Api\Distribution");
        MockTest::mockMethod($distApi, 'dist', "getDistributionInfo");
        MockTest::mockMethod($distApi, 'dist', "setClientInfo");
        MockTest::mockMethod($distApi, 'dist', "setGroupName");
        MockTest::mockMethod($distApi, 'dist', "setServerInfo");
        MockTest::mockMethod($distApi, 'dist', "startDistribution");
        $client->shouldReceive("api")->with('dist')->andReturn($distApi);

        $zoneApi = Mockery::mock("MusicCast\Api\Zone");
        MockTest::mockMethod($zoneApi, 'zone', "getSignalInfo");
        MockTest::mockMethod($zoneApi, 'zone', "getSoundProgramList");
        MockTest::mockMethod($zoneApi, 'zone', "getStatus");
        MockTest::mockMethod($zoneApi, 'zone', "prepareInputChange");
        MockTest::mockMethod($zoneApi, 'zone', "setInput");
        MockTest::mockMethod($zoneApi, 'zone', "setMute");
        MockTest::mockMethod($zoneApi, 'zone', "setPower");
        MockTest::mockMethod($zoneApi, 'zone', "setSleep");
        MockTest::mockMethod($zoneApi, 'zone', "setVolume");
        $client->shouldReceive("api")->with('zone')->andReturn($zoneApi);
        return $client;
    }

    private static function mockMethod($api, $apiName, $method)
    {
        $api->shouldReceive($method)->andReturn(json_decode(
            file_get_contents(__DIR__ . '/assets/' . $apiName . '/' . $method . '.json'),
            true
        ));
    }

    protected function getController()
    {
        $speaker = $this->getSpeaker();
        return new Controller($speaker, $this->network);
    }

    protected function getSpeaker()
    {
        $speaker = new Speaker($this->getDevice());
        return $speaker;
    }

    protected function getDevice()
    {
        $device = new Device("localhost", 80, new DoctrineCachePool(new VoidCache()), new NullLogger());
        $reflection = new ReflectionClass($device);
        $reflection_property = $reflection->getProperty('client');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($device, $this->client);
        return $device;
    }
}
