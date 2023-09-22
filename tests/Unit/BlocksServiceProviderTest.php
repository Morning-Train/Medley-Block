<?php

use Brain\Monkey;
use Brain\Monkey\Functions;
use Morningtrain\WP\Blocks\ServiceProvider;

beforeEach(function () {
    Monkey\setUp();
});

afterEach(function () {
    Monkey\tearDown();
});

it('can register service provider', function () {
    $containerMock = Mockery::mock(\Illuminate\Container\Container::class);
    $provider = new ServiceProvider($containerMock);
    expect($provider->register())->toBeTrue();
});
