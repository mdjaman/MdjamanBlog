/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 Marcel Djaman
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
window.utils = {
    capitalizeFirstLetter: function (string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    },
    launchModal: function (view, title) {
        $('#modal-container .modal-body').html(view);
        $('#modal-container .modal-header h4').html(title);
        $("#modal-container").modal({
            backdrop: 'static',
            keyboard: false
        });
    },
    launchModalAsIt: function (title) {
        $('#modal-container .modal-header h4').html(title);
        $("#modal-container").modal({
            backdrop: 'static',
            keyboard: false
        });
    },
    displayValidationErrors: function (messages) {
        for (var key in messages) {
            if (messages.hasOwnProperty(key)) {
                this.addValidationError(key, messages[key]);
            }
        }
        this.showAlert('Warning!', 'Fix validation errors and try again', 'alert-warning');
    },
    launchNotification: function (title, text, klass) {
        var opts = {
            text: text,
            type: "success",
            addclass: "stack-bottomleft",
            hide: true,
            stack: false,
            buttons: {
                closer: true,
                sticker: true
            }
        };
        if (typeof title !== undefined) {
            opts.title = title;
        }
        if (typeof klass !== undefined) {
            opts.type = klass;
        }
        new PNotify(opts);
    },
    addValidationError: function (field, message) {
        var controlGroup = $('#' + field).parent().parent();
        controlGroup.addClass('error');
        $('.help-inline', controlGroup).html(message);
    },
    removeValidationError: function (field) {
        var controlGroup = $('#' + field).parent().parent();
        controlGroup.removeClass('error');
        $('.help-inline', controlGroup).html('');
    },
    showAlert: function (title, text, klass) {
        $('.alert').removeClass("alert-error alert-warning alert-success alert-info");
        $('.alert').addClass(klass);
        $('.alert .content').html('<strong>' + title + '</strong> ' + text);
        $('.alert').show();
    },
    hideAlert: function () {
        $('.alert').hide();
    },
    getPictureThumbUrl: function (input) {
        var uri = URI(input);
        var inputPath = uri.path();
        var filename = inputPath.substr(0, inputPath.lastIndexOf('.')) || inputPath;
        var ext = inputPath.split('.').pop();
        return filename + '_thumb.' + ext;
    },
    getRelativeTime: function (time, format) {
        format = (typeof format === 'undefined') ? 'YYYY-MM-DD HH:mm:ssZ' : format;
        moment.locale('fr');
        var datetime = moment(time, format);
        var timeago = datetime.fromNow();
        return '<time class="timeago" datetime="' + datetime + '">' + timeago +  '</time>';
    },
    resetHelperPlugin: function () {
        $(".chzn-select").chosen();
        $(".chzn-select-deselect").chosen({allow_single_deselect: true});
        $("time.timeago").timeago();
        $('input[data-type*="date"], .datepicker').datepicker({
            format: 'dd-mm-yyyy',
            language: 'fr',
            todayBtn: "linked"
        });
    },
    notification: function (aTitle, aMsg, aType) {
        var bottomleft = {
            dir1: "right",
            dir2: "up",
            push: "top"
        };

        var opts = {
            title: aTitle,
            text: aMsg,
            type: aType ? aType : "success",
            addclass: "stack-bottomleft",
            stack: bottomleft,
            animate_speed: 'fade',
            sticker: false
        };
        $.pnotify(opts);
    }
};

moment.locale('fr');

$(function () {
    if (typeof PNotify !== 'undefined') {
        PNotify.prototype.options.styling = "bootstrap3";
        PNotify.prototype.options.styling = "fontawesome";
    }

    $(".chzn-select").chosen();
    $(".chzn-select-deselect").chosen({allow_single_deselect: true});
    $("time.timeago").timeago();

    $("[rel*=popover]").popover();

    $("a[rel*=tooltip]").tooltip({
        placement: 'top'
    });

    $('input[data-type*="date"], .datepicker').datepicker({
        format: 'dd-mm-yyyy',
        language: 'fr',
        todayBtn: true,
        todayHighlight: true,
        toggleActive: true
    });

    $('#modal-container').on('shown.bs.modal', function (e) {
        utils.resetHelperPlugin();
    });

    $(".tm-input").tagsManager({
        CapitalizeFirstLetter: false
    });
});

$(function () {
    $("a[href^='http:']:not([href^='http://" + window.location.host + "'])").each(function () {
        $(this).attr("target", "_blank");
        $(this).attr("rel", "nofollow");
    });

    var $form = $("form").not('.navbar-form, .filter-form');
    if ($form) {
        var $inputs = $form.find("input, select, textarea"),
            formChanged = false,
            submited = false;

        if ($inputs.prop("required"))
            window.utils.showAlert('', 'Les champs marqués (<span class="warning">*</span>) sont obligatoires', 'alert-info');

        $form.find('input, select, textarea').on('change', function (e) {
            formChanged = true;
        });

        $form.submit(function () {
            submited = true;
        });

        window.onbeforeunload = function () {
            if (formChanged && !submited) {
                var message = "Etes-vous sûr de vouloir quitter cette page? Des données non enregistrées seront perdues.";
                return message;
            }
        };
    }

    $(".alert .close").on("click", function (e) {
        window.utils.hideAlert();
        e.preventDefault();
    });

});

