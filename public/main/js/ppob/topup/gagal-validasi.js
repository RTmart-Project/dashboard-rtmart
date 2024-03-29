$(document).ready(function () {
    // DataTables
    dataTablesMenungguValidasi();

    function dataTablesMenungguValidasi() {
        $('#gagal-validasi .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'<'filter-gagal-validasi'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            "ajax": {
                url: "/ppob/topup/get/TPS-004",
                data: function (d) {
                    d.fromDate = $('#gagal-validasi #from_date').val();
                    d.toDate = $('#gagal-validasi #to_date').val();
                }
            },
            columns: [
                {
                    data: 'TransactionDate',
                    name: 'TransactionDate',
                    type: 'date'
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
                    data: 'TransferPhoto',
                    name: 'TransferPhoto'
                },
            ],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('TopupMerchantPPOBDibatalkan');
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
            "order": [0, 'desc'],
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
    $("div.filter-gagal-validasi").html(`<div class="input-group">
                            <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                                readonly>
                            <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                                readonly>
                            <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                            <button type="button" name="refresh" id="refresh"
                                class="btn btn-sm btn-warning ml-2">Refresh</button>
                        </div>`);

    // Setting Awal Daterangepicker
    $('#gagal-validasi #from_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // Setting Awal Daterangepicker
    $('#gagal-validasi #to_date').daterangepicker({
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

        $('#gagal-validasi #to_date').daterangepicker({
            minDate: $("#gagal-validasi #from_date").val(),
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

        $('#gagal-validasi #from_date').daterangepicker({
            maxDate: $("#gagal-validasi #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        })
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $('#gagal-validasi .filter-gagal-validasi').on('change', '#from_date', function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $('#gagal-validasi .filter-gagal-validasi').on('change', '#to_date', function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $('#gagal-validasi #from_date').val('');
    $('#gagal-validasi #to_date').val('');
    $('#gagal-validasi #from_date').attr("placeholder", "From Date");
    $('#gagal-validasi #to_date').attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#gagal-validasi #refresh").click(function () {
        $('#gagal-validasi #from_date').val('');
        $('#gagal-validasi #to_date').val('');
        $('#gagal-validasi .table-datatables').DataTable().search('');
        $('#gagal-validasi .table-datatables').DataTable().ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#gagal-validasi #filter").click(function () {
        $('#gagal-validasi .table-datatables').DataTable().ajax.reload();
    });

    $('#gagal-validasi table').on('click', '.lihat-bukti', function (e) {
        e.preventDefault();
        const urlImg = $(this).attr("href");
        const storeName = $(this).data("store-name");
        const topupId = $(this).data("topup-id");
        $.dialog({
            title: `${topupId} - ${storeName}`,
            content: `<img  style="object-fit: contain; height: 350px; width: 100%;" src="${urlImg}">`,
        });
    });
});