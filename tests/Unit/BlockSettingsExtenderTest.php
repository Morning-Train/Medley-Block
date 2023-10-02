<?php

use Brain\Monkey;
use Brain\Monkey\Functions;
use MorningMedley\Block\Classes\BlockSettingsExtender;

beforeEach(function () {
    Monkey\setUp();
    Functions\when('get_block_wrapper_attributes')->justReturn([]);
    $this->filesDir = dirname(__FILE__, 2) . "/_files";
});

afterEach(function () {
    Monkey\tearDown();
    \Mockery::close();
});

it('does nothing if renderView property is not set', function () {
    \Mockery::mock('alias:\Morningtrain\WP\View\View');
    $extender = new BlockSettingsExtender();
    $settings = [
        'render_callback' => 'foo',
    ];
    $metaData = [];
    $updatedSettings = $extender->allowViewRenderInBlockMeta($settings, $metaData);
    expect($updatedSettings)->toBeArray()->and($updatedSettings['render_callback'])->toBe('foo');
});

it('does nothing if wp-view is not loaded', function () {
    // If this test is not run before Mockery mocks the View class then this will not test line 10 : (
    $extender = new BlockSettingsExtender();
    $settings = [
        'render_callback' => 'foo',
    ];
    $metaData = [];
    $updatedSettings = $extender->allowViewRenderInBlockMeta($settings, $metaData);
    expect($updatedSettings)->toBeArray()->and($updatedSettings['render_callback'])->toBe('foo');
});

it('overrides render_callback when renderView is defined', function () {
    \Mockery::mock('alias:\Morningtrain\WP\View\View');
    $extender = new BlockSettingsExtender();
    $settings = [

    ];
    $metaData = [
        'renderView' => 'foo',
    ];
    $updatedSettings = $extender->allowViewRenderInBlockMeta($settings, $metaData);
    expect($updatedSettings)->toBeArray()->and($updatedSettings['render_callback'])->toBeCallable();
});

it('can render a dynamic block', function () {
    Functions\when('get_block_wrapper_attributes')->justReturn('props');
    $viewMock = \Mockery::mock('alias:\Morningtrain\WP\View\View');
    $viewMock->shouldReceive('render')->with('foo', [
        'attributes' => ['foo' => 'bar'],
        'content' => '',
        'block' => 'test/test',
        'blockProps' => 'props',
    ])->andReturn('output');

    $extender = new BlockSettingsExtender();
    $settings = [

    ];
    $metaData = [
        'renderView' => 'foo',
    ];
    $updatedSettings = $extender->allowViewRenderInBlockMeta($settings, $metaData);
    expect($updatedSettings['render_callback'](['foo' => 'bar'], '', 'test/test'))->toBe('output');
});


