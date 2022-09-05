<?php

namespace BaseModule\Plugin\Header;

use Efabrica\Cms\Core\Plugin\BasePluginDefinition;

class PluginWithEmptyPageConfiguration extends BasePluginDefinition
{
    public function pageConfiguration(): array
    {
        return [];
    }
}
