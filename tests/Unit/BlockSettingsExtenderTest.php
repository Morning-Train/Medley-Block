<?php

use Brain\Monkey;
use Brain\Monkey\Functions;
use Morningtrain\WP\Blocks\Classes\BlockSettingsExtender;

beforeAll(function () {
    Monkey\setUp();
    Functions\when('get_block_wrapper_attributes')->justReturn([]);
});

beforeEach(function () {
    $this->filesDir = dirname(__FILE__, 2) . "/_files";
});

afterAll(function () {
    Monkey\tearDown();
});

it('does nothing if renderView property is not set', function () {
    $extender = new BlockSettingsExtender();
    $settings = [
        'render_callback' => 'foo',
    ];
    $metaData = [];
    $updatedSettings = $extender->allowViewRenderInBlockMeta($settings, $metaData);
    expect($updatedSettings)->toBeArray()->and($updatedSettings['render_callback'])->toBe('foo');
});

it('overrides render_callback when renderView is defined', function () {
    $extender = new BlockSettingsExtender();
    $settings = [

    ];
    $metaData = [
        'renderView' => 'foo',
    ];
    $updatedSettings = $extender->allowViewRenderInBlockMeta($settings, $metaData);
    expect($updatedSettings)->toBeArray()->and($updatedSettings['render_callback'])->toBeCallable();
});


