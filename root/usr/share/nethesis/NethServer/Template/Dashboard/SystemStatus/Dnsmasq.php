<?php

echo "<div class='dashboard-item'>";
echo $view->header()->setAttribute('template',$T('dns_dhcp_title'));
echo "<dl>";
echo "<dt>".$T('dns_status_label')."</dt><dd>"; echo $T('enabled'); echo "</dd>";
echo "<dt>".$T('nameservers_label')."</dt><dd>"; echo $T($view['dns_servers']); echo "</dd>";
echo "<dt class='spacer'>".$T('dhcp_status_label')."</dt><dd class='spacer'>"; echo $T($view['dhcp_status']); echo "</dd>";
if ($view['dhcp_status'] == 'enabled') {
    echo "<dt>".$T('dhcp_start_label')."</dt><dd>"; echo $T($view['dhcp_start']); echo "</dd>";
    echo "<dt>".$T('dhcp_end_label')."</dt><dd>"; echo $T($view['dhcp_end']); echo "</dd>";
}
echo "</dl>";
echo "</div>";

$view->includeCss("
    .dashboard-item .spacer {
        margin-top: 8px;
    }
");

