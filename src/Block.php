<?php

namespace MorningMedley\Block;

use \Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
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

    /**
     * Locate blocks by config or paths, then setup init
     *
     * @return void
     */
    public function boot(): void
    {
        \add_action('init', [$this, 'init']);
        $this->locate();
    }

    /**
     * Initialize blocks. Should only be called on init action
     *
     * @return void
     */
    public function init(): void
    {
        $this->extendBlockMetaSettings();

        $blocks = $this->getBlocksDataAssoc($this->absoluteBlocks());

        $this->blockRegistrator->registerBlocks($blocks);
    }

    /**
     * Locate block paths by cache or paths, then store them in BlockCollection
     *
     * @return void
     * @throws BindingResolutionException
     */
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

    /**
     * Get Block data for all known blocks
     *
     * @param  array  $blocks
     * @return array
     */
    public function getBlocksDataAssoc(array $blocks): array
    {
        foreach ($blocks as $k => $block) {
            $blocks[$k] = $this->getBlockDataAssoc($block);
        }

        return $blocks;
    }

    /**
     * Get block data for a given block
     *
     * @param  string  $blockMetaFile
     * @return string[]
     * @throws BindingResolutionException
     */
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

    /**
     * Register a path to locate blocks in
     *
     * @param  string  $path
     * @return $this
     */
    public function registerBlocksPath(string $path)
    {
        $this->paths[] = $path;

        return $this;
    }

    /**
     * Get list of paths containing blocks
     *
     * @return array
     */
    public function getBlocksPaths(): array
    {
        return $this->paths;
    }

    /**
     * Get current BlockCollection
     *
     * @return BlockCollection
     */
    public function blockCollection(): BlockCollection
    {
        return $this->blockCollection;
    }

    /**
     * Add one or more block.json paths
     *
     * @param  string|array  $block
     * @return void
     */
    public function add(string|array $block): void
    {
        $this->blockCollection->add($block);
    }

    /**
     * Get relative block.json paths
     *
     * @return array
     */
    public function blocks(): array
    {
        return $this->blockCollection->blocks();
    }

    /**
     * Get absolute block.json paths
     *
     * @return array
     */
    public function absoluteBlocks(): array
    {
        return $this->blockCollection->absoluteBlocks();
    }

    /**
     * Hook in to extend block meta
     *
     * @return void
     * @throws BindingResolutionException
     */
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
    public function blocksAreCached(): bool
    {
        return file_exists($this->getCachePath());
    }
}






