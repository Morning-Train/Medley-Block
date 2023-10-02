<?php

namespace MorningMedley\Block\Classes;

class BlockRegistrator
{
    public function registerBlocks(array $blocks): void
    {
        foreach ($blocks as $block) {
            $this->registerBlock($block);
        }
    }

    public function registerBlock(string|array $block): void
    {
        if (is_string($block)) {
            $block = ['metaFile' => $block];
        }

        if (! file_exists($block['metaFile']) && ! is_dir($block['metaFile'])) {
            throw new \Exception("BlockRegistrator needs a valid path. \"{$block['metaFile']}\" is not.");
        }

        $this->loadBlockDependencies($block);

        \register_block_type($block['metaFile']);
    }

    public function loadBlockDependencies(array $block): void
    {
        $dir = dirname($block['metaFile']);
        $isJsonRequest = $this->isJsonRequest();
        $deps = [];

        if (isset($block['phpScript'])) {
            $deps = $block['phpScript'];
        }

        if (isset($block['editorPhpScript']) && ($isJsonRequest || $this->shouldLoadBlockEditorScriptsAndStyles())) {
            $deps = array_merge($deps, $block['editorPhpScript']);
        }

        if (isset($block['viewPhpScript']) && (! $isJsonRequest && ! $this->isAdmin())) {
            $deps = array_merge($deps, $block['viewPhpScript']);
        }

        foreach ($deps as $dep) {
            require $dep;
        }
    }

    public function isJsonRequest(): bool
    {
        return \wp_is_json_request();
    }

    public function shouldLoadBlockEditorScriptsAndStyles(): bool
    {
        return \wp_should_load_block_editor_scripts_and_styles();
    }

    public function isAdmin(): bool
    {
        return \is_admin();
    }
}
