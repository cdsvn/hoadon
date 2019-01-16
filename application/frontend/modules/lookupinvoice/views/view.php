<style>
    #area-data {
        border: 3px solid #46b8da;
        border-radius: 5px;
    }
    #tbl-content, #tbl-content td, #tbl-content th {
        border: 1px solid #ddd;
        padding: 3px 3px;
    }
    #tbl-content {
        border-collapse: collapse;
        border-color: #ddd;
    }
    #tbl-content > tbody > tr:nth-child(even) {
        background: #f5f5f5;
    }
    #tbl-content > tbody > tr:nth-child(odd) {
        background: #FFF;
    }

    #pagination {
        padding: 15px 0;
    }
    #pagination a {
        padding: 4px 10px;
        border-top: 1px solid #dee2e6;
        border-right: 1px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;
    }
    #pagination a.current {
        text-decoration: none;
        cursor: default;
        background-color: #f5f5f5;
    }
    #pagination a:first-child {
        border-left: 1px solid #dee2e6;
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;
    }
    #pagination a:last-child {
        border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
    }
    #enddate, #startdate {
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
        background-color: #fff;
    }
    .ipdf, .izip, .viewpdf {
        cursor: pointer;
        text-transform: capitalize;
        color: #138496;
    }
</style>
<div id="area-data">
    <form class="form-horizontal" style="background-color: #f5f5f5; padding-top: 15px; padding-bottom: 10px;">
        <div class="container">
            <div class="row">
                <div class="form-group form-group-sm col-sm-4">
                    <div class="row">
                        <label for="first_name" class="col-sm-5 col-form-label">Invoice No.</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control ifform" id="invoiceno" name="invoiceno">
                        </div>
                    </div>
                </div>
                <div class="form-group form-group-sm col-sm-4">
                    <div class="row">
                        <label for="Street" class="col-sm-5 col-form-label">Invoice Seri</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control ifform" id="invoiceseri" name="invoiceseri">
                        </div>
                    </div>
                </div>
                <div class="form-group form-group-sm col-sm-4">
                    <div class="row">
                        <label for="last_name" class="col-sm-5 col-form-label">Invoice Type</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control ifform" id="invoicetype" name="invoicetype">
                        </div>
                    </div>
                </div>
                <div class="form-group form-group-sm col-sm-4">
                    <div class="row">
                        <label for="City" class="col-sm-5 col-form-label">Buyer Tax Code</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control ifform" id="buyertaxcode" name="buyertaxcode">
                        </div>
                    </div>
                </div>
                <div class="form-group form-group-sm col-sm-4">
                    <div class="row">
                        <label for="City" class="col-sm-5 col-form-label">Template Code</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control ifform" id="templatecode" name="buyername">
                        </div>
                    </div>
                </div>
                <div class="form-group form-group-sm col-sm-4">
                    <div class="row">
                        <label for="City" class="col-sm-5 col-form-label">Buyer ID No</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control ifform" id="buyeridno" name="buyeridno">
                        </div>
                    </div>
                </div>                
                <div class="form-group form-group-sm col-sm-4">
                    <div class="row">
                        <label for="City" class="col-sm-5 col-form-label">From Date</label>
                        <div class="col-sm-7">
                            <div id="dpkstartdate" class="input-group date" data-date-format="dd-mm-yyyy">
                                <input type="text" class="form-control ifform" id="startdate" name="startdate" readonly />                               
                                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                            </div>                            
                        </div>
                    </div>
                </div>
                <div class="form-group form-group-sm col-sm-4">
                    <div class="row">
                        <label for="City" class="col-sm-5 col-form-label">To Date</label>
                        <div class="col-sm-7">
                            <div id="dpkenddate" class="input-group date" data-date-format="dd-mm-yyyy">
                                <input type="text" class="form-control ifform" id="enddate" name="enddate" readonly />                               
                                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                </div>   
                <div class="col-md-4">
                    <div class="btn-group float-right mt-2" role="group">
                        <a class="btn btn-info btn-md" id="ibtn-search" href="#">
                            <i class="fa fa-search" aria-hidden="true"></i> Search</a>
                        <a class="btn btn-md btn-warning" id="ibtn-refresh" href="#">
                            <i class="fa fa-refresh" aria-hidden="true"></i> Refresh</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="container">   
        <div class="row">
            <div class="col-md-7">
                <div style="padding: 15px;">                    
                    <select id="srip" class="form-control" style="display: inline-block; width: 50px; height: 32px; padding: 3px 5px;">
                        <?php
                        for ($i = 1; $i < 5; $i++) {
                            $rip = $i * 5;
                            echo '<option ' . ($rip == $rowInPage ? 'selected' : '') . ' value="' . $rip . '">' . $rip . '</option>';
                        }
                        ?>
                    </select> rows/page
                    &nbsp;&nbsp;
                    <span id="spagetop">0</span>/<span id="spagebottom">0</span> page(s) 
                    &nbsp;&nbsp;
                    From <span id="sfrom"></span> to <span id="sto"></span> on <span id="sall"></span> record(s)
                </div>
            </div>  
            <div class="col-md-5">
                <div id="pagination" class="float-right pagination"></div>
            </div>            
            <div class="col-md-12">
                <div style="overflow: auto;">
                    <table id="tbl-content" style="width: 7200px;">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">No.</th>
                                <th class="text-center" style="width: 100px;">Download</th>
                                <th class="text-center">View</th>
                                <th style="width: 120px;">Invoice Type</th>
                                <th style="width: 150px;">Adjustment Type</th>
                                <th style="width: 130px;">Template Code</th>
                                <th style="width: 120px;">Invoice Seri</th>
                                <th style="width: 130px;">Invoice Number</th>
                                <th style="width: 120px;">Invoice No</th>
                                <th style="width: 120px;">Currency</th>
                                <th style="width: 120px;">Total</th>
                                <th style="width: 120px;">Issue Date</th>
                                <th style="width: 120px;">Issue Date Str</th>
                                <th>State</th>
                                <th>Request Date</th>
                                <th>Description</th>
                                <th>Buyer Id No</th>
                                <th>State Code</th>
                                <th>Subscriber Number</th>
                                <th>Payment Status</th>
                                <th>View Status</th>
                                <th>Download Status</th>
                                <th>Exchange Status</th>
                                <th>Num Of Exchange</th>
                                <th>Create Time</th>
                                <th>Contract Id</th>
                                <th>Contract No</th>
                                <th>Supplier Tax Code</th>
                                <th>Buyer Tax Code</th>
                                <th>Total Before Tax</th>
                                <th>Tax Amount</th>
                                <th>Tax Rate</th>
                                <th>Payment Method</th>
                                <th>Payment Time</th>
                                <th>Customer Id</th>
                                <th style="width: 300px;">Buyer Name</th>
                                <th>No</th>
                                <th>Payment Status</th>
                            </tr>
                        </thead>
                        <tbody id="grid">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $(document.body).on('click', '.ipdf', function () {
            getinvoice($(this).attr('iid'), $(this).attr('ino'), $(this).attr('itc'), 'pdf');
        });
        $(document.body).on('click', '.izip', function () {
            getinvoice($(this).attr('iid'), $(this).attr('ino'), $(this).attr('itc'), 'zip');
        });
        $(document.body).on('click', '.viewpdf', function () {
            getinvoice($(this).attr('iid'), $(this).attr('ino'), $(this).attr('itc'), 'view');
        });
        $(document.body).on('click', '#srip', function () {
            var rip = $(this).val();
            $.ajax({
                type: "POST",
                url: '<?= site_url('lookupinvoice/setrowinpage') ?>',
                data: {'rip': rip}
            }).done(function (r) {
                getgrid(1);
            }).fail(function (x) {
            });
        });
        $(document.body).on('click', 'a[data-ci-pagination-page]', function (e) {
            e.preventDefault();
            var page = $(this).attr('data-ci-pagination-page');
            getgrid(page);
        });
        $(document.body).on('click', '#ibtn-search', function () {
            getgrid(1);
        });
        $(document.body).on('click', '#ibtn-refresh', function () {
            refreshform();
            getgrid(1);
        });
        $("#dpkstartdate").datepicker({
            autoclose: true,
            todayHighlight: true
        }).datepicker('update', new Date(Date.now() - 864e5));
        $("#dpkenddate").datepicker({
            autoclose: true,
            todayHighlight: true
        }).datepicker('update', new Date());
        getgrid(1);
    });
    function refreshform() {
        $('.ifform').val('');
    }
    function getsearch() {
        var obj = {};
        $('.ifform').each(function () {
            var tag = $(this).prop("tagName").toLowerCase();
            var id = $(this).attr('id');
            if (tag === 'input' || tag === 'select') {
                obj[id] = $(this).val().trim();
            }
        });
        return obj;
    }
    function getgrid(page) {
        var filter = getsearch();
        $.ajax({
            type: "POST",
            url: '<?= site_url('lookupinvoice/grid') ?>',
            data: {'page': page, filter: JSON.stringify(filter)}
        }).done(function (r) {
            var obj = JSON.parse(r);
            $('#grid').html(obj.grid);
            if (obj.hasOwnProperty('pagination')) {
                $('#pagination').html(obj.pagination);
                $('#sfrom').html(obj.from);
                $('#sto').html(obj.to);
                $('#sall').html(obj.totalRow);
                $('#spagetop').html(obj.page);
                $('#spagebottom').html(obj.pages);
            }
        }).fail(function (x) {

        });
    }
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
            if (obj.status === "success") {
                var ifile = obj.file;
                if (itype === 'pdf') {
                    window.location = "<?= base_url(); ?>lookupinvoice/pdf/" + ifile;
                } else if (itype === 'zip') {
                    window.location = "<?= base_url(); ?>lookupinvoice/zip/" + ifile;
                }
                if (itype === 'view') {
                    window.open("<?= base_url(); ?>lookupinvoice/detail/" + ifile, '_blank');
                }
            }
        }).fail(function (x) {

        });
    }
</script>
