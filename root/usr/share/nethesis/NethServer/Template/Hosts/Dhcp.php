<?php

echo $view->panel()
        ->insert($view->header('address')->setAttribute('template', 'Host entry'))
        ->insert($view->textInput('hostname', ($view->getModule()->getIdentifier() == 'update' ? $view::STATE_READONLY : 0)))
        ->insert($view->textInput('Description'))
        ->insert($view->textInput('IpAddress'))
        ->insert($view->textInput('MacAddress')->setAttribute('placeholder','00:00:00:00:00:00'))
;

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL);
