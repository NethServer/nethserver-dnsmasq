<?php

echo $view->header('network')->setAttribute('template', $T('Dhcp_Configure_header'));

echo $view->radioButton('DhcpStatus', 'disabled')->setAttribute('label', $T('status_disabled_label'));

echo $view->fieldsetSwitch('DhcpStatus', 'enabled')->setAttribute('label', $T('status_enabled_label'))
    ->insert($view->textInput('DhcpRangeStart'))
    ->insert($view->textInput('DhcpRangeEnd'))
    ;

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL);