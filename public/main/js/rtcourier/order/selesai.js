$(document).ready(function () {
    // DataTables
    dataTablesOrderCourier();

    function dataTablesOrderCourier() {
        $("#selesai .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-customer-transaction'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/rtcourier/order/get/4",
                data: function (d) {
                    d.fromDate = $("#selesai #from_date").val();
                    d.toDate = $("#selesai #to_date").val();
                    d.paymentMethodId = $(
                        "#selesai .select-filter-custom select"
                    ).val();
                },
            },
            columns: [
                {
                    data: "OrderID",
                    name: "tx_product_order.OrderID",
                },
                {
                    data: "CreatedDate",
                    name: "tx_product_order.CreatedDate",
                    type: "date",
                },
                {
                    data: "CourierCode",
                    name: "tx_product_order.CourierCode",
                },
                {
                    data: "CourierName",
                    name: "ms_courier.CourierName",
                },
                {
                    data: "CustomerID",
                    name: "tx_product_order.CustomerID",
                },
                {
                    data: "FullName",
                    name: "ms_customer_account.FullName",
                },
                {
                    data: "OrderAddress",
                    name: "tx_product_order.OrderAddress",
                },
                {
                    data: "CustomerPhoneNumber",
                    name: "ms_customer_account.PhoneNumber",
                },
                {
                    data: "MerchantID",
                    name: "tx_product_order.MerchantID",
                },
                {
                    data: "StoreName",
                    name: "ms_merchant_account.StoreName",
                },
                {
                    data: "StoreAddress",
                    name: "ms_merchant_account.StoreAddress",
                },
                {
                    data: "StorePhoneNumber",
                    name: "ms_merchant_account.PhoneNumber",
                },
                {
                    data: "PaymentMethodName",
                    name: "ms_payment_method.PaymentMethodName",
                },
                {
                    data: "StatusOrder",
                    name: "ms_status_order.StatusOrder",
                },
                {
                    data: "GrandTotal",
                    name: "GrandTotal",
                    searchable: false,
                },
                // {
                //     data: "Action",
                //     name: "Action",
                //     orderable: false,
                //     searchable: false,
                // },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "Customer-Courier-Transaction"
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
                        columns: [
                            0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14,
                        ],
                        orthogonal: "export",
                    },
                },
            ],
            order: [1, "desc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
            aoColumnDefs: [
                {
                    aTargets: [14],
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
        });
    }

    // Create element for DateRange Filter
    $("div.filter-customer-transaction").html(`<div class="input-group">
                          <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" readonly>
                          <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm" readonly>
                          <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                          <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-2">Refresh</button>
                      </div>`);

    // Setting Awal Daterangepicker
    $("#selesai #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#selesai #to_date").daterangepicker({
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

        $("#selesai #to_date").daterangepicker({
            minDate: $("#selesai #from_date").val(),
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

        $("#selesai #from_date").daterangepicker({
            maxDate: $("#selesai #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#selesai .filter-customer-transaction").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#selesai .filter-customer-transaction").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    // Menyisipkan Placeholder Date
    $("#selesai #from_date").val("");
    $("#selesai #to_date").val("");
    $("#selesai #from_date").attr("placeholder", "From Date");
    $("#selesai #to_date").attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#selesai #refresh").click(function () {
        $("#selesai #from_date").val("");
        $("#selesai #to_date").val("");
        $("#selesai .table-datatables").DataTable().search("");
        // $('#selesai .select-filter-custom select').val('').change();
        // $('#selesai .select-filter-custom select option[value=]');
        $("#selesai .table-datatables").DataTable().ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#selesai #filter").click(function () {
        $("#selesai .table-datatables").DataTable().ajax.reload();
    });
});
