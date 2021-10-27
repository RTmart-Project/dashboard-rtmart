$(document).ready(function () {
    // DataTables
    dataTablesMenungguValidasi();

    function dataTablesMenungguValidasi() {
        $('#selesai .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'<'filter-selesai'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            "ajax": {
                url: "/ppob/topup/get/TPS-003",
                data: function (d) {
                    d.fromDate = $('#selesai #from_date').val();
                    d.toDate = $('#selesai #to_date').val();
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
                    data: 'TransferPhoto',
                    name: 'TransferPhoto'
                },
            ],
            "order": [[ 0, "desc" ]],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('TopupMerchantPPOBSelesai');
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
    $("div.filter-selesai").html(`<div class="input-group">
                            <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                                readonly>
                            <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                                readonly>
                            <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                            <button type="button" name="refresh" id="refresh"
                                class="btn btn-sm btn-warning ml-2">Refresh</button>
                        </div>`);

    // Setting Awal Daterangepicker
    $('#selesai #from_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // Setting Awal Daterangepicker
    $('#selesai #to_date').daterangepicker({
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

        $('#selesai #to_date').daterangepicker({
            minDate: $("#selesai #from_date").val(),
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

        $('#selesai #from_date').daterangepicker({
            maxDate: $("#selesai #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        })
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $('#selesai .filter-selesai').on('change', '#from_date', function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $('#selesai .filter-selesai').on('change', '#to_date', function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $('#selesai #from_date').val('');
    $('#selesai #to_date').val('');
    $('#selesai #from_date').attr("placeholder", "From Date");
    $('#selesai #to_date').attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#selesai #refresh").click(function () {
        $('#selesai #from_date').val('');
        $('#selesai #to_date').val('');
        $('#selesai .table-datatables').DataTable().search('');
        $('#selesai .table-datatables').DataTable().ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#selesai #filter").click(function () {
        $('#selesai .table-datatables').DataTable().ajax.reload();
    });

    $('#selesai table').on('click', '.lihat-bukti', function (e) {
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