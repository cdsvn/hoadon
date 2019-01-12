(function ($) {
    $.View = function (options) {

        var defaults = {
            _ns: '',
            _ns_full: '',
            _form_default: '#main-form'
        },
        plugin = this,
                options = options || {};

        plugin.init = function () {
            var settings = $.extend({}, defaults, options);
            settings['buis'] = {};
            $.data(document, 'View', settings);
        }

        plugin.grid = function () {}

        plugin.validateFormData = function () {}

        plugin.setFormData = function () {}

        plugin.resetFormData = function () {}

        plugin.blockUI = function () {}

        plugin.init();

    }

    function _validateEmail(emailAddress) {
        var emailRegex = new RegExp(/^([\w\.\-]+)@([\w\-]+)((\.(\w){2,3})+)$/i);
        var valid = emailRegex.test(emailAddress);
        if (!valid) {
            return false;
        } else {
            return true;
        }
    }

    function _validateInteger(x) {
        if ((parseFloat(x) == parseInt(x)) && !isNaN(x)) {
            return true;
        } else {
            return false;
        }
    }

    function _validateDecimal(x) {
        if (parseFloat(x) && !isNaN(x)) {
            return true;
        } else {
            return false;
        }
    }

    $.View.grid = function (page, search, sort) {
        $.View.blockUI("#ui_grid", true);
        if (page == undefined) {
            page = $("#pagination > a.current").html();
        }
        if (search == undefined) {
            var settings = ($.data(document, 'View'));
            search = $.View.validateFormData(settings._form_default, false, function () {});
        }
        if (sort == undefined) {
            sort = find_sort();
        }
        $.ajax({
            type: 'POST',
            url: _ns_full + '/grid',
            data: {page: page, sort: JSON.stringify(sort), search: JSON.stringify(search)}
        }).done(function (r) {
            var obj = jQuery.parseJSON(r);
            $("#gridview").html(obj.grid);
            $("#bodytable").width($("#headertable").width());
            $("#pagination").html(obj.pagination);
            $("#fromr").html(obj.fromr);
            $("#tor").html(obj.tor);
            $("#allr").html(obj.allr);
            $("#cka").prop("checked", false);
            $.View.blockUI("#ui_grid", false);
        }).fail(function (x) {
            $.View.blockUI("#ui_grid", false);
        });
    }

    function find_sort() {
        var obj = {};
        $('.sort_col').each(function (i) {
            if ($(this).hasClass("sort_asc")) {
                obj[$(this).attr("sort")] = 'ASC';
                return obj;
            } else if ($(this).hasClass("sort_desc")) {
                obj[$(this).attr("sort")] = 'DESC';
                return obj;
            }
        });
        return obj;
    }

    function deletes(ids) {
        if (ids == '') {
            $.dialogbox.prompt({
                content: 'Please select row want delete',
                time: 2000
            });
        } else {
            $.dialogbox({
                type: 'msg',
                title: 'Message',
                content: 'Are you sure you want to delete the selected record(s)?',
                closeBtn: true,
                btn: ['Confirm', 'Cancel'],
                call: [
                    function () {
                        $.dialogbox.close();
                        $.View.blockUI("#ui_grid", true);
                        $.ajax({
                            type: 'POST',
                            url: _ns_full + '/delete',
                            data: {ids: ids}
                        }).done(function (r) {
                            $.View.blockUI("#ui_grid", false);
                            if (r == 1) {
                                $.View.grid(1, {});
                            } else {
                                alert('Delete error');
                            }
                        }).fail(function (x) {
                            $.View.blockUI("#ui_grid", false);
                            alert('Delete fail');
                        });
                    },
                    function () {
                        $.dialogbox.close();
                    }
                ]
            });
        }
    }

    $.View.init = function (grid, callback) {
        console.log('Init view js...');
        if (grid === false) {

        } else {
            // load grid
            $.View.grid(1);
            // click pagination
            $(document.body).on('click', 'a[data-ci-pagination-page]', function (e) {
                e.preventDefault();
                var page = $(this).attr('data-ci-pagination-page');
                $.View.grid(page);
            });
            // click checkbox all
            $("#cka").click(function () {
                $(".cke").prop("checked", $(this).prop("checked"));
            });
            // clicl checkbox element
            $("#bodytable").on("click", ".cke", function () {
                if ($(".cke").length === $('input.cke:checked').length) {
                    $("#cka").prop("checked", true);
                } else {
                    $("#cka").prop("checked", false);
                }
            });
            $("#headertable").on("click", ".sort_col", function () {
                var me = $(this);
                if (me.hasClass("sort_asc")) {
                    $(".sort_col").removeClass("sort_desc").removeClass("sort_asc");
                    me.removeClass("sort_asc").addClass("sort_desc");
                } else {
                    $(".sort_col").removeClass("sort_desc").removeClass("sort_asc");
                    me.removeClass("sort_desc").addClass("sort_asc");
                }
                $.View.grid();
            });
            $("#deletes_btn").click(function () {
                var arr = [];
                $('input.cke:checked').each(function () {
                    arr.push($(this).val());
                })
                deletes(arr.join());
            });
            $("#bodytable").on("click", ".deleterow", function () {
                var id = $(this).attr('idrd');
                deletes(id);
            });
        }
        callback();
    }

    $.View.validateFormData = function (selector, validate, callback) {
        var obj = {};
        var msg = {};
        var form = $(selector);
        $(".fdata", form).each(function () {
            var tagName = $(this).prop("tagName");
            var id = $(this).attr("id");
            if (tagName == 'DIV') {
                var type = $(this).attr("type");
                if (type == "radio") {
                    var name = $(this).attr("name");
                    var val = $('input[type=radio][name=' + name + ']:checked').val();
                    obj[id] = (val == undefined) ? '' : val;
                } else if (type == "checkbox") {
                    var checked = [];
                    $('input[type=checkbox]:checked', $(this)).each(function () {
                        checked.push($(this).val());
                    });
                    obj[id] = checked.join();
                }
            } else if (tagName == 'INPUT') {
                var type = $(this).attr("type");
                if (type == "radio") {
                    var name = $(this).attr("name");
                    if (!obj.hasOwnProperty(name)) {
                        var val = $('input[type=radio][name=' + name + ']:checked').val();
                        obj[name] = (val == undefined) ? '' : val;
                    }
                } else if (type == "checkbox") {
                    obj[id] = ($(this).prop("checked")) ? $(this).val().trim() : '';
                } else {
                    obj[id] = $(this).val().trim();
                }
            } else if (tagName == 'SELECT') {
                obj[id] = $(this).val();
            } else if (tagName == 'TEXTAREA') {
                if ($(this).hasClass('ckeditor')) {
                    obj[id] = CKEDITOR.instances[id].getData();
                } else {
                    obj[id] = $(this).val();
                }
            }
            if (validate == true) {
                var required = $(this).attr('data-required');
                var selector = $(this);
                if (typeof required !== typeof undefined && required !== false) {
                    var arr_required = required.split(",");
                    var sub_msg = {};
                    if (required.length > 0) {
                        for (var key in arr_required) {
                            var sub_msg_key = arr_required[key];
                            var sub_msg_msg = selector.attr("msg-" + sub_msg_key);
                            sub_msg[sub_msg_key] = sub_msg_msg;
                        }
                    }
                    msg[id] = sub_msg;
                }
            }
        });
        callback(obj, msg);
        if (validate == true) {
            for (var key in obj) { // lap qua tat ca cac phan tu tren form
                if (msg.hasOwnProperty(key)) { // neu phan tu nay co message
                    for (var msg_key in msg[key]) {
                        var notice = '';
                        if (msg_key == 'empty') {
                            if (obj[key].length == 0) {
                                notice = msg[key][msg_key];
                            }
                        } else if (msg_key == 'number') {
                            if (_validateInteger(obj[key]) == false) {
                                notice = msg[key][msg_key];
                            }
                        } else if (msg_key == 'decimal') {
                            if (_validateDecimal(obj[key]) == false) {
                                notice = msg[key][msg_key];
                            }
                        } else if (msg_key == 'email') {
                            if (_validateEmail(obj[key]) == false) {
                                notice = msg[key][msg_key];
                            }
                        } else if (msg_key == 'plus') {
                            notice = msg[key][msg_key];
                        }
                        if (notice != '') {
                            $.dialogbox({
                                type: 'msg',
                                title: 'Required',
                                icon: 1,
                                content: notice,
                                btn: ['Confirm'],
                                call: [
                                    function () {
                                        $.dialogbox.close();
                                    }
                                ]
                            });
                            return false;
                        }
                    }
                }
            }
        }
        return obj;
    }

    $.View.setFormData = function (record) {
        var settings = ($.data(document, 'View'));
        $(".fdata", $(settings._form_default)).each(function () {
            var tagName = $(this).prop("tagName");
            var id = $(this).attr("id");
            if (record.hasOwnProperty(id)) {
                if (tagName == 'DIV') {
                    var type = $(this).attr("type");
                } else if (tagName == 'INPUT') {
                    var type = $(this).attr("type");
                    if (type == "radio") {

                    } else if (type == "checkbox") {

                    } else if (type != "password") {
                        $(this).val(record[id]);
                    }
                } else if (tagName == 'SELECT') {
                    $(this).val(record[id]);
                } else if (tagName == 'TEXTAREA') {
                    if ($(this).hasClass('ckeditor')) {
                        CKEDITOR.instances[id].setData(record[id]);
                    } else {
                        $(this).val();
                    }
                }
            }
        });
    }

    $.View.resetFormData = function () {
        $.View.blockUI("#ui_form", true);
        var settings = ($.data(document, 'View'));
        $(".fdata", $(settings._form_default)).each(function () {
            var tagName = $(this).prop("tagName");
            var id = $(this).attr("id");
            if (tagName == 'DIV') {
                var type = $(this).attr("type");
            } else if (tagName == 'INPUT') {
                var type = $(this).attr("type");
                if (type == "radio") {

                } else if (type == "checkbox") {

                } else {
                    $(this).val("");
                }
            } else if (tagName == 'SELECT') {
                $(this).val("");
            } else if (tagName == 'TEXTAREA') {
                if ($(this).hasClass('ckeditor')) {
                    CKEDITOR.instances[id].setData("");
                } else {
                    $(this).val("");
                }
            }
        });
        $.View.blockUI("#ui_form", false);
    }

    $.View.blockUI = function (eselector, status) {
        var settings = ($.data(document, 'View'));
        var buis = settings['buis'];
        if (status == true) {
            selector = $(eselector);
            var offset = selector.offset();
            var width = selector.outerWidth();
            var height = selector.outerHeight();
            if (buis.hasOwnProperty(eselector)) {
                var bui = buis[eselector];
            } else {
                var bui = $("#blockui").clone();
                settings['buis'][eselector] = bui;
                $.data(document, 'View', settings);
            }
            bui.addClass('blockui').css({"left": offset.left, "top": offset.top, "display": "block"}).width(width).height(height).appendTo("body");
        } else {
            if (buis.hasOwnProperty(eselector)) {
                var bui = buis[eselector];
                bui.remove();
            }
        }
    }

}(jQuery));
