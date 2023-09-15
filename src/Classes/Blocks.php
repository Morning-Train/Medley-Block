<?php

namespace Morningtrain\WP\Blocks\Classes;

use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use \Symfony\Contracts\Cache\CacheInterface;
use \Illuminate\Container\Container;

class Blocks
{
    protected array $paths = [];
    protected BlockLocator $blockLocator;
    protected BlockRegistrator $blockRegistrator;
    private CacheInterface $cache;
    private array $phpFileProperties = ['phpScript', 'viewPhpScript', 'editorPhpScript'];
    private Container $app;

    public function __construct(Container $app, BlockRegistrator $blockRegistrator, CacheInterface $cache)
    {
        $this->blockRegistrator = $blockRegistrator;
        $this->cache = $cache;
        $this->app = $app;
        
        \add_action('init', [$this, 'init']);
    }

    public function init(): void
    {
        $blocks = [];
        $this->extendBlockMetaSettings();

        foreach ($this->paths as $path) {
            /** @var BlockLocator $locater */
            $locator = $this->app->make(BlockLocator::class);
            $blocks = [...$locator->locate($path), ...$blocks];
        }

        if (\wp_get_environment_type() !== 'production') {
            $blocks = $this->getBlocksDataAssoc($blocks);
        } else {
            dump($this->cache);
            $blocks = $this->cache->get('blocks-data', function (ItemInterface $item) use ($blocks) {
                $item->expiresAfter(DAY_IN_SECONDS * 30);

                return $this->getBlocksDataAssoc($blocks);
            });
        }

        $this->blockRegistrator->registerBlocks($blocks);
    }

    public function getBlocksDataAssoc(array $blocks): array
    {
        foreach ($blocks as $k => $block) {
            $blocks[$k] = $this->getBlockDataAssoc($block);
        }

        return $blocks;
    }

    public function getBlockDataAssoc(string $blockMetaFile): array
    {
        $parser = $this->app->makeWith(BlockMetaFileParser::class, ['metaFile' => $blockMetaFile]);
        $block = [
            'metaFile' => $blockMetaFile,
        ];

        foreach ($this->phpFileProperties as $property) {
            $block[$property] = $parser->parseFileProperty($property);
        }

        return $block;
    }

    public function registerBlocksPath(string $path)
    {
        $this->paths[] = $path;

        return $this;
    }

    public function extendBlockMetaSettings()
    {
        \add_filter('block_type_metadata_settings',
            [$this->app->make(BlockSettingsExtender::class), 'allowViewRenderInBlockMeta'], 99, 3);
    }
}






