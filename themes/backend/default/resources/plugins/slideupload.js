(function ($) {
    $.fn.slideupload = function (options) {
        var rootElement = this;
        var defaults = {
            pathfile: '',
            btnaddrow: '#addrow',
            storedslide: {},
            storedoldnameremove: [],
            oldrow: '',
        };
        var settings = $.extend({}, defaults, options); //Gop tham so
        var index_row = 0;
        //Ham xu ly cho plugin
        var methods = {
            init: function () {},
            load: function () {},
            addRow: function () {},
            removeRow: function () {},
            getData: function () {},
            getOldNameRemove: function () {},
        };
        //Trien khai ham init
        methods.init = function () {
            methods.load();
            $(settings.btnaddrow).click(function(evt) {
                evt.preventDefault();
                methods.addRow();
            })
        };
        //Trien khai ham load
        methods.load = function () {
            if(settings.oldrow != '') {
                var or = JSON.parse(settings.oldrow);
                var srow = rootElement.find('tbody.srow');
                var defaultRow = rootElement.find('.clone-me');
                for(var r in or) {
                    methods.addRow(or[r]);
                }
            }
        };
        // Add row (from db and click button add new)
        methods.addRow = function(obj) {
            var srow = rootElement.find('tbody.srow');
            var defaultRow = rootElement.find('.clone-me');
            var row = defaultRow.clone();
            row.removeClass('clone-me');
            $('.pic input', row).attr("name", "pic_"+index_row);
            if(typeof obj !== 'undefined') {
                $("td", row).each(function(){
                    var data_type = $(this).attr("data-type");
                    var key_col = $(this).attr("data-col");
                    if(data_type == "textarea" || data_type == "text") {
                        $(this).children().val(obj[key_col]);
                    } else if(data_type == "checkbox") {
                        $(this).children('input').prop("checked", obj[key_col]);
                    } else if(data_type == "image") {
                        $(this).children('img').attr("src", settings.pathfile+obj[key_col]);
                        $(this).children('img').attr("name", obj[key_col]);
                        $(this).children('img').addClass("slimgold");
                    }
                });
            }
            row.attr("irow", index_row++);
            row.appendTo(srow).show();
            $('.fun button', row).click(function () {
                methods.removeRow(row);
            });
            $('.pic img', row).click(function () {
                $('.pic input', row).click();
            });
            $('.pic input', row).change(function (evt) {
                var files = evt.target.files;
                var ir=row.attr("irow");
                for (var i = 0, f; f = files[i]; i++) {
                    if (!f.type.match('image.*'))
                        continue;
                    // remember old file change, to remove file from store folder
                    if($('.pic img', row).hasClass("slimgold")) {
                        $('.pic img', row).removeClass("slimgold");
                        settings.storedoldnameremove.push($('.pic img', row).attr("name"));
                    }
                    // insert file to array to upload
                    settings.storedslide[ir]=f;
                    // read and show file
                    var reader = new FileReader();
                    reader.onload = (function (theFile) {
                        return function (e) {
                            $('.pic img', row).attr("src", e.target.result);
                            $('.pic img', row).attr("name", theFile.name);
                        };
                    })(f);
                    reader.readAsDataURL(f);
                }
            });
        };
        methods.removeRow = function (row) {
            row = $(row);
            var ir=row.attr("irow");
            // remember old name file to remove file from store folder
            if($('.pic img', row).hasClass("slimgold")) {
                settings.storedoldnameremove.push($('.pic img', row).attr("name"));
            }
            // remove element in object store
            if(settings.storedslide.hasOwnProperty(ir)) {
                delete settings.storedslide[ir];
            }
            // remove row
            row.remove();
        };
        methods.getData = function() {
            var srow = rootElement.find('tbody.srow');
            var data_slide = {};
            $('tr', srow).each(function(){
                var obj = {};
                var row = $(this);
                var ir = row.attr("irow");
                $("td", row).each(function(){
                    var data_type = $(this).attr("data-type");
                    var key_col = $(this).attr("data-col");
                    if(data_type == "textarea" || data_type == "text") {
                        obj[key_col] = $(this).children().val();
                    } else if(data_type == "checkbox") {
                        obj[key_col] = $(this).children("input").prop("checked");
                    } else if(data_type == "image") {
                        obj[key_col] = $(this).children("img").attr("name");
                    }
                });
                data_slide[ir] = obj;
            });
            return data_slide;
        };
        methods.getOldNameRemove = function() {
            return settings.storedoldnameremove;
        };
        methods.getNewFile = function() {
            return settings.storedslide;
        };
        methods.init();
        //Ham xu ly cho plugin
        return methods; //Tra ve jQuery object
    };
})(jQuery);