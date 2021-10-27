$(document).ready(function () {
    // DataTables
    dataTablesRestock();

    function dataTablesRestock() {
        $('#merchant-restock .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'<'filter-merchant-restock'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            "ajax": {
                url: "/merchant/restock/get",
                data: function (d) {
                    d.fromDate = $('#merchant-restock #from_date').val();
                    d.toDate = $('#merchant-restock #to_date').val();
                    d.paymentMethodId = $('#merchant-restock .select-filter-custom select').val();
                }
            },
            columns: [
                {
                    data: 'StockOrderID',
                    name: 'StockOrderID'
                },
                {
                    data: 'CreatedDate',
                    name: 'CreatedDate'
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
                    data: 'DistributorName',
                    name: 'DistributorName'
                },
                {
                    data: 'PaymentMethodName',
                    name: 'PaymentMethodName'
                },
                {
                    data: 'StatusOrder',
                    name: 'StatusOrder'
                },
                {
                    data: 'NettPrice',
                    name: 'NettPrice'
                },
                {
                    data: 'ReferralCode',
                    name: 'ReferralCode'
                },
                {
                    data: 'Action',
                    name: 'Action'
                }
            ],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('RestockMerchant');
                },
                text: 'Export',
                titleAttr: 'Excel',
                exportOptions: {
                    modifier: {
                        page: 'all'
                    },
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                    orthogonal: 'export'
                },
            }],
            "order": [],
            "lengthChange": false,
            "responsive": true,
            "autoWidth": false,
            "aoColumnDefs": [
                {
                    "aTargets": [10],
                    "orderable": false
                },
                {
                    "aTargets": [8],
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
    $("div.filter-merchant-restock").html(`<div class="input-group">
                            <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" readonly>
                            <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm" readonly>
                            <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                            <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-2">Refresh</button>
                            <div class="select-filter-custom ml-2">
                                <select>
                                    <option value="">All</option>
                                </select>
                            </div>
                        </div>`);

    // Setting Awal Daterangepicker
    $('#merchant-restock #from_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // Setting Awal Daterangepicker
    $('#merchant-restock #to_date').daterangepicker({
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

        $('#merchant-restock #to_date').daterangepicker({
            minDate: $("#merchant-restock #from_date").val(),
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

        $('#merchant-restock #from_date').daterangepicker({
            maxDate: $("#merchant-restock #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        })
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $('#merchant-restock .filter-merchant-restock').on('change', '#from_date', function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $('#merchant-restock .filter-merchant-restock').on('change', '#to_date', function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $('#merchant-restock #from_date').val('');
    $('#merchant-restock #to_date').val('');
    $('#merchant-restock #from_date').attr("placeholder", "From Date");
    $('#merchant-restock #to_date').attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#merchant-restock #refresh").click(function () {
        $('#merchant-restock #from_date').val('');
        $('#merchant-restock #to_date').val('');
        $('#merchant-restock .table-datatables').DataTable().search('');
        // $('#merchant-restock .select-filter-custom select').val('').change();
        // $('#merchant-restock .select-filter-custom select option[value=]');
        $('#merchant-restock .table-datatables').DataTable().ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#merchant-restock #filter").click(function () {
        $('#merchant-restock .table-datatables').DataTable().ajax.reload();
    });

    // Load PaymentMethod ID and Name for filter
    $.ajax({
        type: "get",
        url: "/payment/method/get",
        success: function (data) {
            let option;
            for (const item of data) {
                option += `<option value="${item.PaymentMethodID}">${item.PaymentMethodName}</option>`;
            }
            $('#merchant-restock .select-filter-custom select').append(option);
            customDropdownFilter.createCustomDropdowns();
        }
    });

    // Event listener saat tombol select option diklik
    $("#merchant-restock .select-filter-custom select").change(function () {
        $('#merchant-restock .table-datatables').DataTable().ajax.reload();
    });
});