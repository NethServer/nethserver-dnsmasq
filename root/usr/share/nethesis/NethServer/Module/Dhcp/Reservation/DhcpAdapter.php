<?php
namespace NethServer\Module\Dhcp\Reservation;

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
 * TODO: add component description here
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class DhcpAdapter extends \Nethgui\Adapter\LazyLoaderAdapter
{
    /**
     * @var \Nethgui\System\PlatformInterface
     */
    private $platform;

    /**
     * @var \Nethgui\Adapter\AdapterInterface
     */
    private $innerAdapter;

    public function __construct(\Nethgui\System\PlatformInterface $p)
    {
        $this->platform = $p;
        $this->innerAdapter = $this->platform->getTableAdapter('hosts', 'local');
        parent::__construct(array($this, 'readTable'));
    }

    public function isModified()
    {
        return $this->innerAdapter->isModified();
    }

    public function save()
    {
        $s = $this->innerAdapter->save();
        if ($s) {
            $this->lazyInitialization();
        }
        return $s;
    }

    public function offsetSet($offset, $value)
    {
        return $this->innerAdapter->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        return $this->innerAdapter->offsetUnset($offset);
    }

    public function readTable()
    {
        $reservations = iterator_to_array($this->innerAdapter);

        $macMap = array();
        foreach($reservations as $rid => $r) {
            $macMap[$r['MacAddress']] = $rid;
        }


        $leases = json_decode($this->platform->exec('/usr/libexec/nethserver/read-dhcp-leases')->getOutput(), TRUE);

        if ( ! is_array($leases)) {
            $leases = array();
        }

        $now = time();

        foreach ($leases as $l) {
            $leaseStatus = $now > $l['expire'] ? 0x0 : 0x1; // Expired : Valid

            $l['name'] = isset($l['name']) ? $l['name'] : 'host-' . substr(md5($l['mac'] . $l['ip']), 0, 8);

            // use hostname from reservation, if associated to MAC from lease cache:
            $lid = isset($macMap[$l['mac']]) ? $macMap[$l['mac']] : $l['name'];

            if (isset($reservations[$lid])) {
                $reservations[$lid]['LeaseStatus'] = $leaseStatus | 0x2; // Reserved
            } elseif(count(array_filter($reservations, function ($e) use ($l) {
                // Search a reservation for the given MAC address
                return $e['MacAddress'] === $l['mac'];
            })) == 0) {
                $reservations[$lid] = array(
                    'MacAddress' => $l['mac'],
                    'IpAddress' => $l['ip'],
                    'Description' => '',
                    'LeaseStatus' => $leaseStatus, // Free                   
                );
            }
        }

        //        die(var_dump($reservations, 1));

        return new \ArrayObject($reservations);
    }

}
