<?php

use Brain\Monkey;

beforeEach(function () {
    Monkey\setUp();
});

afterEach(function () {
    Monkey\tearDown();
});

it('hooks into init on init', function () {
    $blockLoader = new \Morningtrain\WP\Blocks\Classes\BlockLoader();
    $blockLoader->init();
    expect(has_action('init', '\Morningtrain\WP\Blocks\Classes\BlockLoader->loadRegisteredBlocks()'))
        ->not()
        ->toBeFalse();
});

it('it can register a block path', function () {
    $blockLoader = new \Morningtrain\WP\Blocks\Classes\BlockLoader();
    expect($blockLoader->registerBlockPath(dirname(__FILE__, 2) . "/blocks"))->toBeTrue();
});

it('won\'t register an invalid block path', function () {
    $blockLoader = new \Morningtrain\WP\Blocks\Classes\BlockLoader();
    expect(fn() => $blockLoader->registerBlockPath(dirname(__FILE__, 2) . "/invaliddir"))->toThrow(Exception::class);
});

it('won\'t register existing block paths again', function () {
    $blockLoader = new \Morningtrain\WP\Blocks\Classes\BlockLoader();
    expect($blockLoader->registerBlockPath(dirname(__FILE__, 2) . "/blocks"))
        ->toBeTrue()
        ->and($blockLoader->registerBlockPath(dirname(__FILE__, 2) . "/blocks"))
        ->toBeFalse();
});

