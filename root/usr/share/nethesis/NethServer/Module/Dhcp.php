<?php
namespace NethServer\Module;

/*
 * Copyright (C) 2011 Nethesis S.r.l.
 * 
 * This script is part of NethServer.
 * 
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Implement gui module for DHCP configuration
 */
class Dhcp extends \Nethgui\Controller\TabsController
{
    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $base)
    {
        return \Nethgui\Module\SimpleModuleAttributesProvider::extendModuleAttributes($base, 'Configuration', 60);
    }

    public function initialize()
    {
        parent::initialize();
        $this->addChild(new \NethServer\Module\Dhcp\Configure());
        $this->addChild(new \NethServer\Module\Dhcp\Reservation());
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        $isConfigured = 0 !== count(array_filter($this->getPlatform()->getDatabase('dhcp')->getAll('range'), function ($record) {
                    return $record['status'] === 'enabled';
                }));

        if ($isConfigured) {
            $this->sortChildren(function (\Nethgui\Module\ModuleInterface $a, \Nethgui\Module\ModuleInterface $b) {
                if ($a->getIdentifier() === 'Reservation') {
                    return -1;
                }
                return 0;
            });
        }

        parent::prepareView($view);
    }

}

