Name:		nethserver-dnsmasq
Summary:	NethServer configuration files for dnsmasq
Version: 1.5.5
Release: 1%{?dist}
License:	GPL
Source: %{name}-%{version}.tar.gz
BuildArch:	noarch
URL: %{url_prefix}/%{name} 

Requires:	nethserver-hosts > 1.1.0-1.ns6
Requires:	dnsmasq

Obsoletes: nethserver-tftp
Provides: nethserver-tftp

BuildRequires:	nethserver-devtools

%description
Provides DNS and DHCP services on the local network

%prep
%setup

%build
%{makedocs}
perl createlinks

%install
rm -rf $RPM_BUILD_ROOT
(cd root   ; find . -depth -print | cpio -dump $RPM_BUILD_ROOT)
%{genfilelist} \
    $RPM_BUILD_ROOT \
	--dir /var/lib/tftpboot 'attr(0755, root, root)' \
    > %{name}-%{version}-filelist
echo "%doc COPYING" >> %{name}-%{version}-filelist

%clean
rm -rf $RPM_BUILD_ROOT

%post

%preun

%files -f %{name}-%{version}-filelist
%defattr(-,root,root,-)
%doc



%changelog
* Mon Nov 30 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.5.5-1
- Invalid firewall rules after deleting host object - Bug #3324 [NethServer]

* Tue Nov 10 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.5.4-1
- dhcp lease has wrong netmask - Bug #3286 [NethServer]

* Tue Sep 29 2015 Davide Principi <davide.principi@nethesis.it> - 1.5.3-1
- DHCP: multiple dns option ignored - Bug #3262 [NethServer]

* Thu Sep 24 2015 Davide Principi <davide.principi@nethesis.it> - 1.5.2-1
- Drop lokkit support, always use shorewall - Enhancement #3258 [NethServer]

* Tue Sep 08 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.5.1-1
- Configure DHCP on VLANs - Enhancement #3238

* Thu Aug 27 2015 Davide Principi <davide.principi@nethesis.it> - 1.5.0-1
- Custom DHCP options - Feature #3036 [NethServer]

* Wed Jul 15 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.4.7-1
- Dnsmasq: drop bind-interfaces implementation - Enhancement #3220 [NethServer]

* Fri May 22 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.4.6-1
- DHCP: can't create reservation with "Reserve IP" button - Bug #3181 [NethServer]

* Wed May 20 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.4.5-1
- Cannot modify DHCP reservation - Bug #3107 [NethServer]

* Tue May 19 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.4.4-1
- Reverse dns fails if an internal DNS is configured - Enhancement #3054 [NethServer]
- Adjust default dnsmasq cache - Enhancement #3024 [NethServer]

* Wed Jan 28 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.4.3-1.ns6
- Dnsmasq doesn't start: illegal repeated keyword at line 82 of /etc/dnsmasq.conf - Bug #3014 [NethServer]

* Tue Jan 27 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.4.2-1.ns6
- Dhcp server doesn't use all range available - Bug #2994 [NethServer]

* Tue Jan 20 2015 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.4.1-1.ns6
- DHCP LeaseStatus prop persisted to DB - Bug #2985 [NethServer]

* Tue Dec 09 2014 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.4.0-1.ns6
- DNS: remove role property from dns db key - Enhancement #2915 [NethServer]

* Tue Nov 11 2014 Davide Principi <davide.principi@nethesis.it> - 1.3.1-1.ns6
- DHCP: bind dnsmasq to specific interfaces - Enhancement #2926 [NethServer]

* Thu Oct 23 2014 Davide Principi <davide.principi@nethesis.it> - 1.3.0-1.ns6
- Dnsmasq: daemon doesn't start if NameServers property contains more than 2 addresses - Bug #2918 [NethServer]
- drop nethserver-tftp package and add tftp configuration in nethserver-dnsmasq - Enhancement #2916 [NethServer]
- DHCP: can't modify IP reservation - Bug #2914 [NethServer]

* Thu Oct 16 2014 Davide Principi <davide.principi@nethesis.it> - 1.2.1-1.ns6
- DHCP: port closed and missing fragment - Bug #2911 [NethServer]

* Wed Oct 15 2014 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.2.0-1.ns6
- Support DHCP on multiple interfaces - Feature #2849
- Avoid multiple static assignement with the same mac - Bug #2833

* Wed Aug 20 2014 Davide Principi <davide.principi@nethesis.it> - 1.1.2-1.ns6
- Firewall rules: preserve references to other DB records - Enhancement #2835 [NethServer]

* Wed Feb 05 2014 Davide Principi <davide.principi@nethesis.it> - 1.1.1-1.ns6
- Show text labels in DNS & DHCP - Enhancement #2642 [NethServer]
- RST format for help files - Enhancement #2627 [NethServer]
- Dashboard: new widgets - Enhancement #1671 [NethServer]

* Mon Dec 16 2013 Davide Principi <davide.principi@nethesis.it> - 1.1.0-1.ns6
- Fill DHCP reservation form from lease cache - Feature #1949 [NethServer]
- Display current lease status - Feature #1048 [NethServer]

* Tue Oct 22 2013 Davide Principi <davide.principi@nethesis.it> - 1.0.6-1.ns6
- VPN: support IPsec/L2TP - Feature #1957 [NethServer]

* Fri Jul 26 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.5-1.ns6
- Apply DNS changes from web UI #2052

* Tue Jul 16 2013 Davide Principi <davide.principi@nethesis.it> - 1.0.4-1.ns6
- Use dhcp-host dnsmasq option for fixed leases - Enhancement #1917 [NethServer]

* Wed Jun 12 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.3-1.ns6
- Use dhcp-host option for fixed leases #1917
- ip-mac-address validator is now case insensitive #1923

* Tue Apr 30 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.2-1.ns6
- Rebuild for automatic package handling. #1870
- Expand /etc/ethers template #1830

* Tue Mar 19 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.1-1
- Add migration code #1685
