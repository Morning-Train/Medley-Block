<?php

use Brain\Monkey;
use Brain\Monkey\Functions;
use MorningMedley\Block\Classes\BlockMetaFileParser;

beforeEach(function () {
    Monkey\setUp();
    Functions\when('wp_json_file_decode')->alias(function ($file) {
        return json_decode(file_get_contents($file), true);
    });
    $this->filesDir = dirname(__FILE__, 2) . "/_files";

});

afterEach(function () {
    Monkey\tearDown();
});

it('can parse single file string', function () {

    $metaFile = $this->filesDir . "/dummies/foo.json";
    $depFile = $this->filesDir . "/dummies/foo.php";

    $parser = new BlockMetaFileParser($metaFile);

    $result = $parser->parseFileString("file:./foo.php");
    expect($result)->toBe($depFile);
});

it('can parse meta file property', function ($property, $firstFile) {

    $metaFile = $this->filesDir . "/blocks/dynamic/block.json";

    $parser = new BlockMetaFileParser($metaFile);

    $result = $parser->parseFileProperty($property);
    expect($result)->toBeArray()->and($result[0])->toContain($firstFile);
})->with([
    ['phpScript', 'foo.php'],
    ['viewPhpScript', 'bar.php'],
    ['editorPhpScript', 'foo.php'],
]);

it('throws an exception when constructing with non-existant file', function () {
    expect(fn() => new BlockMetaFileParser('foo'))->toThrow(\Exception::class);
});

it('returns an empty array when parsing undefined meta property', function ($file, $property) {
    $metaFile = $this->filesDir . $file;

    $parser = new BlockMetaFileParser($metaFile);

    $result = $parser->parseFileProperty($property);
    expect($result)->toBeArray()->toBeEmpty();
})->with([
    ["/blocks/dynamic/block.json", "foo"],
    ["/dummies/foo.json", 'phpScript'],
]);

it('ignores file strings that does not begin with "file:"', function ($string) {
    $parser = new BlockMetaFileParser($this->filesDir . "/dummies/foo.json");
    expect($parser->parseFileString($string))->toBeNull();
})->with([
    'file :.foo',
    'script: foo',
    'https://foo',
    'some/funky/path',
]);

it('ignores file strings that for files that do not exist', function ($string) {
    $parser = new BlockMetaFileParser($this->filesDir . "/dummies/foo.json");
    expect($parser->parseFileString($string))->toBeNull();
})->with([
    'file:.foo',
    'file:somefile',
]);
