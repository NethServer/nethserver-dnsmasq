<?php
namespace NethServer\Module\Hosts\Dhcp;

/*
 * Copyright (C) 2012 Nethesis S.r.l.
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

use Nethgui\System\PlatformInterface as Validate;

/**
 * TODO: add component description here
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class Configure extends \Nethgui\Controller\Table\AbstractAction
{

    public function initialize()
    {
        $this->declareParameter('DhcpStatus', Validate::SERVICESTATUS, array('configuration', 'dnsmasq', 'DhcpStatus'));
        $this->declareParameter('DhcpRangeStart', $this->createValidator(), array('configuration', 'dnsmasq', 'DhcpRangeStart'));
        $this->declareParameter('DhcpRangeEnd', $this->createValidator(), array('configuration', 'dnsmasq', 'DhcpRangeEnd'));
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        if ($this->parameters['DhcpStatus'] === 'enabled') {
            // Enable the IP address validation
            $this->getValidator('DhcpRangeStart')->ipV4Address();
            $this->getValidator('DhcpRangeEnd')->ipV4Address();
        }
        parent::validate($report);
    }

    protected function onParametersSaved($changedParameters)
    {
        $this->getPlatform()->signalEvent('nethserver-dnsmasq-save@post-process');
    }

}