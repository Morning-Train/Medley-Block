<?php

use Brain\Monkey;
use Brain\Monkey\Functions;
use MorningMedley\Block\ServiceProvider;

beforeEach(function () {
    Monkey\setUp();
});

afterEach(function () {
    Monkey\tearDown();
});

it('can register service provider', function () {
    $containerMock = Mockery::mock(\Illuminate\Container\Container::class);
    $configMock = Mockery::mock(\Illuminate\Config\Repository::class);

    $containerMock->shouldReceive('make')->with('config')->once()->andReturn($configMock);
    $configMock->shouldReceive('set')->andReturn();
    $configMock->shouldReceive('get')->andReturn([]);

    $provider = new ServiceProvider($containerMock);
    $provider->register();
});
