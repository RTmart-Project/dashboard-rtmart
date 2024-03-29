$(document).ready(function () {
    // DataTables
    dataTablesCustomerAccount();

    function dataTablesCustomerAccount() {
        $('#customer-account .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-7'<'filter-customer-account'>tl><'col-sm-12 col-md-1'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            "ajax": {
                url: "/customer/account/get",
                data: function (d) {
                    d.fromDate = $('#customer-account #from_date').val();
                    d.toDate = $('#customer-account #to_date').val();
                }
            },
            columns: [
                {
                    data: 'CustomerID',
                    name: 'ms_customer_account.CustomerID'
                },
                {
                    data: 'FullName',
                    name: 'ms_customer_account.FullName'
                },
                {
                    data: 'PhoneNumber',
                    name: 'ms_customer_account.PhoneNumber'
                },
                {
                    data: 'CreatedDate',
                    name: 'ms_customer_account.CreatedDate',
                    type: 'date'
                },
                {
                    data: 'Address',
                    name: 'ms_customer_account.Address'
                },
                {
                    data: 'MerchantID',
                    name: 'ms_customer_account.MerchantID'
                },
                {
                    data: 'StoreName',
                    name: 'ms_merchant_account.StoreName'
                },
                {
                    data: 'DistributorName',
                    name: 'ms_distributor.DistributorName'
                },
                {
                    data: 'ReferralCode',
                    name: 'ms_customer_account.ReferralCode'
                },
            ],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('CustomerAccount');
                },
                action: exportDatatableHelper.newExportAction,
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
            "order": [3, 'desc'],
            "lengthChange": false,
            "responsive": true,
            "autoWidth": false
        });
    }

    // Create element for DateRange Filter
    $("div.filter-customer-account").html(`<div class="input-group">
                            <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                                readonly>
                            <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                                readonly>
                            <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                            <button type="button" name="refresh" id="refresh"
                                class="btn btn-sm btn-warning ml-2">Refresh</button>
                        </div>`);

    // Setting Awal Daterangepicker
    $('#customer-account #from_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // Setting Awal Daterangepicker
    $('#customer-account #to_date').daterangepicker({
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

        $('#customer-account #to_date').daterangepicker({
            minDate: $("#customer-account #from_date").val(),
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

        $('#customer-account #from_date').daterangepicker({
            maxDate: $("#customer-account #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        })
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $('#customer-account .filter-customer-account').on('change', '#from_date', function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $('#customer-account .filter-customer-account').on('change', '#to_date', function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $('#customer-account #from_date').val('');
    $('#customer-account #to_date').val('');
    $('#customer-account #from_date').attr("placeholder", "From Date");
    $('#customer-account #to_date').attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#customer-account #refresh").click(function () {
        $('#customer-account #from_date').val('');
        $('#customer-account #to_date').val('');
        $('#customer-account .table-datatables').DataTable().search('');
        $('#customer-account .table-datatables').DataTable().ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#customer-account #filter").click(function () {
        $('#customer-account .table-datatables').DataTable().ajax.reload();
    });

});