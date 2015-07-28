<?php
/* @var $view \Nethgui\Renderer\Xhtml */

echo $view->header()->setAttribute('template', $T('Dhcp_Configure_header'));

echo $view->objectsCollection('interfaces')
    ->setAttribute('template', function ($view) use ($T) {
        return $view->fieldsetSwitch('status', 'enabled', $view::FIELDSETSWITCH_CHECKBOX | $view::FIELDSETSWITCH_EXPANDABLE)
            ->setAttribute('uncheckedValue', 'disabled')
            ->setAttribute('labelSource', 'name')
            ->setAttribute('label', '${0}')
            ->insert($view->columns()
                ->insert($view->textInput('DhcpRangeStart')->setAttribute('label', $T('DhcpRangeStart_label')))
                ->insert($view->textInput('DhcpRangeEnd')))
            ->insert($view->fieldset('', $view::FIELDSET_EXPANDABLE)->setAttribute('template', $T('DhcpAdvanced_label'))
                ->insert($view->columns()
                    ->insert($view->textInput('DhcpGatewayIP'))
                    ->insert($view->textInput('DhcpLeaseTime'))
                    ->insert($view->textInput('DhcpDomain'))
                )
                ->insert($view->columns()
                    ->insert($view->textInput('DhcpDNS'))
                    ->insert($view->textInput('DhcpWINS'))
                )
                ->insert($view->columns()
                    ->insert($view->textInput('DhcpNTP'))
                    ->insert($view->textInput('DhcpTFTP'))
                )
           )

        ;
    })
    ->setAttribute('key', 'id');

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP);
