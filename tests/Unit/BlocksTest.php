<?php

use Brain\Monkey;

beforeAll(function () {
    Monkey\setUp();
});

afterAll(function () {
    Monkey\tearDown();
});

it('has Block main class', function () {
    expect(\Morningtrain\WP\Blocks\Blocks::class)->toBeString();
});

it('can setup', function () {
    \Morningtrain\WP\Blocks\Blocks::setup(dirname(__DIR__) . "/blocks");
    expect(\Morningtrain\WP\Blocks\Blocks::isInit())->toBeTrue();
    expect(\Morningtrain\WP\Blocks\Blocks::getBlockService())->toBeInstanceOf(\Morningtrain\WP\Blocks\Classes\Service::class);
    expect(\Morningtrain\WP\Blocks\Blocks::getBlockLoader())->toBeInstanceOf(\Morningtrain\WP\Blocks\Classes\BlockLoader::class);
});

it('throws on invalid path', function () {
    expect(fn() => \Morningtrain\WP\Blocks\Blocks::registerBlockDirectory(__DIR__ . "/directorythatdoesnotexist")
    )->toThrow(Exception::class);
});

it('can register additional block directories', function () {
    expect(\Morningtrain\WP\Blocks\Blocks::registerBlockDirectory(dirname(__DIR__) . "/blocks2"))->toBeTrue();
    expect(\Morningtrain\WP\Blocks\Blocks::registerBlockDirectory(dirname(__DIR__) . "/blocks2"))->toBeFalse();
});
