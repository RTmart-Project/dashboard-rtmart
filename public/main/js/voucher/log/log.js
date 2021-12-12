$(document).ready(function () {
    // DataTables
    dataTablesVoucherLog();

    function dataTablesVoucherLog() {
        $('#voucher-log .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'<'filter-voucher-log'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            "ajax": {
                url: "/voucher/log/get",
                data: function (d) {
                    d.fromDate = $('#voucher-log #from_date').val();
                    d.toDate = $('#voucher-log #to_date').val();
                }
            },
            columns: [
                {
                    data: 'OrderID',
                    name: 'ms_voucher_log.OrderID'
                },
                {
                    data: 'VoucherCode',
                    name: 'ms_voucher_log.VoucherCode'
                },
                {
                    data: 'VoucherTypeName',
                    name: 'ms_voucher_type.VoucherTypeName'
                },
                {
                    data: 'NominalPromo',
                    name: 'ms_voucher_log.NominalPromo'
                },
                {
                    data: 'ProcessTime',
                    name: 'ms_voucher_log.ProcessTime',
                    type: 'date'
                }
            ],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('VoucherLog');
                },
                text: 'Export',
                titleAttr: 'Excel',
                exportOptions: {
                    modifier: {
                        page: 'all'
                    },
                    columns: [0, 1, 2, 3, 4],
                    orthogonal: 'export'
                },
            }],
            "order": [4, 'desc'],
            "lengthChange": false,
            "responsive": true,
            "autoWidth": false,
            "aoColumnDefs": [
                {
                    "aTargets": [3],
                    "mRender": function (data, type, full) {
                        if (type === 'export') {
                            return data;
                        } else {
                            if (data == null || data == "") {
                                return data;
                            } else {
                                const currencySeperatorFormat = thousands_separators(data)
                                return currencySeperatorFormat;
                            }
                        }
                    }
                }
            ]
        });
    }

    // Create element for DateRange Filter
    $("div.filter-voucher-log").html(`<div class="input-group">
                            <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                                readonly>
                            <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                                readonly>
                            <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                            <button type="button" name="refresh" id="refresh"
                                class="btn btn-sm btn-warning ml-2">Refresh</button>
                        </div>`);

    // Setting Awal Daterangepicker
    $('#voucher-log #from_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // Setting Awal Daterangepicker
    $('#voucher-log #to_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    var bCodeChange = false;

    function dateStartChange() {
        if (bCodeChange == true)
            return;
        else
            bCodeChange = true;

        $('#voucher-log #to_date').daterangepicker({
            minDate: $("#voucher-log #from_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        })
        bCodeChange = false;
    }

    function dateEndChange() {
        if (bCodeChange == true)
            return;
        else
            bCodeChange = true;

        $('#voucher-log #from_date').daterangepicker({
            maxDate: $("#voucher-log #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        })
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $('#voucher-log .filter-voucher-log').on('change', '#from_date', function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $('#voucher-log .filter-voucher-log').on('change', '#to_date', function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $('#voucher-log #from_date').val('');
    $('#voucher-log #to_date').val('');
    $('#voucher-log #from_date').attr("placeholder", "From Date");
    $('#voucher-log #to_date').attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#voucher-log #refresh").click(function () {
        $('#voucher-log #from_date').val('');
        $('#voucher-log #to_date').val('');
        $('#voucher-log .table-datatables').DataTable().search('');
        $('#voucher-log .table-datatables').DataTable().ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#voucher-log #filter").click(function () {
        $('#voucher-log .table-datatables').DataTable().ajax.reload();
    });
});