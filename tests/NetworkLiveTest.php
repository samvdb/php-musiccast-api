<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 30/09/17
 * Time: 12:15
 */

namespace MusicCastTests;

class NetworkLiveTest extends \MusicCastTests\LiveTest
{
    public function testGetSpeakers()
    {
        $speakers = $this->network->getSpeakers();
        self::assertNotNull($speakers);
    }

    public function testGetControllers()
    {
        $controllers = $this->network->getControllers();
        self::assertNotNull($controllers);
    }

    public function testGetController()
    {
        $controller = $this->network->getController();
        self::assertNotNull($controller);
    }

    public function testGetControllerByIp()
    {
        $ip = $this->network->getController()->getDevice()->getIp();
        $controller = $this->network->getControllerByIp($ip);
        self::assertEquals($ip, $controller->getDevice()->getIp());
    }
}
