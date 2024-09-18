<?php

namespace MorningMedley\Block\Classes;

use \Illuminate\Container\Container;

class Block
{
    private Container $app;
    private BlockRegistrator $blockRegistrator;

    private array $paths = [];
    private array $blocks = [];
    private array $phpFileProperties = ['phpScript', 'viewPhpScript', 'editorPhpScript'];

    public function __construct(Container $app, array $blocks, BlockRegistrator $blockRegistrator)
    {
        $this->blockRegistrator = $blockRegistrator;
        $this->app = $app;
        $this->blocks = $blocks;

        \add_action('init', [$this, 'init']);
    }

    public function init(): void
    {
        $this->extendBlockMetaSettings();

        $blocks = $this->getBlocksDataAssoc($this->blocks);

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
}






