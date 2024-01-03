<?php

use Brain\Monkey;
use Brain\Monkey\Functions;
use MorningMedley\Block\Classes\Block;
use MorningMedley\Block\Classes\BlockSettingsExtender;
use MorningMedley\Block\Classes\Cli;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;

beforeEach(function () {
    Monkey\setUp();
    $this->filesDir = dirname(__FILE__, 2) . "/_files";
});

afterEach(function () {
    Monkey\tearDown();
    \Mockery::close();
});

function getBlockInstance($args = [])
{
    Functions\expect('add_action')->once();

    $container = $args['container'] ?? Mockery::mock(\Illuminate\Container\Container::class);
    $container->shouldReceive('make')->with(Cli::class);

    $cache = $args['cache'] ?? \Mockery::mock(PhpFilesAdapter::class);
    $registrator = $args['registrator'] ?? \Mockery::mock(\MorningMedley\Block\Classes\BlockRegistrator::class);

    return new Block($container, $registrator, $cache);
}

it('can construct', function () {
    getBlockInstance();
});

it('can register a blocks path', function () {
    $blocks = getBlockInstance();
    $blocks->registerBlocksPath(__DIR__);
    expect($blocks->getBlocksPaths())->toBeArray()->and($blocks->getBlocksPaths()[0])->toBe(__DIR__);
});

it('can extend block meta settings', function () {
    $container = Mockery::mock(\Illuminate\Container\Container::class);
    $blocks = getBlockInstance(['container' => $container]);

    $container->shouldReceive('make')->with(BlockSettingsExtender::class)->andReturn('foo');

    Functions\expect('add_filter')
        ->once()
        ->with('block_type_metadata_settings', ['foo', 'allowViewRenderInBlockMeta'], 99, 3);

    $blocks->extendBlockMetaSettings();
});

it('can get block data associative array', function () {
    $metaFile = $this->filesDir . "/blocks/dynamic/block.json";
    $container = Mockery::mock(\Illuminate\Container\Container::class);
    $blocks = getBlockInstance(['container' => $container]);

    $parserMock = \Mockery::mock(\MorningMedley\Block\Classes\BlockMetaFileParser::class);
    $parserMock->shouldReceive('parseFileProperty')->andReturnUsing(fn($p) => [$p])->times(3);

    $container->shouldReceive('makeWith')
        ->with(\MorningMedley\Block\Classes\BlockMetaFileParser::class, ['metaFile' => $metaFile])
        ->andReturn($parserMock);

    $blockData = $blocks->getBlockDataAssoc($metaFile);
    expect($blockData)->toBeArray()->toHaveKeys(['phpScript', 'viewPhpScript', 'editorPhpScript']);
});

it('can get block data assoc for multiple blocks at once', function () {
    $metaFiles = [$this->filesDir . "/blocks/dynamic/block.json", $this->filesDir . "/blocks/dynamic/block.json"];

    $container = Mockery::mock(\Illuminate\Container\Container::class);
    $blocks = getBlockInstance(['container' => $container]);

    $parserMock = \Mockery::mock(\MorningMedley\Block\Classes\BlockMetaFileParser::class);
    $parserMock->shouldReceive('parseFileProperty')->andReturnUsing(fn($p) => [$p])->times(6);

    $container->shouldReceive('makeWith')
        ->twice()
        ->andReturn($parserMock);

    $blocksData = $blocks->getBlocksDataAssoc($metaFiles);
    expect($blocksData)->toBeArray()->and($blocksData[0])->toHaveKeys([
        'phpScript',
        'viewPhpScript',
        'editorPhpScript',
    ]);
});

it('can initialize without using cache in local env', function () {
    $container = Mockery::mock(\Illuminate\Container\Container::class);
    $locatorMock = Mockery::mock(\MorningMedley\Block\Classes\BlockLocator::class);
    $parserMock = Mockery::mock(\MorningMedley\Block\Classes\BlockMetaFileParser::class);
    $registratorMock = Mockery::mock(\MorningMedley\Block\Classes\BlockRegistrator::class);

    Functions\when('add_filter')->justReturn();
    Functions\when('wp_get_environment_type')->justReturn('local');

    $container->shouldReceive('isProduction')->andReturn(false);

    $container->shouldReceive('make')
        ->with(\MorningMedley\Block\Classes\BlockLocator::class)
        ->andReturn($locatorMock);

    $container->shouldReceive('make')
        ->with(\MorningMedley\Block\Classes\BlockSettingsExtender::class)
        ->andReturn('');

    $container->shouldReceive('makeWith')
        ->with(\MorningMedley\Block\Classes\BlockMetaFileParser::class,
            ['metaFile' => $this->filesDir . "/blocks/dynamic/block.json"])
        ->andReturn($parserMock);

    $locatorMock->shouldReceive('locate')->andReturnUsing(fn($path) => [$path . "/dynamic/block.json"]);

    $parserMock->shouldReceive('parseFileProperty')->andReturnUsing(fn($prop) => [$prop]);

    $registratorMock->shouldReceive('registerBlocks')->andReturn();

    $blocks = getBlockInstance(['container' => $container, 'registrator' => $registratorMock]);
    $blocks->registerBlocksPath($this->filesDir . "/blocks");

    $blocks->init();
});

it('can initialize using cache in production env', function () {
    $container = Mockery::mock(\Illuminate\Container\Container::class);
    $parserMock = Mockery::mock(\MorningMedley\Block\Classes\BlockMetaFileParser::class);
    $registratorMock = Mockery::mock(\MorningMedley\Block\Classes\BlockRegistrator::class);
    $cacheMock = \Mockery::mock(PhpFilesAdapter::class);
    $locatorMock = Mockery::mock(\MorningMedley\Block\Classes\BlockLocator::class);

    Functions\when('add_filter')->justReturn();
    Functions\when('wp_get_environment_type')->justReturn('production');

    $container->shouldReceive('isProduction')->andReturn(true);

    $container->shouldReceive('make')
        ->with(\MorningMedley\Block\Classes\BlockLocator::class)
        ->andReturn($locatorMock);

    $container->shouldReceive('make')
        ->with(\MorningMedley\Block\Classes\BlockSettingsExtender::class)
        ->andReturn('');

    $container->shouldReceive('makeWith')
        ->with(\MorningMedley\Block\Classes\BlockMetaFileParser::class,
            ['metaFile' => $this->filesDir . "/blocks/dynamic/block.json"])
        ->andReturn($parserMock);

    $locatorMock->shouldReceive('locate')->andReturnUsing(fn($path) => [$path . "/dynamic/block.json"]);

    $parserMock->shouldReceive('parseFileProperty')->andReturnUsing(fn($prop) => [$prop]);

    $registratorMock->shouldReceive('registerBlocks')->andReturn();

    $cacheMock->shouldReceive('get')->with('blocks', Closure::class)->andReturn([]);

    $blocks = getBlockInstance(['container' => $container, 'registrator' => $registratorMock, 'cache' => $cacheMock]);
    $blocks->registerBlocksPath($this->filesDir . "/blocks");

    $blocks->init();
});

it('can delete cache', function(){
    $cacheMock = \Mockery::mock(PhpFilesAdapter::class);
    $cacheMock->shouldReceive('delete')->with('blocks')->andReturn('true');
    $blocks = getBlockInstance(['cache' => $cacheMock]);
    expect($blocks->deleteCache())->toBeTrue();
});
