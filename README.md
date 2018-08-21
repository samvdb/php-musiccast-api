# Yamaha MusicCast API PHP Library

# DEPRECATED!!!!
grandDam improved this library alot, it includes tests and more functionality.

Use [this fork](https://github.com/grandDam/php-musiccast-api) Library instead!

```sh
composer require granddam/php-musiccast-api
```

# DEPRECATED!!!!

A simple wrapper for the Yamaha MusicCast API.
Not all call's have been implemented yet so pull requests are welcome!

[![Build Status](https://travis-ci.org/samvdb/php-musiccast-api.svg?branch=master)](https://travis-ci.org/samvdb/php-musiccast-api)

Based on the API specification found at [https://jayvee.com.au/downloads/commands/yamaha/YXC_API_Spec_Basic.pdf](https://jayvee.com.au/downloads/commands/yamaha/YXC_API_Spec_Basic.pdf)

Updated API specs on 2017/04:
[https://www.pdf-archive.com/2017/04/21/yxc-api-spec-advanced/yxc-api-spec-advanced.pdf](https://www.pdf-archive.com/2017/04/21/yxc-api-spec-advanced/yxc-api-spec-advanced.pdf)

### Update
The API pdf's can also be found in this repository.

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

## Creating a client

```php
$yamaha = new MusicCast\Client([
    'host' => 'localhost',
    'port' => 80, // default value
]);
```

## Using the API


```php
$result = $yamaha->api('zone')->status('main');
print_r($result);

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

## Credits

This api is highly inspired by the excellent Github api client made by KnpLabs!

[KnpLabs/php-github-api](https://github.com/KnpLabs/php-github-api)





