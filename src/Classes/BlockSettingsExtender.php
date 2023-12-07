<?php

namespace MorningMedley\Block\Classes;

class BlockSettingsExtender
{
    public function allowViewRenderInBlockMeta(array $settings, array $metadata): array
    {

        if (! isset($metadata['renderView'])) {
            return $settings;
        }

        $settings['render_callback'] = static function ($attributes, $content, $block) use ($metadata) {
            if (! function_exists('\MorningMedley\Functions\view')) {
                return '';
            }

            return \MorningMedley\Functions\view($metadata['renderView'], [
                'attributes' => $attributes,
                'content' => $content,
                'block' => $block,
                'blockProps' => \get_block_wrapper_attributes(),
            ])->render();
        };

        return $settings;
    }
}
