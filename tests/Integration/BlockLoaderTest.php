<?php

namespace Tests\Integration;

use Morningtrain\WP\Blocks\Blocks;
use Brain\Monkey\Functions;

beforeEach(function () {
    parent::setUp();
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
    parent::tearDown();
    \Brain\Monkey\tearDown();
});

it('can register blocks', function () {
    Functions\when('wp_get_environment_type')->justReturn('development');
    expect(\WP_Block_Type_Registry::get_instance()->is_registered('test/serverside1'))->toBeFalse();
    Blocks::setup(dirname(__FILE__, 2) . "/blocks");
    do_action('init');
    expect(\WP_Block_Type_Registry::get_instance()->is_registered('test/serverside1'))->toBeTrue();
});