$(function () {
    $('a#toggle-state').bind('click', function(e) {
        var that = this;
        var row = $(that).parents('tr');
        var id = row.attr('id');
        var state = $(that).data('state');
        $.ajax({
            url: $(that).data('url'),
            type: 'POST',
            cache: false,
            data: {
                state: state
            },
            success: function (response) {
                if (response.status === true) {
                    var text = (state === 1) ? 'activé' : 'desactivé';
                    var bntText = (state === 1) ? 'Desactiver' : 'Activer';
                    $(that).text(bntText);
                    $(that).data('state', (state === 1) ? 0 : 1);
                    row.find("span#state").text(text);
                } else {
                    utils.launchNotification('Erreur', response.msg, 'error');
                }
            },
            error: function () {
                utils.launchNotification('Erreur', ':( Une erreur survenue', 'error');
            }
        });
        e.preventDefault();
    });

    $(document).on('submit', '#form-entity', function (e) {
        $(this).find('button[type="submit"]').prop('disabled', true);
    });
});

var MyBootstrapTable = function(selector, ajaxUrl, cols, actionsHtml) {
    this.selector = selector;
    this.url = ajaxUrl;
    this.cols = cols;
    var defaultHtml = '<a role="button" class="btn btn-primary btn-sm" id="btn-edit">' +
        '<i class="fa fw fa-pencil"></i></a> ' +
        '<a role="button" class="btn btn-danger btn-sm" id="btn-delete">' +
        '<i class="fa fw fa-times"></i></a>';
    this.actionsHtml = actionsHtml || defaultHtml;
}

MyBootstrapTable.prototype.init = function (callback) {
    var self = this;
    if (!self.selector instanceof jQuery) {
        this.selector = $(this.selector);
    }

    $.each(self.cols, function(i, val) {
        val.sortable = true;

        if (val.field === '') {
            val.sortable = false;
        }

        if (val.field === 'id') {
            val.visible = false;
        }

        if (val.field === 'created_at') {
            val.formatter = function(value) {
                var format = "YYYY-MM-DD HH:mm:ss";
                var m = moment(value, format);
                return utils.getRelativeTime(m.format());
            };
        }
    });

    self.cols.push({
        field: '',
        title: '',
        formatter: function() {
            return self.actionsHtml;
        }
    });

    self.selector.bootstrapTable({
        locale: 'fr-FR',
        striped: true,
        pagination: true,
        selectItemName: 'btnSelectItem',
        search: false,
        showColumns: true,
        showRefresh: true,
        sidePagination: 'server',
        pageSize: 20,
        showToggle: true,
        pageList: [10, 25, 50, 100, 'All'],
        dataField: 'data',
        idField: 'id',
        uniqueId: 'id',
        method: 'get',
        url: this.url,
        columns: this.cols,
        rowAttributes: function(row) {
            return {
                'data-id': row.id
            };
        },
        queryParams: function(params) {
            if (typeof params.sort !== 'undefined') {
                params.dir = params.order;
            }

            delete params.order;
            return params;
        }
    });

    if (callback) {
        callback();
    }

}

MyBootstrapTable.prototype.getData = function (useCurrentPage, callback) {
    if (!this.selector instanceof jQuery) {
        this.selector = $(this.selector);
    }
    var data = this.selector.bootstrapTable('getData', useCurrentPage || false);

    if (callback) {
        callback(data);
    }

    return data;
}

MyBootstrapTable.prototype.getRow = function (id, callback) {
    if (!this.selector instanceof jQuery) {
        this.selector = $(this.selector);
    }
    var row = this.selector.bootstrapTable('getRowByUniqueId', id);

    if (callback) {
        callback(row);
    }

    return row;
}

MyBootstrapTable.prototype.appendRow = function (data, callback) {
    var fields = _.pluck(this.cols, 'field');
    var row = {};

    _.each(data, function(val, i) {
        if (_.indexOf(fields, i) !== -1) {
            row[i] = val;
        }
    });

    if (!this.selector instanceof jQuery) {
        this.selector = $(this.selector);
    }
    this.selector.bootstrapTable('append', row);

    if (callback) {
        callback();
    }
}

MyBootstrapTable.prototype.editRow = function (data, callback) {
    var fields = _.pluck(this.cols, 'field');
    var row = {};

    _.each(data, function(val, i) {
        if (_.indexOf(fields, i) !== -1) {
            row[i] = val;
        }
    });

    if (!this.selector instanceof jQuery) {
        this.selector = $(this.selector);
    }
    var params = {
        id: data.id,
        row: data
    };
    this.selector.bootstrapTable('updateByUniqueId', params);

    if (callback) {
        callback();
    }
}

MyBootstrapTable.prototype.removeRow = function (id, callback) {
    if (!this.selector instanceof jQuery) {
        this.selector = $(this.selector);
    }
    this.selector.bootstrapTable('removeByUniqueId', id);

    if (callback) {
        callback();
    }
}
