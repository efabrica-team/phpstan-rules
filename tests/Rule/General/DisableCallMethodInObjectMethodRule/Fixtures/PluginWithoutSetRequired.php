<?php

namespace BaseModule\Plugin\Header;

class PluginWithSetRequired extends BasePluginDefinition
{
    public function test(): array
    {
        return [
            new ChoozerConfigItem('main_menu_parent_page_id', 'Stránka s hlavným menu', 'page'),
            (new ChoozerConfigItem('main_menu_parent_page_id', 'Stránka s hlavným menu', 'page'))
                ->setRequired(),
        ];
    }

    public function pageConfiguration(): array
    {
        return [
            new ChoozerConfigItem('main_menu_parent_page_id', 'Stránka s hlavným menu', 'page'),
            (new ChoozerConfigItem('main_menu_parent_page_id', 'Stránka s hlavným menu', 'page')),
        ];
    }
}
