$(document).ready(function () {
    // DataTables
    dataTablesExpeditionAllProduct();

    function dataTablesExpeditionAllProduct() {
        $("#expedition-all-product .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-7'<'filter-expedition-all-product'>tl><'col-sm-12 col-md-1'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/delivery/on-going/all-product/get/'S035','S036'",
                data: function (d) {
                    d.fromDate = $("#expedition-all-product #from_date").val();
                    d.toDate = $("#expedition-all-product #to_date").val();
                },
            },
            columns: [
                {
                    data: "MerchantExpeditionID",
                    name: "tx_merchant_expedition.MerchantExpeditionID",
                },
                {
                    data: "DistributorName",
                    name: "ms_distributor.DistributorName",
                    orderable: false,
                },
                {
                    data: "CreatedDate",
                    name: "tx_merchant_expedition.CreatedDate",
                    type: "date",
                },
                {
                    data: "StockOrderID",
                    name: "tx_merchant_order.StockOrderID",
                },
                {
                    data: "DeliveryOrderID",
                    name: "tx_merchant_delivery_order_detail.DeliveryOrderID",
                },
                {
                    data: "MerchantID",
                    name: "tx_merchant_order.MerchantID",
                },
                {
                    data: "StoreName",
                    name: "ms_merchant_account.StoreName",
                },
                {
                    data: "PhoneNumber",
                    name: "ms_merchant_account.PhoneNumber",
                },
                {
                    data: "ProductID",
                    name: "tx_merchant_delivery_order_detail.ProductID",
                },
                {
                    data: "ProductName",
                    name: "ms_product.ProductName",
                },
                {
                    data: "Qty",
                    name: "tx_merchant_delivery_order_detail.Qty",
                },
                {
                    data: "Price",
                    name: "tx_merchant_delivery_order_detail.Price",
                },
                {
                    data: "ValueProduct",
                    name: "ValueProduct",
                    searchable: false,
                },
                {
                    data: "StatusOrder",
                    name: "ms_status_order.StatusOrder",
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "DeliveryOrderHistoryAllProduct"
                        );
                    },
                    action: exportDatatableHelper.newExportAction,
                    text: "Export",
                    titleAttr: "Excel",
                    className: "btn-sm",
                    exportOptions: {
                        modifier: {
                            page: "all",
                        },
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13],
                        orthogonal: "export",
                    },
                },
            ],
            aoColumnDefs: [
                {
                    aTargets: [11, 12],
                    mRender: function (data, type, full) {
                        if (type === "export") {
                            return data;
                        } else {
                            if (data == null || data == "") {
                                return data;
                            } else {
                                const currencySeperatorFormat =
                                    thousands_separators(data);
                                return currencySeperatorFormat;
                            }
                        }
                    },
                },
            ],
            order: [2, "desc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }

    // Create element for DateRange Filter
    $("div.filter-expedition-all-product").html(`<div class="input-group">
                    <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                        readonly>
                    <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                        readonly>
                    <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                    <button type="button" name="refresh" id="refresh"
                        class="btn btn-sm btn-warning ml-2">Refresh</button>
                </div>`);

    // Setting Awal Daterangepicker
    $("#expedition-all-product #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#expedition-all-product #to_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    var bCodeChange = false;

    function dateStartChange() {
        if (bCodeChange == true) return;
        else bCodeChange = true;

        $("#expedition-all-product #to_date").daterangepicker({
            minDate: $("#expedition-all-product #from_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    function dateEndChange() {
        if (bCodeChange == true) return;
        else bCodeChange = true;

        $("#expedition-all-product #from_date").daterangepicker({
            maxDate: $("#expedition-all-product #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#expedition-all-product .filter-expedition-all-product").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#expedition-all-product .filter-expedition-all-product").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    // Menyisipkan Placeholder Date
    $("#expedition-all-product #from_date").val("");
    $("#expedition-all-product #to_date").val("");
    $("#expedition-all-product #from_date").attr("placeholder", "From Date");
    $("#expedition-all-product #to_date").attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#expedition-all-product #refresh").click(function () {
        $("#expedition-all-product #from_date").val("");
        $("#expedition-all-product #to_date").val("");
        $("#expedition-all-product .table-datatables").DataTable().search("");
        // $('#expedition-all-product .select-filter-custom select').val('').change();
        // $('#expedition-all-product .select-filter-custom select option[value=]').attr('selected', 'selected');
        $("#expedition-all-product .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#expedition-all-product #filter").click(function () {
        $("#expedition-all-product .table-datatables")
            .DataTable()
            .ajax.reload();
    });
});
