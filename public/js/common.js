"use strict"

function Common() {
    if (typeof Common.instance === 'object') return Common.instance
    Common.instance = this;
    // window.addEventListener("pageshow", function (event) {
    //     var historyTraversal = event.persisted ||
    //         (typeof window.performance != "undefined" &&
    //             window.performance.navigation.type === 2);

    //     if (!historyTraversal) {
    //         Common.instance.Notify();
    //     }
    // })
}

Common.prototype.table = function (element, columns, parameters = {}, callbackFunction = null, customOptions = null, filter = true, dataCallback = null, initCompleteCallback = null) {
    var url = $(element).attr('data-url');

    if ($(element + '_wrapper')[0]) {
        $(element).DataTable().ajax.reload();
        return;
    }

    $.fn.dataTable.Api.register('column().title()', function () {
        return $(this.header()).text().trim();
    });

    if ($(window).width() < 376) {
        $.fn.DataTable.ext.pager.numbers_length = 5;
    }

    var searchCols = [];
    columns.forEach((column, i) => {
        if (column.title === trans('message.status')) {
            column.className += " minW-80"
        }
        if (column.name == 'number') {
            column.className = 'no-wrap'
        }
        if (column.title === trans('message.actions') && typeof column.buttons != 'undefined') {
            column.className = column.className ?? "" + " text-center";
            column.createdCell = function (td, cellData, rowData, row, col) {
                $(td).empty();
                let div = $('<div />').addClass('nt-actions');
                let buttons = column.buttons;
                if (typeof buttons.download != 'undefined') {
                    $(div).append(
                        $('<a />')
                            .addClass('btn btn-sm btn-clean btn-icon')
                            .html('<i class="la la-download"></i>')
                            .attr('data-toggle', 'tooltip')
                            .attr('title',trans('message.download'))
                            .attr('href', url + '/' + cellData + '/download')
                            .attr('target', '_blank')
                        // .attr('download', rowData.original_name)
                        // .on('click', function (e) {
                        //     e.preventDefault();
                        //     if (buttons.download == true) {
                        //         window.open(url + '/' + cellData + '/download');
                        //     } else {
                        //         buttons.download(td, cellData, rowData, row, col);
                        //     }
                        // })
                    )
                }

                if (typeof buttons.edit != 'undefined') {
                    $(div).append(
                        $('<a />')
                            .addClass('btn btn-sm btn-clean btn-icon')
                            .html('<i class="la la-pencil"></i>')
                            .attr('data-toggle', 'tooltip')
                            .attr('title', trans('message.edit'))
                            .on('click', function (e) {
                                e.preventDefault();
                                if (typeof buttons.edit == 'function') {
                                    buttons.edit(td, cellData, rowData, row, col);
                                } else {
                                    location.href = url + "/" + cellData;
                                }
                            })
                    )
                }

                if (typeof buttons.view != 'undefined') {
                    $(div).append(
                        $('<a />')
                            .addClass('btn btn-sm btn-clean btn-icon')
                            .html('<i class="far fa-eye"></i>')
                            .attr('data-toggle', 'tooltip')
                            .attr('title', trans('message.view'))
                            .on('click', function (e) {
                                e.preventDefault();
                                if (typeof buttons.view == 'function') {
                                    buttons.view(td, cellData, rowData, row, col);
                                } else {
                                    location.href = url + "/" + cellData;
                                }
                            })
                    )
                }

                if (typeof buttons.destroy != 'undefined') {
                    if (typeof buttons.destroy.renderCondition != 'undefined' && !buttons.destroy.renderCondition(td, cellData, rowData, row, col)) {
                        return '';
                    }
                    $(div).append(
                        $('<a />')
                            .addClass('btn btn-sm btn-clean btn-icon')
                            .html('<i class="la la-trash"></i>')
                            .attr('data-toggle', 'tooltip')
                            .attr('title', trans('message.delete'))
                            .on('click', function (e) {
                                e.preventDefault();
                                if (typeof buttons.destroy == 'function') {
                                    buttons.destroy(td, cellData, rowData, row, col);
                                } else if (typeof buttons.destroy == 'object' && buttons.destroy.table_child_id) {
                                    Common.prototype.destroy(url + "/" + cellData, function () {
                                        if (rowData[buttons.destroy.parent_column] == buttons.destroy.parent_data) {
                                            $(element).DataTable().ajax.reload()
                                        } else {
                                            $(buttons.destroy.table_child_id).DataTable().ajax.reload()
                                        }
                                    });
                                } else if (typeof buttons.destroy.renderCondition != 'undefined' && typeof buttons.destroy.callback != 'undefined') {
                                    buttons.destroy.callback(td, cellData, rowData, row, col);
                                }
                                else {
                                    Common.prototype.destroy(url + "/" + cellData, function () {
                                        $(element).DataTable().ajax.reload()
                                    });
                                }
                            })
                    )
                }

                if (typeof buttons.estimate != 'undefined' ) {
                    let isShowEstimateButton = false;
                    isShowEstimateButton = rowData.estimates_approve.length
                    if(isShowEstimateButton) {
                        $(div).append(
                            $('<a />')
                                .addClass('btn btn-sm btn-clean btn-icon')
                                .html('<i class="fas fa-file-invoice"></i>')
                                .attr('data-toggle', 'tooltip')
                                .attr('title', trans('message.estimate.view'))
                                .on('click', function (e) {
                                    e.preventDefault();
                                    if (typeof buttons.estimate == 'function') {
                                        buttons.estimate(td, cellData, rowData, row, col);
                                    } else {
                                        let estimateApproved = sortDateByField(rowData.estimates_approve, 'approved_date')
                                        location.href = "/estimates/" + estimateApproved[0].number;
                                    }
                                })
                        )
                    }
                }

                if (typeof buttons.expand != 'undefined') {
                    if (rowData[buttons.expand.data] > 0) {
                        $(div).append(
                            $('<a />')
                                .addClass('btn btn-sm btn-clean btn-icon')
                                .html('<i class="la la-angle-down details-control"></i>')
                                .attr('data-toggle', 'tooltip')
                                .attr('title', trans('message.show_partner'))
                                .on('click', function() {
                                    $(this).children('i').toggleClass('la-angle-down la-angle-up')
                                    buttons.expand.render(td, cellData, rowData, row, col, table, columns, parameters)
                                })
                        )
                    }
                }
                $(td).append(div)
            }
        }

        if (typeof column.evolution !== 'undefined' && column.evolution) {
            let badgeColor = parameters['colors'] || {}
            column.render = function (data, _type, _full, _meta) {
                let jobStatusTxt = '';
                let _options = column.search.options;
                $.each(_options, function (value, text) {
                    if (value == data && value.length > 0) jobStatusTxt = text;
                });
                if (Common.prototype.isEmpty(column.badge)) return jobStatusTxt;
                let style = '';
                if (badgeColor[data]) {
                    let color = badgeColor[data];
                    style = `color: ${color}!important; background-color: ${color}10!important;border-color:${color}!important`
                }
                return `<span class="border rounded border-light kt-font-dark w-100 kt-badge kt-badge--md kt-badge--unified-light kt-font-dark kt-badge--inline" style="${style}">${jobStatusTxt}</span>`;
            }
        }

        let search = column.search;
        if (typeof search === 'undefined' || typeof search.value === 'undefined') return;
        searchCols[i] = { "search": search.value };
    });

    let language = {}
    if (window.locale == 'en') {
        language = {
            'lengthMenu': '_MENU_',
            "info": '_TOTAL_ records',
            "aria": {
                "sortDescending": " - click/return to sort descending"
            }
        };
    } else {
        // Use local file to fix for CORS policy
        language = {
            url: '/js/Japanese.json'
        };
    }

    let options = {
        // responsive: true,
        ordering: false,
        scrollX: true,
        bSort: true,
        dom: "<'row'<'col-sm-12'tr>>\
        <'row p-3'<'col-sm-12 col-md-5 d-flex align-items-left'p><'col-sm-12 col-md-7 dataTables_pager d-flex justify-content-start justify-content-md-end mt-md-0 toolbar text-right'l>>",
        lengthMenu: [[5, 10, 25, 50], ['5 / ' + trans('message.page'), '10 / ' + trans('message.page'), '25 / ' + trans('message.page'), '50 / '+ trans('message.page')]],
        pageLength: 10,
        language: language,
        order: [],
        processing: true,
        serverSide: true,
        ajax: {
            url: url,
            type: 'GET',
            cache: false,
            data: function (d) {
                // set current page to param object
                let pages = $(element).DataTable().page.info()
                Object.assign(d, { page: (pages.page + 1) });
                if (typeof parameters === 'object') {
                    Object.assign(d, parameters);
                }
                if (dataCallback) {
                    dataCallback(d)
                }
            },
            error: function (error) {
                $(`${element}_processing`).hide();
                if (error.responseJSON.error) {
                    toastr.error(error.responseJSON.error);
                }
            }
        },

        searchCols: searchCols,
        columns: columns,
        initComplete: function () {
            function delay(callback, ms) {
                var timer = 0;
                return function () {
                    var context = this,
                        args = arguments;
                    clearTimeout(timer);
                    timer = setTimeout(function () {
                        callback.apply(context, args);
                    }, ms || 0);
                };
            }

            // render input go to
            let html = `<div class="d-flex">
                            <span class="mr-1 fw-400" style="line-height: 33px;">${trans('message.go_to')}</span>
                            <input type="text" id="pageJump" class="form-control form-control-sm fw-500 fs-15 go-to-input">
                        </div>`
            if (window.locale == 'ja') {
                html = `<div class="d-flex">
                            <input type="text" id="pageJump" class="form-control form-control-sm fw-500 fs-15 go-to-input">
                            <span class="mr-1 fw-400" style="line-height: 33px;">${trans('message.go_to')}</span>
                        </div>`
            }
            $("div.toolbar").append(html);

            $('#pageJump').on('keypress',function(e) {
                if(e.which == 13) {
                    table.page(parseInt($(this).val())-1).draw(false);
                }
            })

            var rowFilter = $('<tr />').addClass('filter').appendTo($(table.table().header()));

            // set search columns
            if (filter) {
                this.api().columns().every(function (index) {
                    let th = $('<th>')
                        .addClass('text-center')
                        .appendTo(rowFilter);
                    if (this.title() === trans('message.actions')) {
                        $('<button />')
                            .addClass('btn btn-secondary btn-sm btn-icon btn-pill m-0')
                            .attr('type', 'button')
                            .html('<i class="la la-refresh"></i>')
                            .on('click', function (e) {
                                e.preventDefault();
                                let _filter = $(this).parents('.dataTables_wrapper').find('.dataTables_scrollHead .filter');
                                if (typeof _filter != 'undefined') {
                                    _filter.find('.kt-input').each(function (_index) {
                                        if (typeof columns[_index] != 'undefined') {
                                            table.column(_index).search(columns[_index].active ? columns[_index].active : '', false, false);
                                        }
                                        $(this).val($(this).data('col-active') ? $(this).data('col-active') : '');
                                    });
                                }
                                table.table().order([]).draw();
                            })
                            .appendTo(th);

                        return;
                    }

                    let search = columns[index].search;
                    if (!search) return

                    if (typeof search === 'undefined') {
                        search = {
                            type: 'text'
                        }
                    }

                    switch (search.type) {
                        case 'text':
                        case 'number':
                            $('<input/>')
                                .addClass('form-control form-control-sm form-filter kt-input kt-input-search')
                                .attr('type', search.type)
                                .attr('data-col-index', index)
                                .attr('style', 'min-width:80px')
                                .attr('placeholder', trans('message.text.search_for'))
                                .val(search.value === 'undefined' ? '' : search.value)
                                .keyup(delay(function (_e) {
                                    searchTable($(this).val(), index);
                                }, 700))
                                .appendTo(th);

                            break;

                        case 'dropdowns':
                            let select = $('<select/>')
                                .addClass('form-control form-control-sm form-filter kt-input')
                                .attr('data-col-index', index)
                                .on('change', function () {
                                    searchTable($(this).val(), index);
                                })
                                .appendTo(th)

                            if (typeof search.options === 'undefined') return;
                            $.each(search.options, function (value, text) {
                                let option = $('<option />')
                                    .attr('value', value)
                                    .text(text)
                                    .attr('selected', (typeof search.value != 'undefined' && search.value == value) ? 'selected' : null)
                                if (text === trans('message.all')) {
                                    select.prepend(option)
                                } else {
                                    select.append(option)
                                }

                            });
                            if (typeof search.value != 'undefined') {
                                select.val(search.value).trigger('change');
                            }
                            break;
                        case 'date':
                            let dateElement = $('<div />')
                                .addClass('input-group-sm kt-input-icon kt-input-icon--right')
                                .attr('data-z-index', '1100')
                                .append(
                                    $('<input />')
                                        .addClass('form-control form-filter')
                                        .addClass(search.class ?? '')
                                        .attr({
                                            'name': search.name ?? '',
                                            'readOnly': search.readonly ?? false,
                                            'disabled': search.disabled ?? false,
                                            'type': 'text',
                                            'autocomplete': 'off',
                                            'placeholder': trans('message.text.search_for')
                                        })
                                )
                                .append(
                                    $('<span />').addClass('kt-input-icon__icon kt-input-icon__icon--right')
                                        .append(
                                            $('<span />')
                                                .append(
                                                    $('<i />').addClass('la la-calendar')
                                                )
                                        )
                                )
                                .appendTo(th)
                                .on( 'change', function () {
                                    var v = dateElement.find('input').datepicker({ dateFormat: 'dd-mm-yy' }).val();  // getting search input value
                                    searchTable(v, index);
                                } );
                            if (typeof search.id != 'undefined') {
                                dateElement.find('input').attr('id', search.id)
                            }
                            if (typeof search.dateFormat == 'undefined' || search.dateFormat == 'date') {
                                dateElement.find('input').datepicker({
                                    todayHighlight: true,
                                    autoclose: true,
                                    pickerPosition: 'bottom-left',
                                    todayBtn: "linked",
                                    format: 'yyyy-mm-dd'
                                });
                            } else {
                                if (search.dateFormat == 'time') {
                                    dateElement.find('input').timepicker({
                                        defaultTime: 'current',
                                        minuteStep: 1,
                                        icons:
                                            {
                                                up: 'la la-angle-up',
                                                down: 'la la-angle-down'
                                            },
                                        showSeconds: true,
                                        showMeridian: false,
                                    });
                                } else {
                                    dateElement.find('input').datetimepicker({
                                        todayHighlight: true,
                                        autoclose: true,
                                        pickerPosition: 'bottom-left',
                                        todayBtn: "linked",
                                        format: 'yyyy-mm-dd hh:ii:ss'
                                    });
                                }
                            }
                            break;
                    }


                });
            }

            var setWidthResponsive = () => {
                let dataTables_scrollHeadInner = $(table.table().header()).parents('.dataTables_scrollHeadInner');
                if (dataTables_scrollHeadInner[0]) {
                    if (dataTables_scrollHeadInner.width() < ($(document).width() - 95)) {
                        dataTables_scrollHeadInner.css('width', '100%');
                    }
                }
            }
            setWidthResponsive();
            window.onresize = setWidthResponsive;

            if (initCompleteCallback) {
                initCompleteCallback()
            }
        },

        columnDefs: columns,

        drawCallback: function (settings) {
            if (callbackFunction) {
                callbackFunction(settings);
            }
        },
    }

    if (customOptions !== null && typeof customOptions == 'object') {
        Object.assign(options, customOptions);
    }

    var table = $(element).DataTable(options);

    return table;
}

