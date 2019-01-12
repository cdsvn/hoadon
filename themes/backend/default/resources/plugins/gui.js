(function ($) {
    $.fn.gui = function (options) {
        var rootElement = this;
        var defaults = {
            h_header: 42,
            h_footer: 22,
            l_offset: 291
        };
        // Merge and override parameters
        var settings = $.extend({}, defaults, options);
        // Declare variable
        var h_header = 42;
        var h_footer = 22;
        // Define mothods
        var methods = {
            init: function () {},
            load: function () {},
            setHeigtBodyLeft: function () {},
            setHeigtBodyRight: function () {},
            setHeigtGridData: function () {},
            gridScrollAndResize: function () {},
            setGridTableBody: function () {}
        };
        // Initialize the necessary parameters
        methods.init = function () {
            methods.load();
            $("#toggle_ui").click(function () {
                $("#uil").toggleClass('close_left');
                $("#uir").toggleClass('open_right');
                var open = 'yes';
                if ($("#uil").hasClass("close_left")) {
                    settings.l_offset = 66;
                    $(".arr").css("display", "none");
                    open = 'no';
                } else {
                    settings.l_offset = 291;
                    setTimeout(function () {
                        $(".arr").css("display", "block");
                    }, 400);
                }
                $.ajax({
                    type: 'POST',
                    url: path_ns + '/home/opensibar',
                    data: {open: open}
                }).done(function (r) {
                }).fail(function (x) {
                });
            });
            $(window).resize(function () {
                var h_w = $(window).height();
                methods.setHeigtBodyLeft(h_w);
                methods.setHeigtBodyRight(h_w);
            });
        };
        // Load ui default
        methods.load = function () {
            $(".arr").css("display", "block");
            var h_w = $(window).height();
            methods.setHeigtBodyLeft(h_w);
            methods.setHeigtBodyRight(h_w);
        };
        methods.setHeigtBodyLeft = function (h_w) {
            $(".uil_body").height(h_w - (settings.h_header + settings.h_footer));
        };
        methods.setHeigtBodyRight = function (h_w) {
            var uir_body = h_w - (settings.h_header + settings.h_footer) - 20;
            $(".uir_body").height(uir_body);
            if ($('#ui_form').length > 0 && $('#ui_grid').length > 0) {
                methods.setHeigtGridData(h_w);
                methods.gridScrollAndResize();
            } else if ($('#ui_form').length > 0) {
                var h_form_header = $("#ui_form .box_header").outerHeight(true);
                $("#ui_form .box_body").height(uir_body - h_form_header - 25);
            }
        };
        methods.setHeigtGridData = function (h_w) {
            // auto resize grid
            var h_form = $("#ui_form").outerHeight(true);
            var h_header_grid = $("#grid_header").outerHeight(true);
            var h_pagination = $("#footer_grid").outerHeight(true);
            var padding = 43;
            var grid = h_w - (settings.h_header + h_header_grid + h_pagination + h_form + settings.h_footer + padding);
            $("#grid_body").height(grid);
        };
        methods.gridScrollAndResize = function () {
            var pressed = false;
            var start = undefined;
            var startX, startWidth;
            var hw = $("#headertable").outerWidth();
            var icell = 0;
            $("table#headertable th").mousedown(function (e) {
                icell = $("table th").index($(this));
                if (icell > 2) {
                    start = $(this);
                    pressed = true;
                    startX = e.pageX;
                    startWidth = $(this).outerWidth();
                    hw = $("#headertable").outerWidth();
                    $(start).addClass("resizing");
                    $(start).addClass("noSelect");
                }
            });
            $(document).mousemove(function (e) {
                if (pressed) {
                    var plus = (e.pageX - startX);
                    var sum = startWidth + plus;
                    $(start).width(sum);
                    $("#headertable").width(hw + plus);
                    $("#bodytable").width(hw + plus);
                    $("#bodytable > thead > tr:first th:nth-child(" + (icell + 1) + ")").width(sum);
                    $("#bodytable > tbody > tr:first td:nth-child(" + (icell + 1) + ")").width(sum);
                }
            });
            $(document).mouseup(function () {
                if (pressed) {
                    $(start).removeClass("resizing");
                    $(start).removeClass("noSelect");
                    pressed = false;
                }
            });
            $("#headertable").on("click", ".sort_txt_asc", function (e) {
                e.preventDefault();
            });
            methods.setGridTableBody();
            $(".table-body").scroll(function () {
                $(".table-header").offset({left: -1 * this.scrollLeft + settings.l_offset});
            });
        };
        methods.setGridTableBody = function () {
            $(".table-body").height($(".inner-container").height() - $(".table-header").height());
        };
        // Call default init
        methods.init();
        // Return method to call from object
        return methods;
    }
})(jQuery);