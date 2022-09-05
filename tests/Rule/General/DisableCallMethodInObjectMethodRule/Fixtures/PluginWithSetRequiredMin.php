<?php

namespace App;

class PluginWithSetRequiredMin
{
    public function pageConfiguration(): array
    {
        return [
            (new ChoozerConfigItem('main_menu_parent_page_id', 'Stránka s hlavným menu', 'page'))->setRequired(),
        ];
    }
}
