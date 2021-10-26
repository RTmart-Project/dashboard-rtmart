$(document).ready(function () {
    // DataTables
    dataTablesMenungguValidasi();

    function dataTablesMenungguValidasi() {
        $('#semua-data .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'<'filter-semua-data'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            "ajax": {
                url: "/ppob/topup/get",
                data: function (d) {
                    d.fromDate = $('#semua-data #from_date').val();
                    d.toDate = $('#semua-data #to_date').val();
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
                    data: 'PhoneNumber',
                    name: 'PhoneNumber'
                },
                {
                    data: 'MerchantID',
                    name: 'MerchantID'
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
                    data: 'StatusName',
                    name: 'StatusName'
                },
            ],
            "order": [[ 0, "desc" ]],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('TopupMerchantPPOBSemuaData');
                },
                text: 'Export',
                titleAttr: 'Excel',
                exportOptions: {
                    modifier: {
                        page: 'all'
                    },
                    columns: [0, 1, 2, 3, 4, 5, 6, 7],
                    orthogonal: 'export'
                },
            }],
            "lengthChange": false,
            "responsive": true,
            "autoWidth": false,
            "aoColumnDefs": [
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
    $("div.filter-semua-data").html(`<div class="input-group">
                            <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                                readonly>
                            <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                                readonly>
                            <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                            <button type="button" name="refresh" id="refresh"
                                class="btn btn-sm btn-warning ml-2">Refresh</button>
                        </div>`);

    // Setting Awal Daterangepicker
    $('#semua-data #from_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // Setting Awal Daterangepicker
    $('#semua-data #to_date').daterangepicker({
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

        $('#semua-data #to_date').daterangepicker({
            minDate: $("#semua-data #from_date").val(),
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

        $('#semua-data #from_date').daterangepicker({
            maxDate: $("#semua-data #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        })
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $('#semua-data .filter-semua-data').on('change', '#from_date', function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $('#semua-data .filter-semua-data').on('change', '#to_date', function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $('#semua-data #from_date').val('');
    $('#semua-data #to_date').val('');
    $('#semua-data #from_date').attr("placeholder", "From Date");
    $('#semua-data #to_date').attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#semua-data #refresh").click(function () {
        $('#semua-data #from_date').val('');
        $('#semua-data #to_date').val('');
        $('#semua-data .table-datatables').DataTable().search('');
        $('#semua-data .table-datatables').DataTable().ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#semua-data #filter").click(function () {
        $('#semua-data .table-datatables').DataTable().ajax.reload();
    });
});