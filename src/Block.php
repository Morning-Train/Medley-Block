<?php

namespace MorningMedley\Block;

use \Illuminate\Container\Container;
use MorningMedley\Block\Classes\BlockCollection;
use MorningMedley\Block\Classes\BlockLocator;
use MorningMedley\Block\Classes\BlockMetaFileParser;
use MorningMedley\Block\Classes\BlockRegistrator;
use MorningMedley\Block\Classes\BlockSettingsExtender;

class Block
{

    private array $paths = [];
    private array $phpFileProperties = ['phpScript', 'viewPhpScript', 'editorPhpScript'];

    public function __construct(
        protected Container $app,
        protected BlockRegistrator $blockRegistrator,
        protected BlockCollection $blockCollection
    ) {

    }

    public function boot(): void
    {
        \add_action('init', [$this, 'init']);
        $this->locate();
    }

    public function init(): void
    {
        $this->extendBlockMetaSettings();

        $blocks = $this->getBlocksDataAssoc($this->blockCollection->list());

        $this->blockRegistrator->registerBlocks($blocks);
    }

    public function locate(): void
    {
        if ($this->blocksAreCached()) {
            $this->blockCollection->add(require $this->getCachePath());
        } else {
            $configPaths = (array) $this->app['config']->get('block.compiled', []);
            foreach ([...$this->getBlocksPaths(), ...$configPaths] as $path) {
                // If the path is not a directory then it might be a relative path
                if (! is_dir($path)) {
                    // Prepend path with project basepath
                    $path = $this->app->basePath($path);
                    // If this is still not a dir, then continue instead of crashing
                    if (! is_dir($path)) {
                        continue;
                    }
                }

                /** @var BlockLocator $locater */
                $locator = $this->app->make(BlockLocator::class);
                $this->blockCollection->add($locator->locate($path));
            }
        }
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

    public function add(string|array $block): void
    {
        $this->blockCollection->add($block);
    }

    public function extendBlockMetaSettings()
    {
        \add_filter('block_type_metadata_settings',
            [$this->app->make(BlockSettingsExtender::class), 'allowViewRenderInBlockMeta'], 99, 3);
    }

    /**
     * Get absolute path to cache file
     *
     * @return string
     */
    public function getCachePath(): string
    {
        return $this->app->bootstrapPath('cache/block.php');
    }

    /**
     * Checks if hook cache file exists
     *
     * @return bool
     */
    protected function blocksAreCached(): bool
    {
        return file_exists($this->getCachePath());
    }
}






