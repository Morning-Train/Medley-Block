<?php

use Brain\Monkey;
use Brain\Monkey\Functions;
use MorningMedley\Block\Classes\Cli;

beforeEach(function () {
    Monkey\setUp();
    $this->filesDir = dirname(__FILE__, 2) . "/_files";
});

afterEach(function () {
    Monkey\tearDown();
    \Mockery::close();
});

it('can call the delete method and handle success', function () {
    $cliMock = \Mockery::mock('alias:\Morningtrain\WP\Facades\Blocks');
    $cliMock->shouldReceive('deleteCache')->andReturn(true);
    $wpCliMock = \Mockery::mock('alias:\WP_CLI');
    $wpCliMock->shouldReceive('success')->once()->with('WP-Blocks cache has been deleted.');

    $cli = new Cli();
    $cli->deleteCache();
});

it('can call the delete method and handle failure', function () {
    $cliMock = \Mockery::mock('alias:\Morningtrain\WP\Facades\Blocks');
    $cliMock->shouldReceive('deleteCache')->andReturn(false);
    $wpCliMock = \Mockery::mock('alias:\WP_CLI');
    $wpCliMock->shouldReceive('error')->once()->with('WP-Blocks cache could not be deleted.');

    $cli = new Cli();
    $cli->deleteCache();
});
