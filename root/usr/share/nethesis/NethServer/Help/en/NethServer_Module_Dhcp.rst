====
DHCP
====

The DHCP (Dynamic Host Configuration Protocol) allows you to assign IP
addresses to clients on the local network

Server
======

Configure the DHCP server.

Disabled
    The DHCP server will be disabled and the LAN clients will not
    receive the address in an automatic way by this server. Select
    this option if there is another DHCP server on your local network.

Enabled
    The server will issue IP addresses to computers on the local
    network (recommended).

Range start
    The first IP address in the range assigned to the clients on the
    LAN.

Range end
    The last IP address of the range, addresses between Start and End
    will ge assigned to clients.

Reservation
===========

Create / Modify
---------------

Adds a new static allocation (reservation) to the DHCP server.  The
device with the specified MAC address will always receive the
specified IP Address.

Host Name
    The host name you want to assign to clients on the LAN with the
    specified IP address.

Description
    An optional description to identify the system.

IP Address
    The IP address you want to assign.

MAC Address
    The MAC address of the network system (eg
    11:22:33:44:55:66:77:88).
