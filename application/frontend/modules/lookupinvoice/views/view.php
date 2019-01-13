<form class="form-horizontal">
    <div class="container">
        <div class="row">
            <div class="form-group form-group-sm col-sm-6">
                <div class="row">
                    <label for="first_name" class="col-sm-3 col-form-label">First Name</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="first_name" name="first_name">
                    </div>
                </div>
            </div>
            <div class="form-group form-group-sm col-sm-6">
                <div class="row">
                    <label for="last_name" class="col-sm-3 col-form-label">Last Name</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="last_name" name="last_name">
                    </div>
                </div>
            </div>
            <div class="form-group form-group-sm col-sm-6">
                <div class="row">
                    <label for="Street" class="col-sm-3 col-form-label">Street</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="Street" name="Street">
                    </div>
                </div>
            </div>
            <div class="form-group form-group-sm col-sm-6">
                <div class="row">
                    <label for="City" class="col-sm-3 col-form-label">City</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="City" name="City">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

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
                    echo '<td><span class="ipdf" itc="' . $iv['templateCode'] . '" ino="' . $iv['invoiceNo'] . '" iid="' . $iv['invoiceId'] . '">pdf</span> | <span class="izip" itc="' . $iv['templateCode'] . '" ino="' . $iv['invoiceNo'] . '" iid="' . $iv['invoiceId'] . '">zip</span></td>';
                    echo '<td><span class="viewpdf" itc="' . $iv['templateCode'] . '" ino="' . $iv['invoiceNo'] . '" iid="' . $iv['invoiceId'] . '">View</span></td>';
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
<script type="text/javascript">
    $(document).ready(function () {
        $(document.body).on('click', '.ipdf', function () {
            getinvoice($(this).attr('iid'),$(this).attr('ino'),$(this).attr('itc'),'pdf');
        });
        $(document.body).on('click', '.izip', function () {
            getinvoice($(this).attr('iid'),$(this).attr('ino'),$(this).attr('itc'),'zip');
        });
        $(document.body).on('click', '.viewpdf', function () {
            getinvoice($(this).attr('iid'),$(this).attr('ino'),$(this).attr('itc'),'view');
        });

    });
    function getinvoice(iid, ino, itc, itype) {
        $.ajax({
            type: "POST",
            url: '<?= site_url('lookupinvoice/getinvoice') ?>',
            data: {
                'iid': iid,
                'ino': ino,
                'itc': itc,
                'itype': itype
            }
        }).done(function (r) {
            console.log(r);
            var obj = JSON.parse(r);
            if(obj.status === "success") {
                var ifile = obj.file;
                if(itype==='pdf') {
                    window.location = "<?=base_url();?>lookupinvoice/pdf/"+ifile;
                } else if(itype==='zip') {
                    window.location = "<?=base_url();?>lookupinvoice/zip/"+ifile;
                } if(itype==='view') {
                    window.open("<?=base_url();?>lookupinvoice/detail/"+ifile, '_blank');
                }
            }
        }).fail(function (x) {

        });
    }
</script>
