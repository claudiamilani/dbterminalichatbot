/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

import $ from "jquery";

try {
    $('body').on('hidden.bs.modal', '.modal', function () {
        $(this).removeData('bs.modal');
        $('.modal .modal-title').html('Loading...');
        $('.modal .my-modal-content').html("<p class='text-center'><img alt='Loading' src='/images/ajax-loader.gif'></p>");
    }).on('show.bs.modal', function (e) {
        var source = $(e.relatedTarget);
        $(source.attr('data-target') + ' .modal-content').load(source.attr('href'), function (response, status, xhr) {
            if (status === 'error') {
                var message = ''
                switch (xhr.status) {
                    case 401:
                        message = '<i class="fas fa-warning"></i><br>Accesso negato. La sessione potrebbe essere scaduta, <a onclick="window.location.reload()" style="cursor: pointer">ricarica la pagina</a>'
                        break;
                    case 403:
                        message = '<i class="fas fa-warning"></i><br>Operazione non consentita'
                        break;
                    case 404:
                        message = '<i class="fas fa-question-circle"></i><br>Pagina non trovata'
                        break;
                    case 500:
                        message = '<i class="fas fa-times-circle"></i><br>Errore del server'
                        break;
                    case 503:
                        message = '<i class="fas fa-hourglass"></i><br>Servizio temporaneamente non disponibile'
                        break
                    default:
                        message = 'Si è verificato un problema durante il caricamento del contenuto. Riprovare più tardi.'
                }
                $('.modal-content').html('<div class="modal-header bg-warning"><h4 class="modal-title">Errore</h4></div><div class="modal-body"><div class="container-fluid"><div class="row"><div class="col-md-12 my-modal-content"><p class="text-center">'+message+'</p></div></div></div></div></div>');

            }
        });
    }).on('shown.bs.modal', function (e) {
        window.lft_should_not_refresh = true;
    });
    $.fn.modal.Constructor.prototype.enforceFocus = function () {
    };
} catch (e) {

}


window.parseAjaxErrors = function (response, type = null) {
    try {

        if (response.statusText === 'timeout') {
            try {
                toastr.error('Richiesta in Timeout. Riprovare tra qualche secondo', 'Errore Generico')
            } catch (e) {
                alert('Errore Generico');
            }
            return;
        }
        var errorMessages = JSON.parse(response.responseText);
    } catch (e) {
        try {
            toastr.error(errorMessages, 'Errore ' + e)
        } catch (e) {
            alert(response.responseText);
        }
        return;
    }
    if (typeof errorMessages === "string") {
        try {
            toastr.error(errorMessages, 'Errore')
            return;
        } catch (e) {
            alert(response.responseText);
        }
    }
    if (typeof errorMessages === "object" && errorMessages !== null) {
        switch (type) {
            case null:
            case 'toastr':
                for (let prop in errorMessages) {
                    if (Array.isArray(errorMessages[prop])) {
                        errorMessages[prop].forEach(function (item) {
                            try {
                                toastr.error(item, 'Errore');
                            } catch (e) {
                                alert(item);
                            }
                        });
                    } else {
                        try {
                            toastr.error(errorMessages[prop], 'Errore')
                        } catch (e) {
                            alert('Generic error!');
                            return;
                        }
                    }
                }
                break;
            case 'session':
                return errorMessages;
        }
    } else {
        try {
            toastr.error(errorMessages, 'Errore Generico')
        } catch (e) {
            alert('Errore Generico:' + errorMessages);
        }
    }


};

window.s2addAndSelect = function (selector, id, text) {
    if ($(selector).find("option[value='" + id + "']").length) {
        $(selector).val(id).trigger('change');
    } else {
        // Create a DOM Option and pre-select by default
        var newOption = new Option(text, id, true, true);
        // Append it to the select
        $(selector).append(newOption).trigger('change');
    }
};

$(function () {
    $('.table-responsive').on('shown.bs.dropdown', function (e) {
        var t = $(this),
            m = $(e.target).find('.dropdown-menu'),
            tb = t.offset().top + t.height(),
            mb = m.offset().top + m.outerHeight(true),
            d = 20; // Space for shadow + scrollbar.
        if (t[0].scrollWidth > t.innerWidth()) {
            if (mb + d > tb) {
                t.css('padding-bottom', ((mb + d) - tb));
            }
        } else {
            t.css('overflow', 'visible');
        }
    }).on('hidden.bs.dropdown', function () {
        $(this).css({
            'padding-bottom': '',
            'overflow': ''
        });
    });
});

// Prevent propagation of events to expandableTable when clicking a child anchor element
$(document).on('click', '[data-widget="expandable-table"]', function (e) {
    if($(e.target).is('a')){
        e.stopImmediatePropagation();
    }
})