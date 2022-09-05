<?php

namespace BaseModule\Plugin\Header;

class PluginWithSetRequired extends BasePluginDefinition
{
<<<<<<< HEAD:tests/Rule/Notcms/PluginConfigItemNoRequiredInGlobalConfigurationRule/Fixtures/PluginWithoutSetRequired.php
    protected $identifier = 'header';

    protected $name = 'Header plugin';

    protected $frontendControlClass = PluginWithSetRequired::class;

    protected $globalPlugin = true;

=======
>>>>>>> Generic:tests/Rule/General/DisableCallMethodInObjectMethodRule/Fixtures/PluginWithoutSetRequired.php
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
