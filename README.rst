==================
nethserver-dnsmasq
==================

DNS
===

The system will resolve host and domain names using DNS queries
to external DNS servers.
The configuration is saved inside the ``dns`` key from *nethserver-base* package.

Properties:

* ``NameServers``: comma separated IP list of external DNS
* ``role``: can be set to ``none`` or ``resolver``.
  If role is set to ``none`` the server will always use external DNS.
  For ``resolver`` role see :ref:`dns_server-section`.


Example: ::

 dns=configuration
    NameServers=8.8.8.8,208.67.222.222
    role=none

Hosts
-----

The system can handle local DNS records.
When the server performs a DNS lookup, first it will search inside local DNS records.
If no local record is found, an external DNS query will be done.

.. note:: Local DNS record will always override records from external DNS servers.

DNS records are called :dfn:`hosts` and are saved inside the ``hosts`` database.
Each entry is saved inside the :file:`/etc/hosts` file.

There are three types of records:

* ``local``: hosts inside the internal network
* ``remote``: hosts outside the internal network
* ``self``: alias for the server itself

Records of type ``local`` and ``remote`` can have following properties:

* ``IpAddress``: address of the host
* ``Description``: optional description
* ``MacAddress``: mac address of the host. Used only for DHCP reservation. See :ref:`ip_reservation-section`.


For hosts inside local network, the record key doesn't have the domain part. Example: ::

  host1=local
      Description=Internal network host #1
      IpAddress=192.168.1.23

For hosts outside local network, the record key must have the domain part. Example: ::

  external.otherdomain.tld=remote
      Description=Other domain host
      IpAddress=8.9.10.11

Records of type ``self`` can have following properties:

* ``Description``: optional description

Example: ::

  vhost1.domain.tld=self
      Description=Virtual Host #1


.. _dns_server-section:

DNS server
----------

The system uses *dnsmasq* as DNS and DHCP server and it directly resolves all hosts inside its domain.
All other names will be queried to external DNS servers.

The server will forward reverse lookups to upstream DNS servers, only if upstream DNS servers are inside a
private network (eg. network address is 192.168.x.x).

The option ``bind-interfaces`` is always enabled, as consequence (from dnsmasq man):

 This option has been patched to always use SO_BINDTODEVICE socket option when binding to  interfaces.  As  consequence,  dnsmasq
 WILL  NOT ANSWER to any DNS Queries that come to the socket with the correct destination IP address, but originally on different
 interface. This behavior differs from the original dnsmasq upstream version and is used for security reasons.


Properties:

* ``CacheSize``: entry to be cached by server, default is ``4000``
* ``dhcp-boot``: directly pass parameters to dhcp-boot option
* ``except-interface``: comma-separated list of interfaces. Do not listen to listed interfaces, useful to avoid conflicts with libvirt
* ``tftp-status``: can be ``enabled`` or ``disabled``. If enabled, enable the TFTP server for BOOTP (port 67)
* ``access``: default is ``private``, do NOT set to ``public``
* ``DomainRedirection``: specify a dns server for a particular domain (comma separated). The ``domain.org:192.168.1.1`` will send all queries ``*.domain.org`` for internal machines to ``192.168.1.1``. The special server address ``#`` means, "use the standard servers", so ``sub.domain.org:#`` will send all queries for ``*sub.domain.org`` to the default DNS server of the domain name.

Database example: ::

  dnsmasq=service
    AllowHosts=
    CacheSize=4000
    DenyHosts=
    DomainRedirection=domain.org:192.168.1.1,sub.domain.org:#
    TCPPort=53
    UDPPorts=53,67
    access=private
    dhcp-boot=pxelinux.0,myserver.mydomain.com,192.168.1.1
    except-interface=virbr0,tunspot
    status=enabled
    tftp-status

DHCP
====

The system can act as DHCP server for the local network.
Machines which are configured by DHCP have their names automatically included in the DNS server.

The DHCP can be enabled only on *green* and *blue* interfaces (see :ref:`section-roles-and-zones`).
Configuration is saved inside the ``dhcp`` database.

Each record of ``range`` type is associated to an ethernet interface and can have following properties:

* ``status``: can be ``enabled`` or ``disabled``
* ``DhcpRangeStart``: first IP address of DHCP range
* ``DhcpRangeEnd``: last IP address of DHCP range
* ``DhcpLeaseTime``: seconds of lease time. Default is 86400
* ``DhcpGatewayIp``: (optional) set a custom gateway ip. If not set, the gateway is the ip address of associated interface (record key)

