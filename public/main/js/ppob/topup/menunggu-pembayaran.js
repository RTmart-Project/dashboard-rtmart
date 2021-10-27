$(document).ready(function () {
    // DataTables
    dataTablesMenungguPembayaran();

    function dataTablesMenungguPembayaran() {
        $('#menunggu-pembayaran .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'<'filter-menunggu-pembayaran'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            "ajax": {
                url: "/ppob/topup/get/TPS-001",
                data: function (d) {
                    d.fromDate = $('#menunggu-pembayaran #from_date').val();
                    d.toDate = $('#menunggu-pembayaran #to_date').val();
                }
            },
            columns: [
                {
                    data: 'TransactionDate',
                    name: 'TransactionDate'
                },
                {
                    data: 'StoreName',
                    name: 'StoreName'
                },
                {
                    data: 'MerchantID',
                    name: 'MerchantID'
                },
                {
                    data: 'PhoneNumber',
                    name: 'PhoneNumber'
                },
                {
                    data: 'TxTopupSaldoPpobID',
                    name: 'TxTopupSaldoPpobID'
                },
                {
                    data: 'AmountTopup',
                    name: 'AmountTopup'
                },
                {
                    data: 'UniqueCode',
                    name: 'UniqueCode'
                },
                {
                    data: 'Action',
                    name: 'Action'
                }
            ],
            "order": [[ 0, "desc" ]],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('TopupMerchantPPOBMenungguPembayaran');
                },
                text: 'Export',
                titleAttr: 'Excel',
                exportOptions: {
                    modifier: {
                        page: 'all'
                    },
                    columns: [0, 1, 2, 3, 4, 5, 6],
                    orthogonal: 'export'
                },
            }],
            "lengthChange": false,
            "responsive": true,
            "autoWidth": false,
            "aoColumnDefs": [
                {
                    "aTargets": [7],
                    "orderable": false
                },
                {
                    "aTargets": [5],
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
    $("div.filter-menunggu-pembayaran").html(`<div class="input-group">
                            <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                                readonly>
                            <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                                readonly>
                            <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                            <button type="button" name="refresh" id="refresh"
                                class="btn btn-sm btn-warning ml-2">Refresh</button>
                        </div>`);

    // Setting Awal Daterangepicker
    $('#menunggu-pembayaran #from_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // Setting Awal Daterangepicker
    $('#menunggu-pembayaran #to_date').daterangepicker({
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

        $('#menunggu-pembayaran #to_date').daterangepicker({
            minDate: $("#menunggu-pembayaran #from_date").val(),
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

        $('#menunggu-pembayaran #from_date').daterangepicker({
            maxDate: $("#menunggu-pembayaran #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        })
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $('#menunggu-pembayaran .filter-menunggu-pembayaran').on('change', '#from_date', function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $('#menunggu-pembayaran .filter-menunggu-pembayaran').on('change', '#to_date', function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $('#menunggu-pembayaran #from_date').val('');
    $('#menunggu-pembayaran #to_date').val('');
    $('#menunggu-pembayaran #from_date').attr("placeholder", "From Date");
    $('#menunggu-pembayaran #to_date').attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#menunggu-pembayaran #refresh").click(function () {
        $('#menunggu-pembayaran #from_date').val('');
        $('#menunggu-pembayaran #to_date').val('');
        $('#menunggu-pembayaran .table-datatables').DataTable().search('');
        $('#menunggu-pembayaran .table-datatables').DataTable().ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#menunggu-pembayaran #filter").click(function () {
        $('#menunggu-pembayaran .table-datatables').DataTable().ajax.reload();
    });
});