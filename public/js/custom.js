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
    resetHelperPlugin: function () {
        $(".chzn-select").chosen();
        $(".timeago").timeago();
    },
    uploadClickButton: function () {
        $('#upload-input').click();
    },
    launchModal: function (view, title) {
        $('#modal-container .modal-body').html(view);
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
    launchNotification: function (text, title, klass) {
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
    }
};

$(document).ready(function () {
    if (typeof PNotify !== 'undefined') {
        PNotify.prototype.options.styling = "bootstrap3";
        PNotify.prototype.options.styling = "fontawesome";
    }
    
    $(".chzn-select").chosen();
    $(".chzn-select-deselect").chosen({allow_single_deselect: true});
    $("time.timeago").timeago();
});

$(function () {
    var noMoreText = 'aucun autre élément';
    var morebox = $('.morebox');
    var placeholder = $('.p-liste #placeholder');
    var ID = 20;
    var paginationUrl;

    var page = morebox.data('page');
    var url = morebox.data('url');

    var href = new URI(window.location.href);
    href.addSearch('viewHtml', 1);

    paginationUrl = url + href.search();

    if (url !== 'undefined' && page !== 'undefined') {
        $('.btn-more').on('click', function () {
            var that = this;
            $(that).addClass('loader-infini');
            $(that).find('span').css('display', 'none');
            $.ajax({
                type: "GET",
                url: paginationUrl,
                data: {
                    offset: ID
                },
                success: function (response) {
                    var html = response.html.trim();
                    if (html !== '') {
                        var li_length = placeholder.find('.item-seq').length;
                        placeholder.append(html);
                        $(that).removeClass('loader-infini');
                        $(that).find('span').css('display', '');
                        ID += 20;
                        if (li_length % ID) {
                            morebox.html('<em>' + noMoreText + '</em>');
                        }
                        // reset timeago and chosen plugin for new element
                        utils.resetHelperPlugin();
                    } else {
                        morebox.html('<em>' + noMoreText + '</em>');
                    }
                },
                error: function (xhr, message, error) {
                    morebox.html('<em>:( Oups! Un problème est survenu, réessayez plus tard</em>');
                }
            });
        });
    }
});