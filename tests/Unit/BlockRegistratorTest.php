<?php

use Brain\Monkey;
use Brain\Monkey\Functions;
use MorningMedley\Blocks\Classes\BlockRegistrator;

beforeEach(function () {
    Monkey\setUp();
    $this->filesDir = dirname(__FILE__, 2) . "/_files";
    $this->registrator = new BlockRegistrator();
});

afterEach(function () {
    Monkey\tearDown();
});

it('wraps wordpress boolean functions properly', function ($function, $wpFunction, $value) {
    Functions\when($wpFunction)->justReturn($value);
    expect($this->registrator->{$function}())->toBe($value);
})->with([
    ['isJsonRequest', 'wp_is_json_request', true],
    ['isJsonRequest', 'wp_is_json_request', false],
    ['shouldLoadBlockEditorScriptsAndStyles', 'wp_should_load_block_editor_scripts_and_styles', true],
    ['shouldLoadBlockEditorScriptsAndStyles', 'wp_should_load_block_editor_scripts_and_styles', false],
    ['isAdmin', 'is_admin', true],
    ['isAdmin', 'is_admin', false],
]);

it('loads block dependencies dependently',
    function ($blockArgs, $isJsonRequest, $shouldLoadBlockEditorScriptsAndStyles, $isAdmin, $expectedOutput) {
        Functions\when('wp_is_json_request')->justReturn($isJsonRequest);
        Functions\when('wp_should_load_block_editor_scripts_and_styles')->justReturn($shouldLoadBlockEditorScriptsAndStyles);
        Functions\when('is_admin')->justReturn($isAdmin);

        $block = array_merge([
            'metaFile' => $this->filesDir . "/dummies/block.json",
        ], $blockArgs);

        foreach (['phpScript', 'viewPhpScript', 'editorPhpScript'] as $prop) {
            if (isset($block[$prop])) {
                $block[$prop] = array_map(fn($s) => $this->filesDir . $s, $block[$prop]);
            }
        }

        ob_start();
        $this->registrator->loadBlockDependencies($block);
        $output = ob_get_clean();

        expect($output)->toBe($expectedOutput);
    })
    ->with([
        [["phpScript" => ["/dummies/foo.php"]], false, false, false, "foo"],
        [["viewPhpScript" => ["/dummies/foo.php"]], false, false, false, "foo"],
        [["editorPhpScript" => ["/dummies/foo.php"]], true, true, true, "foo"],
        [["phpScript" => ["/dummies/foo.php", "/dummies/bar.php"]], false, false, false, "foobar"],
        [
            [
                "phpScript" => ["/dummies/foo.php"],
                "viewPhpScript" => ["/dummies/bar.php"],
                "editorPhpScript" => ["/dummies/baz.php"],
            ],
            true,
            true,
            true,
            "foobaz",
        ],
        [
            [
                "phpScript" => ["/dummies/foo.php"],
                "viewPhpScript" => ["/dummies/bar.php"],
                "editorPhpScript" => ["/dummies/baz.php"],
            ],
            false,
            false,
            false,
            "foobar",
        ],
    ]);

it('can register a block', function ($block) {
    Functions\when('wp_is_json_request')->justReturn(false);
    Functions\when('wp_should_load_block_editor_scripts_and_styles')->justReturn(false);
    Functions\when('is_admin')->justReturn(false);

    if (is_string($block)) {
        $block = $this->filesDir . $block;
    } elseif (is_array($block) && is_string($block['metaFile'])) {
        $block['metaFile'] = $this->filesDir . $block['metaFile'];
    }

    $expectedArg = is_string($block) ? $block : $block['metaFile'];
    Functions\expect('register_block_type')
        ->once()
        ->with($expectedArg);

    $this->registrator->registerBlock($block);
})->with([
    '/dummies/foo.json',
    [['metaFile' => '/dummies/foo.json']],
]);

it('throws exception when metaFile is not a file', function ($block) {
    expect(fn() => $this->registrator->registerBlock($block))->toThrow(\Exception::class);
})->with(['bar/foo', '']);

it('can register multiple blocks', function () {
    Functions\when('wp_is_json_request')->justReturn(false);
    Functions\when('wp_should_load_block_editor_scripts_and_styles')->justReturn(false);
    Functions\when('is_admin')->justReturn(false);

    Functions\expect('register_block_type')
        ->times(3);

    $file = $this->filesDir . "/dummies/foo.json";

    $this->registrator->registerBlocks([
        $file,
        ['metaFile' => $file],
        $file,
    ]);
});
