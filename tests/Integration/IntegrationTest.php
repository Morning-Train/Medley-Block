<?php

use Yoast\WPTestUtils\BrainMonkey\TestCase;
use Morningtrain\WP\Facades\Blocks as BlocksFacade;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use MorningMedley\Blocks\Classes\Blocks;
use MorningMedley\Blocks\Classes\BlockRegistrator;
use Illuminate\Container\Container;

if (isUnitTest()) {
    return;
}

uses(TestCase::class);

beforeEach(function () {

});

it('can register a block', function () {
    $container = new Container();
    BlocksFacade::setFacadeApplication($container);

    $container->singleton('wp-blocks',
        fn($container) => new Blocks($container, new BlockRegistrator(),
            new PhpFilesAdapter('wp-blockz', 1, __DIR__ . "/_php_cache")));

    BlocksFacade::registerBlocksPath(dirname(__FILE__, 3) . "/wp/src/wp-content/blocks");
    do_action('init');
    expect(\WP_Block_Type_Registry::get_instance()->is_registered('test/dynamic'))->toBeTrue();
});