Common.prototype.modal = function (options, successCallback = null, _errorCallback = null) {
    options = Object.assign({
        saveBtn: true,
        closeButton: true,
        showCompleteAjax: true,
    }, options);

    let _header = $('<div />')
        .addClass('modal-header d-flex align-items-center border-0 p-0')
        .append(
            $('<h5 />').addClass('modal-title kt-font-bold kt-font-xl').text(options.title ?? 'Panel')
        )
        .append(
            $('<button />')
                .addClass('close')
                .attr({
                    'type': 'button',
                    'data-dismiss': 'modal',
                    'aria-label': trans('message.close'),
                })
        );

    let _body = $('<div />').addClass('modal-body px-0 pb-0 pt-2');

    let _footer = $('<div />')
        // .addClass('mt-5 d-flex justify-content-between')
        .attr('style', 'column-gap: 16px')


    if (options.closeButton) {
        _footer.append(
            $('<button />')
                .addClass('btn btn-secondary btn-wide w-50')
                .attr({
                    'type': 'button',
                    'data-dismiss': 'modal',
                    'aria-label': trans('message.close'),
                })
                .text(trans('message.close'))
        );
    }

    let panel = $('<div />')
        .addClass('modal fade')
        .attr({
            'tabindex': '-1',
            'aria-hidden': true,
        })
        .append(
            $('<div />')
                .addClass(`${options.custom_class ? options.custom_class : 'modal-dialog nt-modal-dialog modal-dialog-centered'}`)
                .append(
                    $('<div />').addClass('modal-content p-4')
                        .append(
                            _header
                        )
                        .append(
                            _body
                        )
                )
        )

    // set alert
    if (options.showAlert) {
        $('<div />')
            .addClass('form-group form-group-last')
            .append(
                $('<div />')
                    .addClass('alert alert-secondary py-0')
                    .attr('role', 'alert')
                    .append(
                        $('<div />')
                            .addClass('alert-icon')
                            .html('<i class="flaticon-warning kt-font-danger"></i>')
                    )
                    .append(
                        $('<div />')
                            .addClass('alert-text')
                            .html(trans('message.field_required'))
                    )
                    .append(
                        $('<div />')
                            .addClass('alert-close')
                            .append(
                                $('<button />')
                                    .addClass('close')
                                    .attr({
                                        'data-dismiss': 'alert',
                                        'aria-label': trans('message.close'),
                                    })
                                    .html('<span aria-hidden="true"><i class="la la-close"></i></span>')
                            )
                    )
            ).appendTo(_body);
    }

    // set form content r
    let form = $('<form />')
        .addClass('kt-form nt-form mt-3')
        .attr({
            'action': '',
            'method': 'POST',
            'enctype': 'multipart/form-data',
            'id': options.formId ?? ''
        })
        .appendTo(_body);

    let items = options.items ?? [];
    let record = options.record ?? {};
    let hasSelect2 = false;
    let hasSwitch = false;
    let hasCurrency = false;
    items.forEach((item) => {
        if (typeof item.xtype === 'undefined' || item.xtype === 'hiddenfield') {
            $('<input />')
                .addClass('')
                .attr({
                    'type': 'hidden',
                    'value': record[item.name] ?? '',
                    'name': item.name,
                })
                .appendTo(form);
            return;
        }

        if (item.xtype === 'html') {
            form.append(item.label)
            return;
        }

        let label;
        if (item.xtype === 'label') {
            form.append($('<h4 />')
                .addClass('col-form-label kt-portlet__head-title kt-font-lg')
                .text(item.label))
            return;
        }

        let element;
        switch (item.xtype) {
            case 'text':
            case 'number':
            case 'color':
            case 'password':
                if (item.label) {
                    label = $(`<label class="text-uppercase fs-10 ${item.required ? 'required' : ''}">${item.label}</label>`)
                }
                element = $('<input />')
                    .addClass('form-control animated')
                    .addClass(item.class)
                    .attr({
                        'autocomplete': 'off',
                        'type': item.xtype,
                        'name': item.name,
                        'readOnly': item.readOnly ?? false,
                        'disabled': item.disabled ?? false,
                        'animated': true
                    })
                    .css('padding-top', '0.65rem')
                    .val(record[item.name] ?? (item.value ?? ''))
                if (typeof item.id != 'undefined') {
                    element.attr('id', item.id);
                }
                if (typeof item.curency != 'undefined' && item.curency) {
                    element.attr('data-curency', true);
                    hasCurrency = true;
                }
                break;

            case 'switch':
                hasSwitch = true;
                element = $('<input />')
                    .attr({
                        'type': 'checkbox',
                        'data-on-color': 'brand',
                        'switch': true,
                        'name': item.name,
                        'readOnly': item.readOnly ?? false,
                        'disabled': item.disabled ?? false,
                    })
                if (typeof item.id != 'undefined') {
                    element.attr('id', item.id);
                }
                if (record[item.name] == 1) {
                    element.attr('checked', true)
                }

                break;

            case 'button':
                element = item.text;
                break;
            case 'hidden':
                element = $('<input />')
                    .attr({
                        'type': 'hidden',
                        'name': item.name,
                    })
                break;
            case 'textarea':
                if (item.label) {
                    label = $(`<label class="text-uppercase fs-10 ${item.required ? 'required' : ''}">${item.label}</label>`)
                }
                element = $('<textarea />')
                    .addClass('form-control animated')
                    .attr({
                        'rows': item.rows ?? '5',
                        'name': item.name,
                        'id': item.name,
                        'readOnly': item.readOnly ?? false,
                        'disabled': item.disabled ?? false,
                        'animated': true
                    })
                    .text(record[item.name] ?? '');
                    if (item.value) {
                        element.text(item.value);
                    }
                break;

            case 'dropdown':
                if (item.label) {
                    label = $(`<label class="text-uppercase fs-10 ${item.required ? 'required' : ''}">${item.label}</label>`)
                }
                hasSelect2 = true
                element = $('<select />')
                    .addClass('form-control animated')
                    .attr({
                        'name': item.name,
                        'readOnly': item.readOnly ?? false,
                        'disabled': item.disabled ?? false,
                        'multiple': item.multiple ?? false,
                        'aria-hidden': false,
                        'select2': true,
                        'select2-placeholder' : item.placeHolder ?? '',
                        'allow-clear': true,
                        'animated' : true
                    })

                if (typeof item.id != 'undefined') {
                    element.attr('id', item.id);
                }

                if (typeof item.url != 'undefined') {
                    element.attr('select2-url', item.url);
                    element.attr('select2', true);

                    if (typeof item.param != 'undefined') {
                        element.attr('select2-param-selectors', item.param)
                    }
                }

                if (typeof item.options != 'undefined') {
                    if (typeof item.emptyOption != 'undefined' && item.emptyOption) {
                        $('<option />')
                            .attr('value', '')
                            .text('')
                            .appendTo(element)
                    }
                    $.each(item.options, function (value, text) {

                        $('<option />')
                            .attr('value', value)
                            .text(text)
                            .appendTo(element)
                    });

                    if (item.value) {
                        element.val(item.value);
                    } else if (record[item.name]) {
                        element.val(record[item.name]).trigger('change');
                    }
                }

                break;

            case 'file':
                let labelFile = trans('message.choose_file');
                if (typeof record[item.name] != 'undefined') {
                    labelFile = record[item.name];
                    let ar = labelFile.split('/');
                    labelFile = ar[ar.length - 1];
                }

                element = $('<div />')
                    .addClass('custom-file')
                    .append(
                        $('<input />')
                            .addClass('custom-file-input')
                            .addClass(item.class ?? '')
                            .attr({
                                'name': item.name ?? '',
                                'readOnly': item.readonly ?? false,
                                'disabled': item.disabled ?? false,
                                'type': 'file',
                                'accept': item.accept ?? '',
                            })
                            .on('change', function (e) {
                                e.preventDefault();
                                if (typeof this.files[0] === 'undefined' || typeof this.files[0].name === 'undefined') {
                                    element.find('label').html(trans('message.choose_file'));
                                    return;
                                }
                                let labelName = this.files[0].name;
                                element.find('label').html(labelName);
                                if (typeof item.show != 'undefined' && item.show) {
                                    element.append(
                                        $('<div />')
                                            .attr('style', 'width: 170px; height: 170px; overflow: hidden')
                                            .addClass('border rounded mt-3')
                                    )
                                }

                            })
                    )
                    .append(
                        $('<label />')
                            .addClass('custom-file-label text-left overflow-hidden')
                            .attr('for', 'customFile')
                            .html(labelFile)
                    )

                break;

            case 'date':
                element = $('<div />')
                    .addClass('input-group')
                    .attr('data-z-index', '1100')
                    .append(
                        $('<input />')
                            .addClass('form-control')
                            .addClass(item.class ?? '')
                            .attr({
                                'name': item.name ?? '',
                                'readOnly': item.readonly ?? false,
                                'disabled': item.disabled ?? false,
                                'type': 'text',
                                'autocomplete': 'off'
                            })
                            .val(item.value ?? '')
                    )
                    .append(
                        $('<div />')
                            .addClass('input-group-append')
                            .append(
                                $('<span />')
                                    .addClass('input-group-text')
                                    .html('<i class="la la-calendar-plus-o"></i>')
                            )
                    )
                if (typeof item.id != 'undefined') {
                    element.find('input').attr('id', item.id)
                }
                if (typeof item.dateFormat == 'undefined' || item.dateFormat == 'date') {
                    element.find('input').datepicker({
                        todayHighlight: true,
                        autoclose: true,
                        pickerPosition: 'bottom-left',
                        todayBtn: "linked",
                        format: 'yyyy-mm-dd'
                    });
                    if (typeof record['date'] != "undefined") {
                        element.find('input').datepicker({dateFormat: 'yy/mm/dd',}).datepicker("setDate", record['date']);
                    }
                } else {
                    if (item.dateFormat == 'time') {
                        element.find('input').timepicker({
                            defaultTime: 'current',
                            minuteStep: 1,
                            icons:
                                {
                                    up: 'la la-angle-up',
                                    down: 'la la-angle-down'
                                },
                            showSeconds: true,
                            showMeridian: false,
                        });
                    } else {
                        element.find('input').datetimepicker({
                            todayHighlight: true,
                            autoclose: true,
                            pickerPosition: 'bottom-left',
                            todayBtn: "linked",
                            format: 'yyyy-mm-dd hh:ii:ss'
                        });
                    }
                }
                break;

            default:
                break;
        }

        $('<div />')
            .addClass(`${item.parent_class || 'form-group'}`)
            .append(element)
            .append(label)
            .append(
                $('<span />').addClass('form-text text-danger')
            )
            .appendTo(form);
    });

    let spinner = $('<span />')
        .append(
            $('<span />')
                .addClass('kt-spinner kt-spinner--sm kt-spinner--light mr-4')
        )
        .append(
            $('<span />')
                .addClass('ml-2')
                .text(options.btnSaveText ?? trans('message.save'))
        )
    let icon_check = $('<span />')
        .append(
            $('<span />')
                .text(options.btnSaveText ?? trans('message.save'))
        )

    if (options.saveBtn) {
        _footer.addClass(`mt-3 d-flex ${options.closeButton ? 'justify-content-between' : 'justify-content-end'}`)
            .append(
                $('<button />')
                    .addClass('btn btn-brand btn-wide w-50')
                    .attr({
                        'type': 'submit',
                    })
                    .append(icon_check)
                    .on('click', function (e) {
                        e.preventDefault();
                        Common.prototype.clearInvalidMessage()
                        if (typeof options.onSubmit != 'undefined') {
                            options.onSubmit();
                            return;
                        }
                        let submit = $(this);
                        submit.attr('disabled', true).html(spinner);
                        var formData = new FormData(form[0]);
                        if (typeof options.method != 'undefined') {
                            formData.set('_method', options.method)
                        }
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: options.url ?? '',
                            data: formData,
                            type: 'post',
                            enctype: 'multipart/form-data',

                            success: function (result) {
                                submit.attr('disabled', false).html(icon_check);
                                if (!result) {
                                    swal.fire({
                                        position: 'top-end',
                                        type: 'warning',
                                        title: trans('message.notify.error.errors'),
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    return;
                                }
                                if (result.error) {
                                    toastr.error(result.error);
                                } else {
                                    if (options.onSuccess) options.onSuccess(result);
                                    panel.modal('hide');
                                    if (options.showCompleteAjax) {
                                        swal.fire({
                                            position: 'top-right',
                                            type: 'success',
                                            title: trans('message.notify.success.success'),
                                            showConfirmButton: false,
                                            timer: 1500
                                        });
                                    }
                                }
                                if (successCallback) successCallback(result)
                            },

                            error: function (errors) {
                                submit.attr('disabled', false).html(icon_check);

                                if (typeof errors == "undefined") return;
                                const message = errors.responseJSON;

                                if (message.error) {
                                    toastr.error(message.error);
                                    return;
                                }

                                if (typeof message.errors == 'undefined') {
                                    swal.fire({
                                        position: 'top-end',
                                        type: 'warning',
                                        title: trans('message.notify.error.errors'),
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    return
                                }
                                if (options.highlightInput) {
                                    let objError = message.errors
                                    Object.keys(objError).forEach(function (key) {
                                        $(`[name=${key}]`)
                                            .addClass("is-invalid")
                                            .parent()
                                            .append(
                                                `<span class="invalid-feedback" style="flex-basis: 100%;"><strong>${objError[key][0]}</strong></span>`
                                            )
                                    })
                                }
                                toastr.remove()
                            },

                            contentType: false,
                            processData: false
                        });
                    })
            )
    } else {
        _footer.addClass('d-block text-center');
    }

    _footer.appendTo(_body);
    panel.appendTo('body').modal();
    panel.on('shown.bs.modal', function () {
        if (options.onReady) {
            options.onReady();
        }
        panel.removeAttr('tabindex', "");
    });

    panel.on("hidden.bs.modal", function () {
        panel.remove()
    });

    if (hasSelect2) this.setSelect2();
    if (hasSwitch) this.setSwitch();
    if (hasCurrency) this.setCurrency();
    this.setAnimatedInput();
}

Common.prototype.getItem = function (url, data = null) {
    return $.ajax({method: 'GET', url, data});
}

Common.prototype.simpleAjax = function (url, method, data, callback, callbackError, callBackComplete = null) {
    toastr.clear();
    $.ajax({
        method,
        url,
        data,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        contentType: false,
        cache: false,
        processData: false,
        async: true,
        success: function (dataSuccess) {
            if (callback) callback(dataSuccess);
            else Common.prototype.showNotify(trans('message.notify.success.success'), 'success');

        },
        error: function (errors) {
            if (callbackError) callbackError(errors);
            else {
                if (typeof errors == "undefined" || errors == '' || errors == null) {
                    Common.prototype.showNotify();
                    return;
                }
                errors = $.parseJSON(errors.responseText);
                let isErrors = false
                if (typeof errors.message !== "undefined") {
                    isErrors = true
                    toastr.clear()
                }
                $.each(errors.errors, function (_index, error) {
                    if (isErrors) {
                        toastr.error(error[0])
                    } else {
                        Common.prototype.showNotify(error[0], "error")
                    }
                })
            }
        },
        complete: function(dataComplete) {
            if (callBackComplete) callBackComplete(dataComplete);
        }
    })
}

Common.prototype.showNotify = function (title = trans('message.notify.error.errors'), type = 'error', content = null,position = 'top-end', timer = 2500, callback = null) {
    let icon = 'default.svg'
    switch (type) {
        case 'success':
            icon = 'success.svg'
                break;
        case 'error':
            icon = 'error.svg'
            break;
    }
    Swal.fire({
        html: `<div class="d-flex swal2-custom">
                    <div class="d-flex justify-content-center align-items-center swal2-custom-icon">
                        <img src="/media/sweet-aleart-2/${icon}" alt="${icon}">
                    </div>
                    <div class="p-3 d-flex justify-content-center flex-column">
                        <p class="swal2-custom-title">${title}</p>
                        ${content ? `<p class="swal2-custom-content">${content} </p>` :''}
                    </div>
                </div>`,
        showCloseButton: true,
        showConfirmButton: false,
        position,
        customClass: {
            popup: `p-0 rounded-0 colored-toast ${type}`,
            closeButton: 'h-100 align-items-center'
        },
        timer: timer
    }).then(function() {
        if (callback) {
            callback()
        }
    })
}

Common.prototype.Notify = function () {
    let flashMessage = localStorage["flash_message"];
    if (flashMessage) {
        this.showNotify(flashMessage, 'success')
        localStorage.removeItem("flash_message");
    }

    if (typeof _successMessage !== "undefined" && _successMessage !== "") {
        this.showNotify(_successMessage, 'success', _successContentMessage)
    }

    if (typeof _errorMessage !== "undefined" && _errorMessage !== "") {
        this.showNotify(_errorMessage, 'error', _errorContentMessage)
    }

    if (typeof _warningMessage !== "undefined" && _warningMessage !== "") {
        this.showNotify(_warningMessage, 'warning', _warningContentMessage)
    }
};

Common.prototype.showErrors = function (errors = null) {
    if (errors) {
        errors = $.parseJSON(errors.responseText);
        $.each(errors.errors, function (_index, error) {
            Common.prototype.showNotify(error[0]);
        })
    }
}

Common.prototype.flashNotifyAfterReload = function(message) {
    localStorage["flash_message"] = message
}

Common.prototype.readFileAndPreview = function (input, element = false) {
    $(input).on('change', function () {
        if (typeof this.files == 'undefined' || typeof this.files[0] == 'undefined') return;
        var reader = new FileReader();
        reader.onload = function (e) {
            if (element) {
                element.attr('src', e.target.result)
            }
        }
        reader.readAsDataURL(this.files[0]);
        let name = '';
        if (typeof this.files[0] === 'undefined' || typeof this.files[0].name === 'undefined') return;
        name = this.files[0].name
        let label = $(this).parent().find('label');
        if (label[0]) label.html(name)
    });
}

Common.prototype.select2Ajax = function (selector, url) {
    $(selector).select2({
        placeholder: trans('message.text.search_for'),
        width: '100%',
        allowClear: true,
        ajax: {
            delay: 100,
            url: url,
            data: function (params) {
                return {
                    term: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (data, params) {
                var results = [];
                let isMore = false
                params.page = params.page || 1;
                if (data !== undefined && (data.length > 0 || (data.data && data.data.length > 0))) {
                    if (Array.isArray(data.data)) {
                        results = {
                            items: data.data
                        }
                        isMore = (params.page * 10) < data.total
                    }
                    if (Array.isArray(data)) {
                        results = {
                            items: data
                        }
                    }
                    var convertedResult = [];
                    if (results.items !== undefined) {
                        results.items.forEach(function (item) {
                            if (item.id !== undefined && item.label !== undefined) {
                                convertedResult.push({
                                    id: item.id,
                                    text: item.label
                                })
                            }
                            if (item.label !== undefined && item.value !== undefined) {
                                convertedResult.push({
                                    id: item.value,
                                    text: item.label
                                })
                            }
                        })
                        results = convertedResult;
                    } else {
                        results = [];
                    }
                }
                return {
                    results: results,
                    pagination: {
                        more: isMore
                    }
                };
            }
        }
    })
}

Common.prototype.setSelect2 = function () {
    $('[select2]').each(function (_index) {
        var url = $(this).attr('select2-url');
        var search = $(this).attr('select2-search');
        var allowClear = $(this).attr('allow-clear');
        var placeHolder = $(this).attr('select2-placeholder')

        if (typeof url === 'undefined') {
            let _params = {width: '100%'}
            if (search == 'false') {
                Object.assign(_params, {minimumResultsForSearch: Infinity})
            }

            if (placeHolder !== 'undefined') {
                Object.assign(_params, {placeholder:  placeHolder})
            }

            if (allowClear == 'true') {
                Object.assign(_params, {
                    allowClear: true, placeholder: {
                        id: '',
                        text: placeHolder || ''
                    },
                })
            }
            $(this).select2(_params);
            return;
        }
        var params = $(this).attr('select2-param-selectors');
        var element = $(this);
        if (params !== undefined) {
            params = params.split(',');
            var values = params;
            var me = this;
            for (var i = 0; i < params.length; i++) {
                $(params[i]).on('change', function () {
                    $(element).val('');
                    $(element).find('option').each(function () {
                        $(this).remove();
                    });
                    params = $(element).attr('select2-param-selectors');
                    url = $(element).attr('select2-url');
                    params = params.split(',');
                    for (var j = 0; j < params.length; j++) {
                        values[j] = $(params[j]).val();
                    }
                    url = Common.prototype.formatWithParams(url, values);
                    Common.prototype.select2Ajax(me, url);
                });
                values[i] = $(params[i]).val();
            }
            url = Common.prototype.formatWithParams(url, values);
        }
        Common.prototype.select2Ajax(this, url);
    })
}

Common.prototype.setSwitch = function () {
    $('input[switch=true]').each(function (_index, value) {
        let state = ($(value).prop('checked') ? true : false) ?? false;
        $(value).bootstrapSwitch();
        setTimeout(function () {
            $(value).bootstrapSwitch('state', state);
        }, 500)
    })
}

Common.prototype.formatWithParams = String.prototype.f = function (url, params) {
    var s = url,
        i = params.length;

    while (i--) {
        s = s.replace(new RegExp('\\{' + i + '\\}', 'gm'), params[i]);
    }
    return s;
};

Common.prototype.destroy = function (url, onSuccessCallBack, onYesCallBack, onNoCallBack) {
    const modalDestroy = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-brand btn-wide',
            cancelButton: 'btn btn-secondary btn-wide'
        },
        buttonsStyling: false
    })
    modalDestroy.fire({
        title: trans('message.notify.are_sure'),
        text: trans('message.notify.txt_revert'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: trans('message.notify.agree_delete'),
        cancelButtonText: trans('message.notify.disagree_delete'),
        reverseButtons: true
    }).then(function (result) {
        if (result.value) {
            if (onYesCallBack) onYesCallBack();
            const destroySwal = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-secondary',
                },
                buttonsStyling: false
            })
            $.ajax({
                method: 'DELETE',
                url,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function (res) {
                    if (onSuccessCallBack) onSuccessCallBack(res);
                    if (res === true || res.message || res.result === true) {
                        destroySwal.fire({
                            position: 'top-end',
                            type: 'success',
                            title: trans('message.notify.success.success'),
                            showConfirmButton: false,
                            timer: 1500
                        });
                        return;
                    }

                    let errMsg = trans('message.notify.error.errors');
                    if (typeof res.error != 'undefined') {
                        errMsg = res.error;
                    }

                    destroySwal.fire({
                        position: 'top-end',
                        type: 'warning',
                        title: errMsg,
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function (errors) {
                    if (typeof errors == "undefined") return;

                    errors = $.parseJSON(errors.responseText);
                    if (typeof errors.error_permission != 'undefined' && errors.error_permission) {
                        destroySwal.fire({
                            position: 'center-center',
                            type: 'warning',
                            title: trans('message.notify.error.errors'),
                            html: '<p class="text-danger">' + trans('message.permission.not_have') + '</p>',
                        });
                        return
                    }

                    if (typeof errors.message != 'undefined' && errors.message) {
                        destroySwal.fire({
                            position: 'top-end',
                            type: 'warning',
                            title: errors.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        return
                    }

                    destroySwal.fire({
                        position: 'top-end',
                        type: 'warning',
                        title: trans('message.notify.error.errors'),
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            })
        } else if (result.dismiss === 'cancel') {
            if (onNoCallBack) onNoCallBack();
        }
    });
}

Common.prototype.setCurrency = function (symbol, symbolEnd = 1, rightAlign = false) {
    if (!symbol) return;
    $('[data-currency]').each(function () {
        $(this).inputmask("remove");

        let options = {
            autoUnmask: true,
            autoGroup: true,
            groupSeparator: ",",
            groupSize: 3,
            digits: 4,
            removeMaskOnSubmit: true,
            allowMinus: false,
            allowPlus: false,
            rightAlign: rightAlign,
            onBeforeMask: function (value, _opts) {
                return value.toString();
            }
        }

        if (parseInt(symbolEnd)) {
            options.suffix = " " + symbol
        } else {
            options.prefix = symbol + " "
        }
        $(this).inputmask("decimal", options);
    })
}

Common.prototype.setDecimalFormat = function ($element = null, rightAlign = false) {
    let inputMasks = $('[decimal]');
    if ($element) {
        inputMasks = $element.find('[decimal]')
    }
    inputMasks.each(function () {
        $(this).inputmask("remove");
        let options = {
            autoUnmask: true,
            autoGroup: true,
            alias: 'decimal',
            rightAlign: rightAlign,
            groupSeparator: '.',
            min: 0
        }
        $(this).inputmask(options);
    })
}

Common.prototype.setDatePicker = function () {
    if ($.fn.datepicker) {
        $('[datepicker]').datepicker({
            todayHighlight: true,
            autoclose: true,
            pickerPosition: 'bottom-left',
            todayBtn: 'linked',
            format: 'yyyy-mm-dd',
        }).each(function () {
            let value = $(this).val()
            if ($(this).attr('datepicker-default-date') !== undefined && value.length == 0) {
                $(this).datepicker('setDate', 'now')
            }
            if ($(this).attr('datepicker-default-date-30') !== undefined && value.length == 0) {
                var futureMonth = moment().add(1, 'M');
                var date = new Date(futureMonth)
                $(this).datepicker('setDate', date)
            }
        });
    }
}

Common.prototype.setLoadingFormSubmit = function () {
    $('.submit-loading').each(function (_index) {
        $(this).on('click', function () {
            $(this).addClass('disabled kt-spinner kt-spinner--md kt-spinner--light kt-padding-l-55').find('i').remove();
            setTimeout(() => {
                $(this).attr('disabled', true);
            });
        });
    })
}
Common.prototype.setLoadingFormSubmit();

Common.prototype.setAnimatedInput = function () {
    let $animated = $('[animated]')
    $animated.each(function() {
        if (this.value && !$(this).hasClass('has-value')) {
            $(this).addClass('has-value')
        }
    })
    $animated.on('focusin select2:open', function() {
        $(this).addClass('has-value');
    });

    $animated.on('focusout select2:close', function() {
        if (!this.value && !$(this).attr('select2-placeholder') && !$(this).attr('placeholder')) {
            $(this).removeClass('has-value');
        }
    });
}
Common.prototype.setAnimatedInput()

Common.prototype.updateLock = function () {
    $('form').on('click', '.update-form', function (e) {
        let button = $(this);
        e.preventDefault();
        let type = $(this).data('type');
        let id = $(this).data('id');
        let exit = $(this).data('exit');
        let noReload = $(this).attr('data-no-reload');
        let data = {type, id};
        if (typeof exit !== 'undefined') {
            data = {type, id, exit};
        }
        $.ajax({
            method: 'POST',
            url: '/ajaxs/update-lock',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data,
            success: function (res) {
                if (res) {
                    if (typeof exit !== 'undefined') {
                        Common.prototype.showNotify(trans('message.notify.lock_edit'), 'success')
                    } else {
                        Common.prototype.showNotify(trans('message.notify.unlock_edit'), 'success')
                    }
                    if (noReload) {
                        let html;
                        if (typeof exit !== 'undefined') {
                            html = `<a href="javascript:;" data-type="${type}" data-id="${id}" class="btn btn-brand btn-wide submit-loading update-form" data-no-reload="true"><i class="la la-edit"></i>${trans('message.edit')}</a>`
                            button.closest('form').find('input:not([type="hidden"]), textarea, select')
                                .prop('disabled', true)
                                .removeClass('is-invalid')
                                .siblings('div.invalid-feedback')
                                .remove();
                            $('.nav-link.disabled').removeClass('disabled').parent().removeClass('cursor-not-allowed')
                        } else {
                            button.closest('form').find('input, textarea, select').prop('disabled', false);
                            html = `
                            <a href="javascript:;" id="btnCancel" class="btn btn-secondary btn-wide submit-loading update-form mr-2" data-exit="true" data-type="${type}" data-id="${id}" data-no-reload="true"><i class="la la-close"></i>${trans('message.cancel')}</a>
                            <button type="submit" id="btnSubmit" class="btn btn-brand btn-wide submit-loading" data-no-reload="true"><i class="la la-check"></i>${trans('message.submit')}</button>`
                            let activePanel = button.closest('.tab-pane').attr('id')
                            $(`a.nav-link:not([href="#${activePanel}"])`).addClass('disabled').parent().addClass('cursor-not-allowed')
                        }
                        button.parent().empty().append(html)
                        Common.prototype.setLoadingFormSubmit()
                    } else {
                        setTimeout(function () {
                            location.reload();
                        }, 1000)
                    }
                    return;
                }
                Common.prototype.showNotify(trans('message.notify.another_user_editing'))
            },
            error: function (_errors) {
                toastr.error(trans('message.notify.error.update'));
            },
            complete: function () {
                button.removeClass('disabled kt-spinner kt-spinner--md kt-spinner--light kt-padding-l-55')
                button.prepend(
                    $('<i />').addClass('la la-edit')
                )
            }
        })
    })
}
Common.prototype.updateLock();

Common.prototype.autoResizeTextarea = function (sectionId) {
    $(`#${sectionId}`).find('textarea').each(function () {
        $(this).height($(this)[0].scrollHeight - 18);
    });
}

Common.prototype.getValueFromQueryStringKey = function (url, key) {
    let urlParams = new RegExp('[\?&]' + key + '=([^&#]*)').exec(url);
    return (urlParams !== null) ? urlParams[1] || 0 : 1;
}

Common.prototype.confirmActionAlert = function (okCallback = null, cancelCallback = null, title = null, text = null, confirmText = null, cancelText = null) {
    const modalDestroy = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-brand btn-wide button-min-width',
            cancelButton: 'btn btn-secondary btn-wide button-min-width'
        },
        buttonsStyling: false
    })

    modalDestroy.fire({
        title: title == null ? trans('message.notify.are_sure') : title,
        text: text == null ? trans('message.notify.txt_revert') : text,
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: confirmText == null ? trans('message.notify.agree').toUpperCase() : confirmText,
        cancelButtonText: cancelText == null ? trans('message.notify.disagree').toUpperCase() : cancelText,
        reverseButtons: true
    }).then(function (result) {
        if (result.value) {
            if (okCallback) {
                okCallback()
            }
        } else {
            if (cancelCallback) {
                cancelCallback()
            }
        }
    });
}

Common.prototype.scrollToError = function (errorElement = '.invalid-feedback') {
    let $errors = $(errorElement)
    if ($errors.length > 0) {
        let $firstError = $errors.filter(function () {
            return $(this).css('display') !== 'none';
        });
        $([document.documentElement, document.body]).animate({
                scrollTop: getOffsetTop($firstError.first()[0]) - 200,
            },
            500
        )
    }

    function getOffsetTop(el) {
        let y = 0,
            n = true;

        do {
            if (n) {
                y += el.offsetTop || 0
                n = false
            } else if (getComputedStyle(el).position === "relative") {
                n = true
            }
            el = el.parentElement;
        } while (el != null && (el.tagName || '').toLowerCase() !== 'html');

        return parseInt(y, 10);
    }
}

Common.prototype.showErrorWhenValidateFail = function (message) {
    let $submitButton = $("#btnSubmit")
    $submitButton
        .removeClass(
            "disabled kt-spinner kt-spinner--md kt-spinner--light kt-padding-l-55"
        )

    setTimeout(function () {
        $submitButton.removeAttr("disabled")
        Swal.fire({
            position: "top-end",
            type: "warning",
            title: message,
            showConfirmButton: false,
            timer: 1500,
            onClose: () => {
                Common.prototype.scrollToError()
            }
        })
    }, 500)
}

Common.prototype.removeIconLoading = function ($element, $prependIcon = false, $icon = 'la la-edit') {
    setTimeout(function () {
        $element.removeClass('disabled kt-spinner kt-spinner--md kt-spinner--light kt-padding-l-55').prop("disabled", false);
        if ($prependIcon) {
            $element.prepend(
                $('<i />').addClass($icon)
            )
        }
    })
}

Common.prototype.clearInvalidMessage = function ($element = null) {
    let $elementInValid = $(".is-invalid");
    if ($element) {
        $elementInValid = $element
    }
    $elementInValid.each(function () {
        $(this).removeClass('is-invalid')
            .siblings('span.invalid-feedback').remove()
    })
}

Common.prototype.nullToEmpty = function (str) {
    if (str === null || str === undefined) {
        return ''
    }
    return str
}

Common.prototype.isEmpty = function (str) {
    return (!str || str.length === 0);
}

Common.prototype.textTruncate = function (text, length, end = '...') {
    if (text == null) {
        return "";
    }
    if (text.length <= length) {
        return text;
    }
    text = text.substring(0, length);
    return text + end;
}

Common.prototype.shortenText = function(str, maxLen, separator = ' ') {
    if (str.length <= maxLen) return str;
    return str.substr(0, str.lastIndexOf(separator, maxLen));
}

Common.prototype.showMoreButton = function() {
   $(".show-more-text").each(function() {
       if (!$(this).length) {
           return;
       }
       let $that = $(this)
       let button = $(this).siblings(".btn-show-more");

       let content = $(this).html();
       let width =  $(this).width();
       let showChars = width / 3.6;

       if (content.length > showChars) {
           let firstContent = Common.prototype.shortenText(content, showChars);
           let lastContent = content.substr(firstContent.length, content.length - firstContent.length);
           let html = `${firstContent}<span class="more-ellipses">...</span><span class="more-content"><span>${lastContent}</span></span>`;
           $(this).html(html);
           $(this).find(".more-content span").hide();
           button.show()
       }

       button.on("click", function() {
           let $showMoreText = $(this).prev()
           if ($that.hasClass("active")) {
               $showMoreText.find(".more-ellipses").toggle()
               $showMoreText.find(".more-content span").slideToggle(300);
               $that.removeClass("active");
               button.text(trans('message.see_more'))
           } else {
               $showMoreText.find(".more-ellipses").toggle()
               $showMoreText.find(".more-content span").slideToggle(300);
               $that.addClass("active");
               button.text(trans('message.close'))
           }
       });
   });
}

Common.prototype.addBorderToNavMobile = function($navbarWrapper) {
    addBorderToNavMobile($navbarWrapper)
    $(window).resize(function() {
        addBorderToNavMobile($navbarWrapper)
    })

    function addBorderToNavMobile(_$navbarWrapper) {
        if (_$navbarWrapper.hasScrollBar('horizontal')) {
            _$navbarWrapper.addClass('scroll-border').removeClass('no-scroll-border')
        } else {
            _$navbarWrapper.addClass('no-scroll-border').removeClass('scroll-border')
        }
    }
}

Common.prototype.headerSignin = function() {
    $('.custom-nav .nav-mobile__action .openable').on('click', function () {
        $('body').css('overflow', 'hidden')
        $(this).addClass('d-none');
        $('.custom-nav .nav-mobile').addClass('show-custom')
        $('.custom-nav .nav-mobile__action .closeable').removeClass('d-none')
    })
    $('.custom-nav .nav-mobile__action .closeable').on('click', function () {
        $('body').css('overflow', 'auto')
        $(this).addClass('d-none');
        $('.custom-nav .nav-mobile__action .openable').removeClass('d-none')
        $('.custom-nav .nav-mobile').removeClass('show-custom')
    })
}

Common.prototype.headerSignin()

function trans(key, replace = {}) {
    let translation = key.split('.').reduce((t, i) => t[i] || null, window.translations);

    for (var placeholder in replace) {
        translation = translation.replace(`:${placeholder}`, replace[placeholder]);
    }

    return translation;
}

jQuery.fn.hasScrollBar = function(direction)
{
    if (direction == 'vertical')
    {
        return this.get(0).scrollHeight > this.get(0).clientHeight;
    }
    else if (direction == 'horizontal')
    {
        return this.get(0).scrollWidth > this.get(0).clientWidth
    }
    return false;

}
