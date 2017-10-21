# Yamaha MusicCast API PHP Library

A php library for interacting with Yamaha MusicCast speakers.

[![Build Status](https://travis-ci.org/grandDam/php-musiccast-api.svg?branch=master)](https://travis-ci.org/grandDam/php-musiccast-api)

Based on the API specification found at [https://jayvee.com.au/downloads/commands/yamaha/YXC_API_Spec_Basic.pdf](https://jayvee.com.au/downloads/commands/yamaha/YXC_API_Spec_Basic.pdf)

Updated API specs on 2017/04:
[https://www.pdf-archive.com/2017/04/21/yxc-api-spec-advanced/yxc-api-spec-advanced.pdf](https://www.pdf-archive.com/2017/04/21/yxc-api-spec-advanced/yxc-api-spec-advanced.pdf)

## Requirements

PHP >= 5.6
Guzzle library,
(optional) PHPUnit to run tests.

## Install

Download [Composer](https://getcomposer.org/)
```bash
$ curl -s http://getcomposer.org/installer | php
```

Via composer
```bash
$ composer require samvdb/php-musiccast-api php-http/guzzle6-adapter
```

You can install any adapter you want but guzzle is probably fine for what you want to do.

## Examples
Start all groups playing music

```php
$musicCast = new \duncan3dc\Sonos\Network;
$controllers = $musicCast->getControllers();
foreach ($controllers as $controller) {
    echo $controller->getGroup()\n";
    echo "\tState: " . $controller->getStateName() . "\n";
    $controller->play();
}
```

## Enabling events

Yamaha can notify you directly in case of changes. The events are spread out as UDP unicast packets.
In order to receive these packets you must subscribe every 10 minutes, else the subscription will expire.

Only the IP that requests the subscription will receive the events.

The default port is `41100`.

```php
$yamaha->api('events')->subscribe();
```

### Using php sockets to read the events

Pretty easy using Clue's socket wrapper.

```bash
$ composer require clue/socket-raw
```

```php
$factory = new \Socket\Raw\Factory();
$socket = $factory->createUdp4();
$socket->bind('0.0.0.0:41100');

while(true) {
    $data = $socket->read(5120);
    $result = json_decode($data, true);
    
    print_r($result);
}
```


## Testing

``` bash
$ composer test
```






