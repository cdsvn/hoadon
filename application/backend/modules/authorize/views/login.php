<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link href="<?php echo url_tmpl(); ?>resources/css/font_login.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo url_tmpl(); ?>resources/plugins/dialogbox/css/dialogbox.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo url_tmpl(); ?>resources/css/login.css" rel="stylesheet" type="text/css" />
        <script src="<?php echo url_tmpl(); ?>resources/plugins/jquery/3.1.1/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo url_tmpl(); ?>resources/plugins/dialogbox/js/dialogbox.js"></script>
        <style>
            body {
                font-family: 'Arsenal Regular';
                font-size: 19px;
                background: #edeff1;
                background: url(<?= base_url(); ?>files/backend/default/login/bg_login.jpg);
                background-position: center center;
                background-repeat: no-repeat;
            }
            input {
                font-family: 'Arsenal Regular';
                font-size: 18px;
            }
            .hide_capcha {
                display: none !important;
            }
        </style>
    </head>
    <body>
        <div style="width: 100%; height: 100%; display: table;">
            <div class="modal">
                <form class="modal-content" action="action_page.php">
                    <div class="imgcontainer text-center">
                        <img style="max-width: 220px;" src="<?= base_url(); ?>files/share/logo.png" />
                    </div>
                    <div class="container">
                        <input type="hidden" id="csrf_token" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>">
                        <!--                        <label><b>Username</b> <span id="user_required" class="login_required">required</span></label>-->
                        <input type="text" placeholder="<?= $this->lang->line('enteruser'); ?>" name="uname" id="username" value="<?= !empty($username) ? $username : '' ?>" required>
                        <!--                        <label><b>Password</b> <span id="pass_required" class="login_required">required</span></label>-->
                        <input type="password" autocomplete="off" placeholder="<?= $this->lang->line('enterpassword'); ?>" name="psw" id="password" value="<?= !empty($password) ? $password : '' ?>" required>
                        <label class="rcap <?= ($show_capcha) ? '' : 'hide_capcha'; ?>">
                            <b>Capcha</b> <span id="ccha_required" class="login_required"><?= $this->lang->line('required'); ?></span>
                            <img id="refresh_capcha" src="<?= base_url(); ?>/files/backend/default/refresh.png" />
                        </label>
                        <input class="rcap <?= ($show_capcha) ? '' : 'hide_capcha'; ?>" type="text" placeholder="<?= $this->lang->line('entercapcha'); ?>" name="ccha" id="capcha" value="" required>
                        <div class="rcap <?= ($show_capcha) ? '' : 'hide_capcha'; ?>" id="wrap_capcha">
                            <?= $capcha; ?>
                        </div>
                        <button type="button" id="btn_login"><?= $this->lang->line('btn_login'); ?></button>
                        <label for="remember"><input type="checkbox" id="remember" <?= !empty($remember) ? 'checked' : '' ?>> <?= $this->lang->line('rememberme'); ?></label>
                    </div>
                </form>
            </div>
        </div>
        <div id="uiloading" style="width: 100%; height: 100%; position: absolute; display: none; top:0; right: 0; left: 0; bottom: 0; background: rgba(85, 85, 85, 0.5);">
            <div style="width: 100%; height: 100%; display: flex; justify-content: center; align-items: center;">
                <div class="loader"></div>
            </div>
        </div>
        <script>
            var url_tmpl = '<?php echo url_tmpl(); ?>';
            /*
             * 0: Login fail (wrong password or account)
             * 1: Login pass
             * 2: Account several similar names
             * 3: Login fail 5 times, lock account
             * 4: Account not active
             * 5: Change password requests (first login)
             * 6: Wrong capcha
             */
            $(document).ready(function () {
                $("#refresh_capcha").click(function () {
                    var thisobj = $(this);
                    thisobj.addClass('refresh-animate');
                    $('#wrap_capcha').html('<img style="padding-top: 17px;" src="<?= url_tmpl() ?>resources/images/loading/loading.gif" />');
                    var ajax_data = {};
                    ajax_data[$('#csrf_token').attr('name')] = $('#csrf_token').val();
                    $.ajax({
                        async: false,
                        type: 'POST',
                        url: '<?php echo admin_url($this->admin->lang . "/authorize/refreshcapcha") ?>',
                        data: ajax_data
                    }).done(function (r) {
                        $('#wrap_capcha').html(r);
                        thisobj.removeClass('refresh-animate');
                    }).fail(function (x) {
                        thisobj.removeClass('refresh-animate');
                    });
                });
                $("#btn_login").click(function () {
                    var username = $("#username").val().trim();
                    var password = $("#password").val().trim();
                    var capcha = $("#capcha").val().trim();
                    if (username.length === 0) {
                        $("#user_required").show();
                        $("#username").addClass('boder_required');
                    }
                    if (password.length === 0) {
                        $("#pass_required").show();
                        $("#password").addClass('boder_required');
                    }
                    if (capcha.length === 0) {
                        $("#ccha_required").show();
                        $("#capcha").addClass('boder_required');
                    }
                    var remember = $("#remember").prop("checked") ? 1 : 0;
                    var ajax_data = {username: username, password: password, capcha: capcha, remember: remember};
                    ajax_data[$('#csrf_token').attr('name')] = $('#csrf_token').val();
                    $("#uiloading").show();
                    $.ajax({
                        type: 'POST',
                        crossDomain: true,
                        url: '<?php echo admin_url($this->admin->lang . "/authorize/login") ?>',
                        data: ajax_data
                    }).done(function (r) {
                        var msg = '';
                        if (r === "1") {
                            msg = '<?= $this->lang->line('login_successs'); ?>';
                            window.location = '<?php echo admin_url($this->admin->lang . "/home") ?>';
                        } else if (r.indexOf("0-") !== -1) {
                            if (parseInt(r.split("-")[1]) > 5) {
                                $('.rcap').removeClass('hide_capcha');
                            }
                            msg = '<?= $this->lang->line('wronginfo'); ?>';
                        } else if (r === "2") {
                            msg = '<?= $this->lang->line('login_samename'); ?>';
                        } else if (r === "3") {
                            msg = '<?= $this->lang->line('col_fail5'); ?>';
                        } else if (r === "4") {
                            msg = '<?= $this->lang->line('login_deactive'); ?>';
                        } else if (r === "5") {
                            msg = '<?= $this->lang->line('login_changepass'); ?>';
                        } else if (r === "6") {
                            msg = '<?= $this->lang->line('wrongcapcha'); ?>';
                        }
                        $("#uiloading").hide();
                        if (msg !== '') {
                            $.dialogbox.prompt({
                                content: msg,
                                time: 3000
                            });
                        }
                    }).fail(function (x) {
                        $("#uiloading").hide();
                        var msg = '<?= $this->lang->line('login_fail'); ?>';
                        $.dialogbox.prompt({
                            content: msg,
                            time: 3000
                        });
                    });
                });
                $('#username').focus(function () {
                    $(this).removeClass("boder_required");
                    $("#user_required").hide();
                });
                $('#password').focus(function () {
                    $("#pass_required").hide();
                    $(this).removeClass("boder_required");
                });
                $('#capcha').focus(function () {
                    $("#ccha_required").hide();
                    $(this).removeClass("boder_required");
                });
                $(document).keypress(function (e) {
                    var keycode = (e.keyCode ? e.keyCode : e.which);
                    if (keycode == '13') {
                        $('#btn_login').click();
                    }
                });
            });
            function create_capcha(callback) {
                $.ajax({
                    async: false,
                    type: 'POST',
                    url: '<?php echo admin_url($this->admin->lang . "/authorize/refreshcapcha") ?>',
                    data: {}
                }).done(function (r) {
                    $('#wrap_capcha').html(r);
                    callback();
                }).fail(function (x) {
                    callback();
                });
            }
            function stoprefresh() {
                $("#refresh_capcha").removeClass('refresh-animate');
            }
        </script>
    </body>
</html>