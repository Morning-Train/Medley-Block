<?php

use Brain\Monkey;

beforeEach(function () {
    Monkey\setUp();
});

afterEach(function () {
    Monkey\tearDown();
});

it('can construct', function () {
    expect(new \Morningtrain\WP\Blocks\Classes\BlockLoader())->toBeInstanceOf(\Morningtrain\WP\Blocks\Classes\BlockLoader::class);
});


