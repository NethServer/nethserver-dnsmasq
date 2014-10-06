<?php
namespace NethServer\Module\Dhcp;

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

use \Nethgui\System\PlatformInterface as Validate;

/**
 * Implement gui module for DHCP configuration
 */
class Reservation extends \Nethgui\Controller\TableController
{

    public function initialize()
    {
        $columns = array(
            'Key',
            'Description',
            'IpAddress',
            'MacAddress',
            'Actions'
        );

        $tableAdapter = new Reservation\DhcpAdapter($this->getPlatform());

        $parameterSchema = array(
            array('hostname', Validate::HOSTNAME_SIMPLE, \Nethgui\Controller\Table\Modify::KEY),
            array('Description', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
            array('IpAddress', Validate::IPv4, \Nethgui\Controller\Table\Modify::FIELD),
            array('MacAddress', Validate::MACADDRESS, \Nethgui\Controller\Table\Modify::FIELD),
        );

        $this
            ->setTableAdapter($tableAdapter)
            ->setColumns($columns)
            ->addRowAction(new Reservation\Reserve())
            ->addRowAction(new Reservation\Modify('update'))
            ->addRowAction(new Reservation\Modify('delete'))
            ->addTableAction(new Reservation\Modify('create'))
            ->addTableAction(new \Nethgui\Controller\Table\Help('Help'))
        ;

        $self = $this;

        array_map(function ($id) use ($parameterSchema, $self) {
            $self->getAction($id)->setSchema($parameterSchema);
        }, array('create', 'delete', 'Reserve', 'update'));

        parent::initialize();
    }

    public function prepareViewForColumnActions(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        $cellView = $action->prepareViewForColumnActions($view, $key, $values, $rowMetadata);

        if ( ! isset($values['LeaseStatus']) || $values['LeaseStatus'] & 0x2) {
            unset($cellView['Reserve']);
        } else {
            unset($cellView['update']);
            unset($cellView['delete']);
            $rowMetadata['rowCssClass'] .= ' free';
        }

        if ( ! isset($values['LeaseStatus']) || ! ($values['LeaseStatus'] & 0x1)) {
            $rowMetadata['rowCssClass'] .= ' expired padicon';
        }

        return $cellView;
    }

}
