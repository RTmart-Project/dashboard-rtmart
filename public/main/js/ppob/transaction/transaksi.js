$(document).ready(function () {
    // DataTables
    dataTablesTransaksi();

    function dataTablesTransaksi() {
        $('#transaksi .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'<'filter-transaksi'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            "ajax": {
                url: "/ppob/transaction/get",
                data: function (d) {
                    d.fromDate = $('#transaksi #from_date').val();
                    d.toDate = $('#transaksi #to_date').val();
                }
            },
            columns: [
                {
                    data: 'TransactionDate',
                    name: 'TransactionDate'
                },
                {
                    data: 'PPOBOrderID',
                    name: 'PPOBOrderID'
                },
                {
                    data: 'MerchantID',
                    name: 'MerchantID'
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
                    data: 'TypeOrder',
                    name: 'TypeOrder'
                },
                {
                    data: 'NominalOrder',
                    name: 'NominalOrder'
                },
                {
                    data: 'TotalPrice',
                    name: 'TotalPrice'
                },
                {
                    data: 'CompanyMargin',
                    name: 'CompanyMargin'
                },
                {
                    data: 'StatusName',
                    name: 'StatusName'
                }
            ],
            "order": [[ 0, "desc" ]],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('TransaksiPPOBMerchant');
                },
                text: 'Export',
                titleAttr: 'Excel',
                exportOptions: {
                    modifier: {
                        page: 'all'
                    },
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                    orthogonal: 'export'
                },
            }],
            "lengthChange": false,
            "responsive": true,
            "autoWidth": false,
            "aoColumnDefs": [
                {
                    "aTargets": [6, 7, 8],
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
                },
                {
                    "aTargets": [4],
                    "mRender": function (data, type, full) {
                        return data.toUpperCase();
                    }
                }
            ]
        });
    }

    // Create element for DateRange Filter
    $("div.filter-transaksi").html(`<div class="input-group">
                            <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                                readonly>
                            <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                                readonly>
                            <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                            <button type="button" name="refresh" id="refresh"
                                class="btn btn-sm btn-warning ml-2">Refresh</button>
                        </div>`);

    // Setting Awal Daterangepicker
    $('#transaksi #from_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // Setting Awal Daterangepicker
    $('#transaksi #to_date').daterangepicker({
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

        $('#transaksi #to_date').daterangepicker({
            minDate: $("#transaksi #from_date").val(),
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

        $('#transaksi #from_date').daterangepicker({
            maxDate: $("#transaksi #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        })
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $('#transaksi .filter-transaksi').on('change', '#from_date', function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $('#transaksi .filter-transaksi').on('change', '#to_date', function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $('#transaksi #from_date').val('');
    $('#transaksi #to_date').val('');
    $('#transaksi #from_date').attr("placeholder", "From Date");
    $('#transaksi #to_date').attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#transaksi #refresh").click(function () {
        $('#transaksi #from_date').val('');
        $('#transaksi #to_date').val('');
        $('#transaksi .table-datatables').DataTable().search('');
        $('#transaksi .table-datatables').DataTable().ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#transaksi #filter").click(function () {
        $('#transaksi .table-datatables').DataTable().ajax.reload();
    });
});