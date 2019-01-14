<?php

foreach ($rows as $iv) {
    echo "<tr>";
    echo '<td><span class="ipdf" itc="' . $iv['templateCode'] . '" ino="' . $iv['invoiceNo'] . '" iid="' . $iv['invoiceId'] . '">pdf</span> | <span class="izip" itc="' . $iv['templateCode'] . '" ino="' . $iv['invoiceNo'] . '" iid="' . $iv['invoiceId'] . '">zip</span></td>';
    echo '<td><span class="viewpdf" itc="' . $iv['templateCode'] . '" ino="' . $iv['invoiceNo'] . '" iid="' . $iv['invoiceId'] . '">View</span></td>';
    foreach ($iv as $v) {
        echo "<td>$v</td>";
    }
    echo "<tr>";
}