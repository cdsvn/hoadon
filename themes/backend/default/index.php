<!DOCTYPE html>
<html>
    <head>
        <title><?= configs('SITE_NAME');?></title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url(); ?>files/share/<?= configs('ICO_BROWSER');?>">
        <link href="<?php echo url_tmpl(); ?>resources/css/reset.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo url_tmpl(); ?>resources/css/grid.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo url_tmpl(); ?>resources/css/font.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo url_tmpl(); ?>resources/css/custom.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo url_tmpl(); ?>resources/plugins/dialogbox/css/dialogbox.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo url_tmpl(); ?>resources/plugins/jquery/3.1.1/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo url_tmpl(); ?>resources/plugins/dialogbox/js/dialogbox.js"></script>
        <script src="<?php echo url_tmpl(); ?>resources/plugins/gui.js"></script>
    </head>
    <body style="overflow: hidden;">
        <div class="uil <?= ($_SESSION['opensibar']) ? '' : 'close_left'; ?>" id="uil">
            <div class="uil_top clearfix">
                <?php echo Modules::run('includes/Includes/uil_top'); ?>
            </div>
            <div class="uil_body">
                <?php echo Modules::run('includes/Includes/uil_body'); ?>
            </div>
            <div class="uil_bottom">
                <?php echo Modules::run('includes/Includes/uil_bottom'); ?>
            </div>
        </div>
        <div class="uir <?= ($_SESSION['opensibar']) ? '' : 'open_right'; ?>" id="uir">
            <div class="uir_top">
                <?php echo Modules::run('includes/Includes/uir_top'); ?>
            </div>
            <div class="uir_body">
                <?php echo $content; ?>
            </div>
            <div class="uir_bottom">
                <?php echo Modules::run('includes/Includes/uir_bottom'); ?>
            </div>
        </div>
        <div id="blockui" style="display: none;"></div>
        <script>
            var url_tmpl = '<?= url_tmpl(); ?>';
            var offleft = 291;
            var path_js = '<?= url_tmpl(); ?>';
            var path_ns = '<?= admin_url() . '/' . $this->admin->lang; ?>';
            $(document).ready(function () {
                $(document.body).gui();
            });
        </script>
    </body>
</html>