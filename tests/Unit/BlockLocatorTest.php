<?php

use Brain\Monkey;
use Brain\Monkey\Functions;
use Morningtrain\WP\Blocks\Classes\BlockLocator;
use Symfony\Component\Finder\Finder;

beforeEach(function () {
    Monkey\setUp();
    $this->filesDir = dirname(__FILE__, 2) . "/_files";
});

afterEach(function () {
    Monkey\tearDown();
});

it('throws an exception given invalid path', function () {
    $finder = \Mockery::mock(Finder::class);
    $locator = new BlockLocator($finder);
    expect(fn() => $locator->locate(__DIR__ . '/foo'))->toThrow(\Exception::class);
});

it('can locate blocks', function () {
    // TODO: Figure out how to properly mock Finder class
    $locator = new BlockLocator(new Finder());
    $blocks = $locator->locate($this->filesDir . "/blocks");
    expect($blocks)->toBeArray()->and($blocks[0])->toContain('dynamic/block.json');
});
