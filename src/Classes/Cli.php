<?php

namespace MorningMedley\Block\Classes;

class Cli
{
    public function deleteCache()
    {
        if (\Morningtrain\WP\Facades\Blocks::deleteCache()) {
            \WP_CLI::success("WP-Blocks cache has been deleted.");
        } else {
            \WP_CLI::error("WP-Blocks cache could not be deleted.");
        }
    }
}
