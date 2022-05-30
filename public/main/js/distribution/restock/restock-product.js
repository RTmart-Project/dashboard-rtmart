$(document).ready(function () {
    // DataTables
    dataTablesRestockProduct();

    function dataTablesRestockProduct() {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf_token"]').attr("content"),
            },
        });

        $("#restock-all-product .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-restock-all-product'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/merchant/restock/product/get",
                method: "POST",
                data: function (d) {
                    d.fromDate = $("#restock-all-product #from_date").val();
                    d.toDate = $("#restock-all-product #to_date").val();
                    d.paymentMethodId = $(
                        "#restock-all-product .select-filter-custom select"
                    ).val();
                },
            },
            columns: [
                {
                    data: "StockOrderID",
                    name: "RestockProduct.StockOrderID",
                },
                {
                    data: "CreatedDate",
                    name: "RestockProduct.CreatedDate",
                    type: "date",
                },
                {
                    data: "DistributorName",
                    name: "RestockProduct.DistributorName",
                },
                {
                    data: "MerchantID",
                    name: "RestockProduct.MerchantID",
                },
                {
                    data: "StoreName",
                    name: "RestockProduct.StoreName",
                },
                {
                    data: "Grade",
                    name: "RestockProduct.Grade",
                },
                {
                    data: "PhoneNumber",
                    name: "RestockProduct.PhoneNumber",
                },
                {
                    data: "Partner",
                    name: "RestockProduct.Partner",
                },
                {
                    data: "PaymentMethodName",
                    name: "RestockProduct.PaymentMethodName",
                },
                {
                    data: "StatusOrder",
                    name: "RestockProduct.StatusOrder",
                },
                {
                    data: "StoreAddress",
                    name: "ms_merchant_account.StoreAddress",
                    searchable: false,
                },
                {
                    data: "TotalPrice",
                    name: "RestockProduct.TotalPrice",
                },
                {
                    data: "DiscountPrice",
                    name: "RestockProduct.DiscountPrice",
                },
                {
                    data: "DiscountVoucher",
                    name: "RestockProduct.DiscountVoucher",
                },
                {
                    data: "ServiceChargeNett",
                    name: "RestockProduct.ServiceChargeNett",
                },
                {
                    data: "DeliveryFee",
                    name: "RestockProduct.DeliveryFee",
                },
                {
                    data: "TotalAmount",
                    name: "TotalAmount",
                },
                {
                    data: "ReferralCode",
                    name: "RestockProduct.ReferralCode",
                },
                {
                    data: "SalesName",
                    name: "RestockProduct.SalesName",
                },
                {
                    data: "ProductID",
                    name: "RestockProduct.ProductID",
                },
                {
                    data: "ProductName",
                    name: "RestockProduct.ProductName",
                },
                {
                    data: "PromisedQuantity",
                    name: "RestockProduct.PromisedQuantity",
                },
                {
                    data: "Price",
                    name: "RestockProduct.Price",
                },
                {
                    data: "Discount",
                    name: "RestockProduct.Discount",
                },
                {
                    data: "Nett",
                    name: "RestockProduct.Nett",
                },
                {
                    data: "SubTotalPrice",
                    name: "SubTotalPrice",
                },
                {
                    data: "PurchasePriceEstimation",
                    name: "RestockProduct.PurchasePriceEstimation",
                    searchable: false,
                },
                {
                    data: "MarginEstimation",
                    name: "MarginEstimation",
                    searchable: false,
                },
                {
                    data: "MarginEstimationPercentage",
                    name: "MarginEstimationPercentage",
                    searchable: false,
                },
                {
                    data: "PurchasePriceReal",
                    name: "RestockProduct.PurchasePriceReal",
                    searchable: false,
                },
                {
                    data: "MarginReal",
                    name: "MarginReal",
                    searchable: false,
                },
                {
                    data: "MarginRealPercentage",
                    name: "MarginRealPercentage",
                    searchable: false,
                },
                {
                    data: "TotalMargin",
                    name: "TotalMargin",
                    searchable: false,
                },
                {
                    data: "TotalMarginPercentage",
                    name: "TotalMarginPercentage",
                    searchable: false,
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "RestockAllProduct"
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
                            15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27,
                            28, 29, 30, 31, 32, 33,
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
                    aTargets: [
                        11, 12, 13, 14, 15, 16, 21, 22, 23, 24, 25, 26, 27, 29,
                        30, 32,
                    ],
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
    $("div.filter-restock-all-product").html(`<div class="input-group">
                          <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" readonly>
                          <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm" readonly>
                          <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                          <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-2">Refresh</button>
                      </div>`);

    // Setting Awal Daterangepicker
    $("#restock-all-product #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#restock-all-product #to_date").daterangepicker({
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

        $("#restock-all-product #to_date").daterangepicker({
            minDate: $("#restock-all-product #from_date").val(),
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

        $("#restock-all-product #from_date").daterangepicker({
            maxDate: $("#restock-all-product #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#restock-all-product .filter-restock-all-product").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#restock-all-product .filter-restock-all-product").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    const d = new Date();
    const date = `${d.getFullYear()}-${("0" + (d.getMonth() + 1)).slice(-2)}-${(
        "0" + d.getDate()
    ).slice(-2)}`;

    // Menyisipkan Placeholder Date
    $("#restock-all-product #from_date").val("");
    $("#restock-all-product #to_date").val("");
    $("#restock-all-product #from_date").attr("placeholder", date);
    $("#restock-all-product #to_date").attr("placeholder", date);

    // Event Listener saat tombol refresh diklik
    $("#restock-all-product #refresh").click(function () {
        $("#restock-all-product #from_date").val("");
        $("#restock-all-product #to_date").val("");
        $("#restock-all-product .table-datatables").DataTable().search("");
        // $('#restock-all-product .select-filter-custom select').val('').change();
        // $('#restock-all-product .select-filter-custom select option[value=]');
        $("#restock-all-product .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#restock-all-product #filter").click(function () {
        $("#restock-all-product .table-datatables").DataTable().ajax.reload();
    });
});
