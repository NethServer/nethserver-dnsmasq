<?php
namespace NethServer\Module\Dhcp\Reservation;

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
 * Implement gui module for /etc/hosts configuration
 */
class Modify extends \Nethgui\Controller\Table\Modify
{

    public function initialize()
    {        
        if($this->getIdentifier() === 'delete') {
            $this->setViewTemplate('Nethgui\Template\Table\Delete');
        } else {
            $this->setViewTemplate('NethServer\Template\Dhcp\Modify');
        }        
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        // Bind the dhcp-reservation platform validator:
        if ($this->getRequest()->isMutation()) {
            $this->getValidator('IpAddress')
                ->platform('dhcp-reservation', $this->parameters['MacAddress'], $this->parameters['hostname']);
        }
        if ($this->getIdentifier() === 'delete') {
            $v = $this->createValidator()->platform('host-delete', 'hosts');
            if ( ! $v->evaluate($this->parameters['hostname'])) {
                $report->addValidationError($this, 'Key', $v);
            }
        }
        parent::validate($report);
    }

    public function process()
    {
        parent::process();
        if($this->getRequest()->isMutation()) {
            if($this->getAdapter()->offsetExists('LeaseStatus')) {
                $this->getAdapter()->offsetUnset('LeaseStatus');
            }
        }
    }

    public function onParametersSaved($changes)
    {
        $actionName = $this->getIdentifier();
        if ($actionName === 'update') {
            $actionName = 'modify';
        }
        $this->getPlatform()->signalEvent(sprintf('host-%s &', $actionName), array($this->parameters['hostname']));
    }

}
