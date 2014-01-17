<?php
namespace NethServer\Module\Dashboard\SystemStatus;

/*
 * Copyright (C) 2013 Nethesis S.r.l.
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
 * Retrieve dnsmasq configuration 
 *
 * @author Giacomo Sanchietti
 */
class Dnsmasq extends \Nethgui\Controller\AbstractController
{

    public $sortId = 20;
 
    private $dns;
    private $dhcp;

    private function readDNS()
    {
        $dns = array();
        $k = $this->getPlatform()->getDatabase('configuration')->getKey('dns');
        $dns['servers'] = $k['NameServers'];
        $dns['role'] = $k['role'];

        return $dns;
    }

    private function readDHCP()
    {
        $dhcp = array();
        $k = $this->getPlatform()->getDatabase('configuration')->getKey('dnsmasq');
        $dhcp['start'] = $k['DhcpRangeStart'];
        $dhcp['end'] = $k['DhcpRangeEnd'];
        $dhcp['status'] = $k['DhcpStatus'];

        return $dhcp;
    }


    public function process()
    {
        $this->dns = $this->readDNS();
        $this->dhcp = $this->readDHCP();
    }
 
    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        if (!$this->dns) {
            $this->dns = $this->readDNS();
        }
        if (!$this->dhcp) {
            $this->dhcp = $this->readDHCP();
        }
        foreach ($this->dhcp as $k => $v) {
            $view['dhcp_' . $k] = $v;
        }
        foreach ($this->dns as $k => $v) {
            $view['dns_' . $k] = $v;
        }
    }
}
