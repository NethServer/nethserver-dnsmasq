<?php
/* @var $view \Nethgui\Renderer\Xhtml */

if ($view->getModule()->getIdentifier() === 'create') {
    $headerTemplate = $T('Dhcp_create_header');
} elseif ($view->getModule()->getIdentifier() === 'Reserve') {
    $headerTemplate = $T('Dhcp_reserve_header');
} else {
    $headerTemplate = $T('Dhcp_update_header');
}

echo $view->header('hostname')->setAttribute('template', $headerTemplate);

echo $view->panel()
    ->insert($view->textInput('hostname', ($view->getModule()->getIdentifier() == 'update' ? $view::STATE_READONLY : 0)))
    ->insert($view->textInput('Description'))
    ->insert($view->textInput('MacAddress')->setAttribute('placeholder', '00:00:00:00:00:00'))
    ->insert($view->textInput('IpAddress'));

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL);

$view->includeCss("

.DataTable tr.expired td:first-child { color: inherit; background-image: url('/images/sync.png') }
.DataTable tr.free td { color: #888 !important }
");
