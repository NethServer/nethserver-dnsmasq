<?php

echo "<div class='dashboard-item'>";
echo $view->header()->setAttribute('template',$T('dns_dhcp_title'));
echo "<dl>";
echo "<dt>".$T('dns_status_label')."</dt><dd>"; echo $T('enabled'); echo "</dd>";
echo "<dt>".$T('nameservers_label')."</dt><dd>"; echo $T($view['dns_servers']); echo "</dd>";
echo "<dt class='spacer'>".$T('dhcp_status_label')."</dt><dd class='spacer'>";  echo "</dd>";
echo "</dl>";
echo "<ul style='clear: both'>";
foreach ($view['dhcp'] as $i => $props) {
    $status = $props['status'];
    if ($status == 'disabled') {
        echo "<li><span class='dhcp-bold'>$i</span>: ".$T('disabled')."</li>";
    } else {
        echo "<li><span class='dhcp-bold'>$i</span>: ".$props['start']." - ".$props['end']."</li>";
    }
}
echo "</ul>";
echo "</div>";

$view->includeCss("
    .dashboard-item .spacer {
        margin-top: 8px;
    }

    .dashboard-item .dhcp-bold {
        font-weight: bold;
        padding-left: 5px;
    }
");

