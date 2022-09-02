<?php

class PluginWithSetRequiredMin extends APluginWithSetRequired
{
    public function globalConfiguration(): array
    {
        return [
                (new ChoozerConfigItem('main_menu_parent_page_id', 'Stránka s hlavným menu', 'page'))->setRequired(),
        ];
    }
}
