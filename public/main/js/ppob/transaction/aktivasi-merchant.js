$(document).ready(function () {
    // DataTables
    dataTablesAktivasiMerchant();

    function dataTablesAktivasiMerchant() {
        $('#aktivasi-merchant .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'<'filter-aktivasi-merchant'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            "ajax": {
                url: "/ppob/merchant/get",
                data: function (d) {
                    d.fromDate = $('#aktivasi-merchant #from_date').val();
                    d.toDate = $('#aktivasi-merchant #to_date').val();
                }
            },
            columns: [
                {
                    data: 'ActivatedPPOBDate',
                    name: 'ActivatedPPOBDate'
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
                    data: 'StoreAddress',
                    name: 'StoreAddress'
                },
                {
                    data: 'PhoneNumber',
                    name: 'PhoneNumber'
                },
                {
                    data: 'SaldoPPOB',
                    name: 'SaldoPPOB'
                },
            ],
            "order": [[ 0, "desc" ]],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('AktivasiPPOBMerchant');
                },
                text: 'Export',
                titleAttr: 'Excel',
                exportOptions: {
                    modifier: {
                        page: 'all'
                    },
                    columns: [0, 1, 2, 3, 4, 5],
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
    $("div.filter-aktivasi-merchant").html(`<div class="input-group">
                            <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                                readonly>
                            <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                                readonly>
                            <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                            <button type="button" name="refresh" id="refresh"
                                class="btn btn-sm btn-warning ml-2">Refresh</button>
                        </div>`);

    // Setting Awal Daterangepicker
    $('#aktivasi-merchant #from_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // Setting Awal Daterangepicker
    $('#aktivasi-merchant #to_date').daterangepicker({
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

        $('#aktivasi-merchant #to_date').daterangepicker({
            minDate: $("#aktivasi-merchant #from_date").val(),
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

        $('#aktivasi-merchant #from_date').daterangepicker({
            maxDate: $("#aktivasi-merchant #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        })
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $('#aktivasi-merchant .filter-aktivasi-merchant').on('change', '#from_date', function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $('#aktivasi-merchant .filter-aktivasi-merchant').on('change', '#to_date', function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $('#aktivasi-merchant #from_date').val('');
    $('#aktivasi-merchant #to_date').val('');
    $('#aktivasi-merchant #from_date').attr("placeholder", "From Date");
    $('#aktivasi-merchant #to_date').attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#aktivasi-merchant #refresh").click(function () {
        $('#aktivasi-merchant #from_date').val('');
        $('#aktivasi-merchant #to_date').val('');
        $('#aktivasi-merchant .table-datatables').DataTable().search('');
        $('#aktivasi-merchant .table-datatables').DataTable().ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#aktivasi-merchant #filter").click(function () {
        $('#aktivasi-merchant .table-datatables').DataTable().ajax.reload();
    });
});