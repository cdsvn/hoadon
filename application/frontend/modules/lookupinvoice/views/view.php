<div class="row">
    <div class="col-md-12" style="overflow: auto;">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Download</th>
                    <th>View</th>
                    <?php
                    foreach ($rs['invoices'][0] as $k => $v) {
                        echo "<th>$k</th>";
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($rs['invoices'] as $iv) {
                    echo "<tr>";
                    echo '<td><span class="ipdf" iid="' . $iv['invoiceNo'] . '">pdf</span> | <span class="ipdf" iid="' . $iv['invoiceNo'] . '">zip</span></td>';
                    echo "<td>View</td>";
                    foreach ($iv as $v) {
                        echo "<td>$v</td>";
                    }
                    echo "<tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

