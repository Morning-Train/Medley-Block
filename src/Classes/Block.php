<?php

namespace MorningMedley\Block\Classes;

use \Symfony\Contracts\Cache\ItemInterface;
use \Symfony\Contracts\Cache\CacheInterface;
use \Illuminate\Container\Container;

class Block
{
    private Container $app;
    private CacheInterface $cache;
    private BlockRegistrator $blockRegistrator;

    private array $paths = [];
    private array $phpFileProperties = ['phpScript', 'viewPhpScript', 'editorPhpScript'];
    private string $cacheKey = 'blocks';

    public function __construct(Container $app, BlockRegistrator $blockRegistrator, CacheInterface $cache)
    {
        $this->blockRegistrator = $blockRegistrator;
        $this->cache = $cache;
        $this->app = $app;

        \add_action('init', [$this, 'init']);
        if (class_exists("\WP_CLI")) {
            \WP_CLI::add_command('wp-blocks', $this->app->make(Cli::class));
        }
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

        if (!$this->app->isProduction()) {
            $blocks = $this->getBlocksDataAssoc($blocks);
        } else {
            $blocks = $this->cache->get($this->cacheKey, function (ItemInterface $item) use ($blocks) {
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

    public function getBlocksPaths(): array
    {
        return $this->paths;
    }

    public function extendBlockMetaSettings()
    {
        \add_filter('block_type_metadata_settings',
            [$this->app->make(BlockSettingsExtender::class), 'allowViewRenderInBlockMeta'], 99, 3);
    }

    public function deleteCache(): bool
    {
        return $this->cache->delete($this->cacheKey);
    }
}






