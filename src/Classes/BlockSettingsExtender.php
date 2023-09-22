<?php

namespace Morningtrain\WP\Blocks\Classes;

class BlockSettingsExtender
{
    public function allowViewRenderInBlockMeta(array $settings, array $metadata): array
    {
        if (! class_exists("\Morningtrain\WP\View\View")) {
            return $settings;
        }

        if (! isset($metadata['renderView'])) {
            return $settings;
        }

        $settings['render_callback'] = static function ($attributes, $content, $block) use ($metadata) {
            return \Morningtrain\WP\View\View::render($metadata['renderView'], [
                'attributes' => $attributes,
                'content' => $content,
                'block' => $block,
                'blockProps' => \get_block_wrapper_attributes(),
            ]);
        };

        return $settings;
    }
}
