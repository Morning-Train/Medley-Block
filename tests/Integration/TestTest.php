<?php

use Yoast\WPTestUtils\BrainMonkey\TestCase;

if (isUnitTest()) {
    return;
}

uses(TestCase::class);

beforeAll(function () {

});

afterAll(function () {

});

test('hello', function () {
    expect('hello')->toBe('hello');
});
