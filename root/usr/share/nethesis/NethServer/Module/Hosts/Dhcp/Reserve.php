<?php
namespace NethServer\Module\Hosts\Dhcp;

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
 * Precompile the create form with data from current lease status
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class Reserve extends \Nethgui\Controller\Table\RowAbstractAction
{

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        parent::bind($request);
        if ($request->isMutation()) {
            $keyValue = $request->getParameter('hostname');
        } else {
            $keyValue = \Nethgui\array_end($this->getRequest()->getPath());
        }
        $this->getAdapter()->setKeyValue($keyValue);
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        // Bind the dhcp-reservation platform validator:
        $this->getValidator('IpAddress')
            ->platform('dhcp-reservation', $this->parameters['MacAddress']);

        parent::validate($report);
    }

    public function process()
    {
        if ($this->getRequest()->isMutation()) {
            // XXX Force record to be marked DIRTY:
            $this->getAdapter()->offsetSet('FORCE', 1);
            $this->getAdapter()->offsetUnset('FORCE');

            if ($this->saveParameters()) {
                $this->getPlatform()->signalEvent('host-create@post-process', array(
                    $this->parameters['hostname']));
            }
        }
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        $view->setTemplate('NethServer\Template\Hosts\Dhcp\Modify');
        parent::prepareView($view);
    }

}