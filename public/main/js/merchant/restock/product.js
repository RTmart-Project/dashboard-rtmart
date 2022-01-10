$(document).ready(function () {
    // DataTables
    dataTablesRestockProduct();

    function dataTablesRestockProduct() {
        $('#product-restock .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'<'filter-product-restock'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            "ajax": {
                url: "/merchant/restock/product/get",
                data: function (d) {
                    d.fromDate = $('#product-restock #from_date').val();
                    d.toDate = $('#product-restock #to_date').val();
                    d.paymentMethodId = $('#product-restock .select-filter-custom select').val();
                }
            },
            columns: [
                {
                    data: 'StockOrderID',
                    name: 'tx_merchant_order.StockOrderID'
                },
                {
                    data: 'CreatedDate',
                    name: 'tx_merchant_order.CreatedDate',
                    type: 'date'
                },
                {
                    data: 'MerchantID',
                    name: 'tx_merchant_order.MerchantID'
                },
                {
                    data: 'StoreName',
                    name: 'ms_merchant_account.StoreName'
                },
                {
                    data: 'PhoneNumber',
                    name: 'ms_merchant_account.PhoneNumber'
                },
                {
                    data: 'DistributorName',
                    name: 'ms_distributor.DistributorName'
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
                    name: 'tx_merchant_order.TotalPrice'
                },
                {
                    data: 'DiscountPrice',
                    name: 'tx_merchant_order.DiscountPrice'
                },
                {
                    data: 'ServiceChargeNett',
                    name: 'tx_merchant_order.ServiceChargeNett'
                },
                {
                    data: 'TotalAmount',
                    name: 'TotalAmount'
                },
                {
                    data: 'ReferralCode',
                    name: 'ms_merchant_account.ReferralCode'
                },
                {
                    data: 'SalesName',
                    name: 'ms_sales.SalesName'
                },
                {
                    data: 'ProductID',
                    name: 'tx_merchant_order_detail.ProductID'
                },
                {
                    data: 'ProductName',
                    name: 'ms_product.ProductName'
                },
                {
                    data: 'PromisedQuantity',
                    name: 'tx_merchant_order_detail.PromisedQuantity'
                },
                {
                    data: 'Price',
                    name: 'tx_merchant_order_detail.Price'
                },
                {
                    data: 'Discount',
                    name: 'tx_merchant_order_detail.Discount'
                },
                {
                    data: 'Nett',
                    name: 'tx_merchant_order_detail.Nett'
                },
                {
                    data: 'SubTotalPrice',
                    name: 'SubTotalPrice'
                }
            ],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('RestockMerchantAllProduct');
                },
                action: exportDatatableHelper.newExportAction,
                text: 'Export',
                titleAttr: 'Excel',
                exportOptions: {
                    modifier: {
                        page: 'all'
                    },
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
                    orthogonal: 'export'
                },
            }],
            "order": [1, 'desc'],
            "lengthChange": false,
            "responsive": true,
            "autoWidth": false,
            "aoColumnDefs": [
                {
                    "aTargets": [8, 9, 10, 11, 17, 18, 19, 20],
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
    $("div.filter-product-restock").html(`<div class="input-group">
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
    $('#product-restock #from_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // Setting Awal Daterangepicker
    $('#product-restock #to_date').daterangepicker({
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

        $('#product-restock #to_date').daterangepicker({
            minDate: $("#product-restock #from_date").val(),
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

        $('#product-restock #from_date').daterangepicker({
            maxDate: $("#product-restock #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        })
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $('#product-restock .filter-product-restock').on('change', '#from_date', function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $('#product-restock .filter-product-restock').on('change', '#to_date', function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $('#product-restock #from_date').val('');
    $('#product-restock #to_date').val('');
    $('#product-restock #from_date').attr("placeholder", "From Date");
    $('#product-restock #to_date').attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#product-restock #refresh").click(function () {
        $('#product-restock #from_date').val('');
        $('#product-restock #to_date').val('');
        $('#product-restock .table-datatables').DataTable().search('');
        // $('#product-restock .select-filter-custom select').val('').change();
        // $('#product-restock .select-filter-custom select option[value=]');
        $('#product-restock .table-datatables').DataTable().ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#product-restock #filter").click(function () {
        $('#product-restock .table-datatables').DataTable().ajax.reload();
    });

    // Event listener saat tombol select option diklik
    $("#product-restock .select-filter-custom select").change(function () {
        $('#product-restock .table-datatables').DataTable().ajax.reload();
    });
});