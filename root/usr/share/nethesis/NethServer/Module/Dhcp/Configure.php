<?php

namespace NethServer\Module\Dhcp;

/*
 * Copyright (C) 2014 Nethesis S.r.l.
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

use \Nethgui\System\PlatformInterface as Validate;

class Configure extends \Nethgui\Controller\AbstractController
{

    public function initialize()
    {
        parent::initialize();
        $this->tableAdapter = $this->getPlatform()->getTableAdapter('dhcp', 'range');
    }

    private function getDataSource()
    {
        static $dataSource;
        if (isset($dataSource)) {
            return $dataSource;
        }
        $dataSource = new \ArrayObject();

        foreach ($this->tableAdapter as $key => $record) {
            $dataSource[$key] = new \Nethgui\Adapter\RecordAdapter($this->tableAdapter);
            $dataSource[$key]->setKeyValue($key);
        }

        return $dataSource;
    }

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        parent::bind($request);
        if ($request->isMutation()) {
            $dataSource = $this->getDataSource();
            foreach ($request->getParameter('interfaces') as $key => $props) {
                if (isset($dataSource[$key])) {
                    $dataSource[$key]->set($props);
                } else {
                    $dataSource[$key] = new \Nethgui\Adapter\RecordAdapter($this->tableAdapter);
                    $dataSource[$key]->setKeyValue($key)->set($props);
                }
            }
        }
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        parent::validate($report);

        $submittedInterfaces = array_keys($this->getRequest()->getParameter('interfaces'));
        $existingInterfaces = array_keys($this->getNetworkInterfaces());

        if (count($submittedInterfaces) !== count(\array_intersect($existingInterfaces, $submittedInterfaces))) {
            $report->addValidationErrorMessage($this, 'interfaces', 'valid_interface_existing');
        }

        $statusValidator = $this->createValidator(Validate::SERVICESTATUS);
        $hostnameValidator = $this->createValidator(Validate::HOSTNAME);
        $positiveValidator = $this->createValidator(Validate::POSITIVE_INTEGER);
        $ipValidator = $this->createValidator()->ipV4Address();

        $interfaces = $this->getNetworkInterfaces();
        foreach ($this->getRequest()->getParameter('interfaces') as $key => $record) {

            $fakeModule = $this->getFakeModule('interfaces_' . $key);

            if ( ! $statusValidator->evaluate($record['status'])) {
                $report->addValidationError($fakeModule, 'status', $statusValidator);
                continue;
            }
            if ($record['status'] !== 'enabled') {
                continue; /* skip range validation if DHCP is disabled */
            }
            if ($ipValidator->evaluate($record['DhcpRangeStart'])) {
                if (ip2long($record['DhcpRangeStart']) < ip2long($this->getDefaultRange('start', $key, $interfaces[$key]))) {
                    $report->addValidationErrorMessage($fakeModule, 'DhcpRangeStart', 'valid_iprange_outofbounds');
                }
            } else {
                $report->addValidationError($fakeModule, 'DhcpRangeStart', $ipValidator);
            }
            if ($ipValidator->evaluate($record['DhcpRangeEnd'])) {
                if (ip2long($record['DhcpRangeEnd']) > ip2long($this->getDefaultRange('end', $key, $interfaces[$key]))) {
                    $report->addValidationErrorMessage($fakeModule, 'DhcpRangeEnd', 'valid_iprange_outofbounds');
                }
            } else {
                $report->addValidationError($fakeModule, 'DhcpRangeEnd', $ipValidator);
            }
            if (isset($record['DhcpGatewayIP']) && $record['DhcpGatewayIP']) {
               if (!$ipValidator->evaluate($record['DhcpGatewayIP'])) {
                   $report->addValidationError($fakeModule, 'DhcpGatewayIP', $ipValidator);
               }
            }
            if (isset($record['DhcpLeaseTime']) && $record['DhcpLeaseTime'] > 0) {
               if (!$positiveValidator->evaluate($record['DhcpLeaseTime'])) {
                   $report->addValidationError($fakeModule, 'DhcpLeaseTime', $positiveValidator);
               }
            }
            if (isset($record['DhcpDomain']) && $record['DhcpDomain']) {
               if (!$hostnameValidator->evaluate($record['DhcpDomain'])) {
                   $report->addValidationError($fakeModule, 'DhcpDomain', $hostnameValidator);
               }
            }
            $props = array('DhcpDNS', 'DhcpWINS', 'DhcpNTP', 'DhcpTFTP');
            foreach ($props as $prop) {
                if (isset($record[$prop]) && $record[$prop]) {
                    foreach (explode(',',$record[$prop]) as $ip) {
                        if (!$ipValidator->evaluate($ip)) {
                            $report->addValidationError($fakeModule, $prop, $ipValidator);
                        }
                   }
                }
            }
        }
    }

    private function getFakeModule($identifier)
    {
        $className = 'fakeModule_' . md5($identifier);
        eval("class $className extends \Nethgui\Module\AbstractModule {}");
        $m = new $className($identifier);
        $m->setParent($this);
        return $m;
    }

    private function getNetworkInterfaces()
    {
        static $interfaces;

        if (isset($interfaces)) {
            return $interfaces;
        }

        $interfaces = array_filter($this->getPlatform()->getDatabase('networks')->getAll(), function ($record) {
            if ( ! in_array($record['type'], array('ethernet', 'bridge', 'bond', 'vlan'))) {
                return FALSE;
            }
            if ( ! in_array($record['role'], array('green', 'blue'))) {
                return FALSE;
            }
            return TRUE;
        });

        return $interfaces;
    }

    private function getDefaultRange($type, $key, $props)
    {
        $ipaddr = ip2long($props['ipaddr']);
        $netmask = ip2long($props['netmask']);

        if ( ! ($ipaddr && $netmask)) {
            return '';
        }

        if ($type === 'start') {
            return long2ip(($ipaddr & $netmask) | 1);
        } elseif ($type === 'end') {
            return long2ip(($ipaddr | ~$netmask) & ~1);
        }

        return '';
    }

    public function process()
    {
        parent::process();
        if ($this->getRequest()->isMutation()) {
            foreach ($this->getDataSource() as $record) {
                $record->save();
            }
            $changes = $this->tableAdapter->save();
            if ($changes) {
                $this->getPlatform()->signalEvent('nethserver-dnsmasq-save');
            }
        }
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $interfaces = $this->getNetworkInterfaces();
        $dataSource = $this->getDataSource();
        $ds = array();
        foreach ($interfaces as $key => $props) {
            $record = isset($dataSource[$key]) ? \iterator_to_array($dataSource[$key]) : array();
            $ds[] = array(
                'id' => $key,
                'name' => $key . ' - ' . $props['role'],
                'status' => isset($record['status']) ? $record['status'] : 'disabled',
                'DhcpRangeStart' => isset($record['DhcpRangeStart']) ? $record['DhcpRangeStart'] : $this->getDefaultRange('start', $key, $props),
                'DhcpRangeEnd' => isset($record['DhcpRangeEnd']) ? $record['DhcpRangeEnd'] : $this->getDefaultRange('end', $key, $props),
                'DhcpGatewayIP' => isset($record['DhcpGatewayIP']) ? $record['DhcpGatewayIP'] : '',
                'DhcpLeaseTime' => isset($record['DhcpLeaseTime']) ? $record['DhcpLeaseTime'] : '',
                'DhcpDomain' => isset($record['DhcpDomain']) ? $record['DhcpDomain'] : '',
                'DhcpDNS' => isset($record['DhcpDNS']) ? $record['DhcpDNS'] : '',
                'DhcpWINS' => isset($record['DhcpWINS']) ? $record['DhcpWINS'] : '',
                'DhcpNTP' => isset($record['DhcpNTP']) ? $record['DhcpNTP'] : '',
                'DhcpTFTP' => isset($record['DhcpTFTP']) ? $record['DhcpTFTP'] : '',
            );
        }

        $view['interfaces'] = $ds;
    }

}
