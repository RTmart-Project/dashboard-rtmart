$(document).ready(function () {
    // DataTables
    dataTablesTransactions();

    function dataTablesTransactions() {
        $('#customer-transaction .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'<'filter-customer-transaction'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            "ajax": {
                url: "/customer/transaction/get",
                data: function (d) {
                    d.fromDate = $('#customer-transaction #from_date').val();
                    d.toDate = $('#customer-transaction #to_date').val();
                    d.paymentMethodId = $('#customer-transaction .select-filter-custom select').val();
                }
            },
            columns: [
                {
                    data: 'OrderID',
                    name: 'OrderID'
                },
                {
                    data: 'CustomerID',
                    name: 'CustomerID'
                },
                {
                    data: 'FullName',
                    name: 'FullName'
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
                    data: 'StoreName',
                    name: 'StoreName'
                },
                {
                    data: 'TotalPrice',
                    name: 'TotalPrice'
                },
                {
                    data: 'StatusOrder',
                    name: 'StatusOrder'
                },
                {
                    data: 'CreatedDate',
                    name: 'CreatedDate'
                },
                {
                    data: 'Action',
                    name: 'Action'
                }
            ],
            "order": [[ 8, "desc" ]],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('CustomerTransaction');
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
                    "aTargets": [9],
                    "orderable": false
                },
                {
                    "aTargets": [6],
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
    $("div.filter-customer-transaction").html(`<div class="input-group">
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
    $('#customer-transaction #from_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // Setting Awal Daterangepicker
    $('#customer-transaction #to_date').daterangepicker({
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

        $('#customer-transaction #to_date').daterangepicker({
            minDate: $("#customer-transaction #from_date").val(),
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

        $('#customer-transaction #from_date').daterangepicker({
            maxDate: $("#customer-transaction #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        })
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $('#customer-transaction .filter-customer-transaction').on('change', '#from_date', function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $('#customer-transaction .filter-customer-transaction').on('change', '#to_date', function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $('#customer-transaction #from_date').val('');
    $('#customer-transaction #to_date').val('');
    $('#customer-transaction #from_date').attr("placeholder", "From Date");
    $('#customer-transaction #to_date').attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#customer-transaction #refresh").click(function () {
        $('#customer-transaction #from_date').val('');
        $('#customer-transaction #to_date').val('');
        $('#customer-transaction .table-datatables').DataTable().search('');
        // $('#customer-transaction .select-filter-custom select').val('').change();
        // $('#customer-transaction .select-filter-custom select option[value=]');
        $('#customer-transaction .table-datatables').DataTable().ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#customer-transaction #filter").click(function () {
        $('#customer-transaction .table-datatables').DataTable().ajax.reload();
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
            $('#customer-transaction .select-filter-custom select').append(option);
            customDropdownFilter.createCustomDropdowns();
        }
    });

    // Event listener saat tombol select option diklik
    $("#customer-transaction .select-filter-custom select").change(function () {
        $('#customer-transaction .table-datatables').DataTable().ajax.reload();
        // console.log($('#customer-transaction .select-filter-custom select').val());
    });
});