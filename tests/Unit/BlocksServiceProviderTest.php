<?php

use Brain\Monkey;
use Brain\Monkey\Functions;
use Morningtrain\WP\Blocks\BlocksServiceProvider;

beforeEach(function () {
    Monkey\setUp();
});

afterEach(function () {
    Monkey\tearDown();
});

it('can register service provider', function () {
    $containerMock = Mockery::mock(\Illuminate\Container\Container::class);
    $provider = new BlocksServiceProvider($containerMock);
    expect($provider->register())->toBeTrue();
});
