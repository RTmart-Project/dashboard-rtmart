$(document).ready(function () {
    // DataTables
    dataTablesMenungguValidasi();

    function dataTablesMenungguValidasi() {
        $('#menunggu-validasi .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'<'filter-menunggu-validasi'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            "ajax": {
                url: "/ppob/topup/get/TPS-002",
                data: function (d) {
                    d.fromDate = $('#menunggu-validasi #from_date').val();
                    d.toDate = $('#menunggu-validasi #to_date').val();
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
                {
                    data: 'Action',
                    name: 'Action'
                },
            ],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('TopupMerchantPPOBMenungguValidasi');
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
                    "aTargets": [8],
                    "orderable": false
                },
                {
                    "aTargets": [5],
                    "mRender": function (data, type, full) {
                        if (type === 'export') {
                            return data;
                        } else {
                            var currencySeperatorFormat = thousands_separators(data)
                            return currencySeperatorFormat;
                        }
                    }
                }
            ]
        });
    }

    // Create element for DateRange Filter
    $("div.filter-menunggu-validasi").html(`<div class="input-group">
                            <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                                readonly>
                            <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                                readonly>
                            <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                            <button type="button" name="refresh" id="refresh"
                                class="btn btn-sm btn-warning ml-2">Refresh</button>
                        </div>`);

    // Setting Awal Daterangepicker
    $('#menunggu-validasi #from_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // Setting Awal Daterangepicker
    $('#menunggu-validasi #to_date').daterangepicker({
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

        $('#menunggu-validasi #to_date').daterangepicker({
            minDate: $("#menunggu-validasi #from_date").val(),
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

        $('#menunggu-validasi #from_date').daterangepicker({
            maxDate: $("#menunggu-validasi #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        })
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $('#menunggu-validasi .filter-menunggu-validasi').on('change', '#from_date', function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $('#menunggu-validasi .filter-menunggu-validasi').on('change', '#to_date', function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $('#menunggu-validasi #from_date').val('');
    $('#menunggu-validasi #to_date').val('');
    $('#menunggu-validasi #from_date').attr("placeholder", "From Date");
    $('#menunggu-validasi #to_date').attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#menunggu-validasi #refresh").click(function () {
        $('#menunggu-validasi #from_date').val('');
        $('#menunggu-validasi #to_date').val('');
        $('#menunggu-validasi .table-datatables').DataTable().search('');
        $('#menunggu-validasi .table-datatables').DataTable().ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#menunggu-validasi #filter").click(function () {
        $('#menunggu-validasi .table-datatables').DataTable().ajax.reload();
    });

    // Set seperator '.' currency
    const currencyJumlahTopup = new AutoNumeric('#jumlah-topup', {
        allowDecimalPadding: false,
        decimalCharacter: ',',
        digitGroupSeparator: '.',
        unformatOnSubmit: true
    });

    // Event listener saat tombol konfirmasi diklik
    $('#menunggu-validasi table').on('click', '.btn-konfirmasi', function () {
        const topupId = $(this).data("topup-id");
        const storeName = $(this).data("store-name");
        const jumlahTopup = $(this).data("jumlah-topup");

        // Ubah action dan title modal sesuai data yang diklik
        $('#konfirmasi-topup-modal form').attr('action', '/ppob/topup/confirm/' + topupId);
        $('#konfirmasi-topup-modal .modal-title').text('Topup Saldo (' + topupId + ' - ' + storeName + ')');

        // Ubah jumlah topup modal sesuai data yang diklik
        currencyJumlahTopup.set(jumlahTopup);
    });

    // Event listener saat tombol batal diklik
    $('#menunggu-validasi table').on('click', '.btn-batal', function (e) {
        e.preventDefault();
        const storeName = $(this).data("store-name");
        const topupId = $(this).data("topup-id");
        $.confirm({
            title: 'Batalkan Topup!',
            content: `Yakin ingin membatalkan topup <b>${storeName}</b> ?`,
            closeIcon: true,
            buttons: {
                batalkan: {
                    btnClass: 'btn-red',
                    draggable: true,
                    dragWindowGap: 0,
                    action: function () {
                        window.location = '/ppob/topup/cancel/' + topupId
                    }
                },
                tidak: function () {
                }
            }
        });
    });

    $('#menunggu-validasi table').on('click', '.lihat-bukti', function (e) {
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