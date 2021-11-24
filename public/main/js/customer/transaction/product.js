$(document).ready(function () {
    // DataTables
    dataTablesTransactionProduct();

    function dataTablesTransactionProduct() {
        $('#customer-transaction-product .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'<'filter-customer-transaction-product'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            "ajax": {
                url: "/customer/transaction/product/get",
                data: function (d) {
                    d.fromDate = $('#customer-transaction-product #from_date').val();
                    d.toDate = $('#customer-transaction-product #to_date').val();
                    d.paymentMethodId = $('#customer-transaction-product .select-filter-custom select').val();
                }
            },
            columns: [
                {
                    data: 'OrderID',
                    name: 'tx_product_order.OrderID'
                },
                {
                    data: 'CreatedDate',
                    name: 'tx_product_order.CreatedDate',
                    type: 'date'
                },
                {
                    data: 'FullName',
                    name: 'ms_customer_account.FullName'
                },
                {
                    data: 'Address',
                    name: 'ms_customer_account.Address'
                },
                {
                    data: 'PhoneNumber',
                    name: 'ms_customer_account.PhoneNumber'
                },
                {
                    data: 'MerchantID',
                    name: 'tx_product_order.MerchantID'
                },
                {
                    data: 'StoreName',
                    name: 'ms_merchant_account.StoreName'
                },
                {
                    data: 'StoreAddress',
                    name: 'ms_merchant_account.StoreAddress'
                },
                {
                    data: 'DistributorName',
                    name: 'ms_distributor.DistributorName'
                },
                {
                    data: 'SalesName',
                    name: 'ms_sales.SalesName'
                },
                {
                    data: 'PaymentMethodName',
                    name: 'ms_payment_method.PaymentMethodName'
                },
                {
                    data: 'StatusOrder',
                    name: 'ms_status_order.StatusOrder'
                },
                {
                    data: 'TotalPrice',
                    name: 'tx_product_order.TotalPrice'
                },
                {
                    data: 'productID',
                    name: 'tx_product_order_detail.productID'
                },
                {
                    data: 'ProductName',
                    name: 'ms_product.ProductName'
                },
                {
                    data: 'Quantity',
                    name: 'tx_product_order_detail.Quantity'
                },
                {
                    data: 'Price',
                    name: 'tx_product_order_detail.Price'
                },
                {
                    data: 'Discount',
                    name: 'tx_product_order_detail.Discount'
                },
                {
                    data: 'Nett',
                    name: 'tx_product_order_detail.Nett'
                },
                {
                    data: 'SubTotalPrice',
                    name: 'tx_product_order_detail.SubTotalPrice'
                }
            ],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('CustomerTransactionAllProduct');
                },
                action: exportDatatableHelper.newExportAction,
                text: 'Export',
                titleAttr: 'Excel',
                exportOptions: {
                    modifier: {
                        page: 'all'
                    },
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18],
                    orthogonal: 'export'
                },
            }],
            "order": [1, 'desc'],
            "lengthChange": false,
            "responsive": true,
            "autoWidth": false,
            "aoColumnDefs": [
                {
                    "aTargets": [11, 15, 16, 17, 18],
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
    $("div.filter-customer-transaction-product").html(`<div class="input-group">
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
    $('#customer-transaction-product #from_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // Setting Awal Daterangepicker
    $('#customer-transaction-product #to_date').daterangepicker({
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

        $('#customer-transaction-product #to_date').daterangepicker({
            minDate: $("#customer-transaction-product #from_date").val(),
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

        $('#customer-transaction-product #from_date').daterangepicker({
            maxDate: $("#customer-transaction-product #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        })
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $('#customer-transaction-product .filter-customer-transaction-product').on('change', '#from_date', function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $('#customer-transaction-product .filter-customer-transaction-product').on('change', '#to_date', function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $('#customer-transaction-product #from_date').val('');
    $('#customer-transaction-product #to_date').val('');
    $('#customer-transaction-product #from_date').attr("placeholder", "From Date");
    $('#customer-transaction-product #to_date').attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#customer-transaction-product #refresh").click(function () {
        $('#customer-transaction-product #from_date').val('');
        $('#customer-transaction-product #to_date').val('');
        $('#customer-transaction-product .table-datatables').DataTable().search('');
        // $('#customer-transaction-product .select-filter-custom select').val('').change();
        // $('#customer-transaction-product .select-filter-custom select option[value=]');
        $('#customer-transaction-product .table-datatables').DataTable().ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#customer-transaction-product #filter").click(function () {
        $('#customer-transaction-product .table-datatables').DataTable().ajax.reload();
    });

    // Event listener saat tombol select option diklik
    $("#customer-transaction-product .select-filter-custom select").change(function () {
        $('#customer-transaction-product .table-datatables').DataTable().ajax.reload();
        // console.log($('#customer-transaction-product .select-filter-custom select').val());
    });
});