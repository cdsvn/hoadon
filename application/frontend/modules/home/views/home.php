<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered">
            <thead>
                <tr>
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
