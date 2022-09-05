<?php

namespace BaseModule\Plugin\Header;

use Efabrica\Cms\Core\Plugin\BasePluginDefinition;
use Efabrica\Cms\Core\Plugin\Config\ChoozerConfigItem;

class PluginWithSetRequired extends BasePluginDefinition
{
    protected $identifier = 'header';

    protected $name = 'Header plugin';

    protected $frontendControlClass = PluginWithSetRequired::class;

    protected $globalPlugin = true;

    public function pageConfiguration(): array
    {
        return [
            new ChoozerConfigItem('main_menu_parent_page_id', 'Stránka s hlavným menu', 'page'),
            (new ChoozerConfigItem('main_menu_parent_page_id', 'Stránka s hlavným menu', 'page'))->setRequired(),
        ];
    }

    public function globalConfiguration(): array
    {
        return [
            new ChoozerConfigItem('main_menu_parent_page_id', 'Stránka s hlavným menu', 'page'),
            (new ChoozerConfigItem('main_menu_parent_page_id', 'Stránka s hlavným menu', 'page'))->setRequired(),
        ];
    }
}
