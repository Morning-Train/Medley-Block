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
            if (! class_exists('\Illuminate\Support\Facades\View')) {
                return '';
            }

            return \Illuminate\Support\Facades\View::make($metadata['renderView'], [
                'attributes' => $attributes,
                'content' => $content,
                'block' => $block,
                'blockProps' => \get_block_wrapper_attributes(),
            ]);
        };

        return $settings;
    }
}
