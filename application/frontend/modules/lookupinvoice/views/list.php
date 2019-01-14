<?php

foreach ($rows as $iv) {
    echo "<tr>";
    echo '<td><span class="ipdf" itc="' . $iv['templateCode'] . '" ino="' . $iv['invoiceNo'] . '" iid="' . $iv['invoiceId'] . '">pdf</span> | <span class="izip" itc="' . $iv['templateCode'] . '" ino="' . $iv['invoiceNo'] . '" iid="' . $iv['invoiceId'] . '">zip</span></td>';
    echo '<td><span class="viewpdf" itc="' . $iv['templateCode'] . '" ino="' . $iv['invoiceNo'] . '" iid="' . $iv['invoiceId'] . '">View</span></td>';
    foreach ($iv as $k=>$v) {
    	if($k=='createTime') {
    		$v = date("d-m-Y H:i", 1388516401);
    	}
        echo "<td>$v</td>";
    }
    echo "<tr>";
}