The key of the record is the name of the associated interface. Example: ::

  eth0=range
    DhcpGatewayIp=
    DhcpLeaseTime=86400
    DhcpRangeEnd=192.168.1.100
    DhcpRangeStart=192.168.5.200
    status=enabled


Hosts inside the blue network can always access the local DNS server.


The gateway for clients will be:

* if set, the value of property ``DhcpGatewayIp``
* otherwise if the server has a red interface, the gateway is the IP address of the interface where the DHCP is enabled
  (eg. IP of the blue interface for clients in the guest's network)
* otherwise if the server has only a green interface, the gateway of the green interface will be used


.. _ip_reservation-section:

IP reservation
==============

It's possible to reserve IPs for specific devices associating the MAC address of the device with the reserved IP.
The reservation is saved inside the ``hosts`` database.

Example: ::

  host1=local
      Description=Internal network host #1
      IpAddress=192.168.1.23
      MacAddress=08:00:27:48:BF:F3


TFTP server
===========

TFTP module contains configuration fragments that enables dnsmasq built-in TFTP server.

TFTP server has no authentication or encryption support. 

When installed tftp is disabled by default and need to be enabled with: ::

 config setprop dnsmasq tftp-status enabled
 signal-event nethserver-dnsmasq-save

The package also add directory :file:`/var/lib/tftpboot` that is the root of tftp server.

Enabling TFTP adds 5 new configuration options to :file:`/etc/dnsmasq.conf`. Here variables explanation according with dnsmasq documentation

* ``enable-tftp``: enable tftp server
* ``tftp-secure``: allow only files owned by the user dnsmasq is running as will be send over the net
* ``dhcp-boot= ...``: Set the boot filename for netboot/PXE. You will only need this is you want to boot machines over the network and you will need a TFTP server; driven by db prop
* ``tftp-root=/var/lib/tftpboot``: Set the root directory for files available via FTP.
* ``dhcp-option=66, LOCAL_IP``: set local ip as default tftp server for machines that receive dhcp from this server


Properties
----------

* ``status``: can be ``enabled`` or ``disabled``. If ``enabled``, TFTP server is configured and port 69 UDP is opened.
* ``UDPPort``: UDP port used. Only ``69`` is allowed.
* ``access``: define if access is ``public``, ``private`` or ``none``.
* ``dhcp-boot``:  Set the boot filename for PXE. Ths is needed for booting machines over the network. Empty by default.
* ``type``: only ``service`` is allowed.


Test TFTP
---------

Testing is very simple:

Enable TFTP server: ::

 config setprop dnsmasq tftp-status enabled
 signal-event nethserver-dnsmasq-save

Create a file to share, owned by ``nobody`` user: ::

 echo "test"  > /var/lib/tftpboot/foobar
 chown nobody:nobody /var/lib/tftpboot/foobar

From another machine, install tftp and get file
(on Fedora)::

 yum install tftp
 
Always from the other machine, allow incoming UDP connection from our TFTP server. Loading TFTP conntrack module should be enough::

 modprobe nf_conntrack_tftp 
 
Connect to TFTP server::

 tftp TFTP_SERVER_HOST

...and get the file::

 tftp> get foobar


Configure a PXE server
----------------------

Those instructions set up a PXE server for CentOS
Install and configure syslinux and nethserver-tftp: ::
 
 yum install syslinux
 cp /usr/share/syslinux/{pxelinux.0,menu.c32,memdisk,mboot.c32,chain.c32} /var/lib/tftpboot/
 config setprop tftp dhcp-boot pxelinux.0
 signal-event nethserver-tftp-save
 mkdir /var/lib/tftpboot/pxelinux.cfg

Create the file :file:`/var/lib/tftpboot/pxelinux.cfg/default` with the following content: ::

 default menu.c32
 prompt 0
 timeout 300

 MENU TITLE PXE Menu

 LABEL CentOS
 kernel CentOS/vmlinuz
 append initrd=CentOS/initrd.img

 Create a CentOS directory:

Create a CentOS directory: ::

 mkdir -p /var/lib/tftpboot/CentOS 

Copy inside the directory :file:`vmlinuz` and :file:`initrd.img` files. These files can be found inside the ISO or browsing the yum ``os`` mirror.

Change files owner to nobody: ::

 chown -R nobody /var/lib/tftpboot/*
