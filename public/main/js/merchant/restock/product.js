$(document).ready(function () {
    // DataTables
    dataTablesRestockProduct();

    function dataTablesRestockProduct() {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf_token"]').attr("content"),
            },
        });

        $("#product-restock .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-9'<'filter-product-restock'>tl><'col-sm-12 col-md-2'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/merchant/restock/product/get",
                method: "POST",
                data: function (d) {
                    d.fromDate = $("#product-restock #from_date").val();
                    d.toDate = $("#product-restock #to_date").val();
                    d.paymentMethodId = $(
                        "#product-restock .filter-payment select"
                    ).val();
                    d.filterAssessment = $(
                        "#product-restock .filter-assessment select"
                    ).val();
                    d.filterValid = $(
                        "#product-restock .filter-valid select"
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
                    data: "MerchantID",
                    name: "RestockProduct.MerchantID",
                },
                {
                    data: "StoreName",
                    name: "RestockProduct.StoreName",
                },
                {
                    data: "OwnerFullName",
                    name: "RestockProduct.OwnerFullName",
                },
                {
                    data: "StoreAddress",
                    name: "RestockProduct.StoreAddress",
                },
                {
                    data: "NumberIDCard",
                    name: "RestockProduct.NumberIDCard",
                },
                {
                    data: "Ket",
                    name: "Ket",
                    searchable: false,
                    orderable: false,
                },
                {
                    data: "TurnoverAverage",
                    name: "RestockProduct.TurnoverAverage",
                },
                {
                    data: "Grade",
                    name: "RestockProduct.Grade",
                },
                {
                    data: "Partner",
                    name: "RestockProduct.Partner",
                },
                {
                    data: "PhoneNumber",
                    name: "RestockProduct.PhoneNumber",
                },
                {
                    data: "DistributorName",
                    name: "RestockProduct.DistributorName",
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
                    data: "QtyDOkirim",
                    name: "RestockProduct.QtyDOkirim",
                    searchable: false,
                },
                {
                    data: "DOSelesai",
                    name: "RestockProduct.DOSelesai",
                    searchable: false,
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
                            "RestockMerchantAllProduct"
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
                            28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39,
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
                        8, 15, 16, 17, 18, 19, 20, 28, 29, 30, 31, 32, 33, 35,
                        36, 38,
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
                {
                    aTargets: [6],
                    mRender: function (data, type, full) {
                        if (type === "export") {
                            return "'" + data;
                        } else {
                            return data;
                        }
                    },
                },
            ],
        });
    }

    // Create element for DateRange Filter
    $("div.filter-product-restock").html(`<div class="input-group">
                            <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" readonly>
                            <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm" readonly>
                            <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                            <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-2">Refresh</button>
                            <div class="filter-payment ml-2">
                                <select class="form-control form-control-sm">
                                    <option selected disabled hidden>Filter Pembayaran</option>
                                    <option value="">All</option>
                                </select>
                            </div>
                            <div class="filter-assessment ml-2">
                                <select class="form-control form-control-sm">
                                    <option selected disabled hidden>Filter Assessment</option>
                                    <option value="">All</option>
                                    <option value="already-assessed">Sudah Assessment</option>
                                    <option value="not-assessed">Belum Assessment</option>
                                </select>
                            </div>
                            <div class="filter-valid ml-2">
                                <select class="form-control form-control-sm">
                                    <option selected disabled hidden>Filter Valid</option>
                                    <option value="">All</option>
                                    <option value="valid">Valid Checked</option>
                                    <option value="invalid">Valid Unchecked</option>
                                </select>
                            </div>
                        </div>`);

    // Setting Awal Daterangepicker
    $("#product-restock #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#product-restock #to_date").daterangepicker({
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

        $("#product-restock #to_date").daterangepicker({
            minDate: $("#product-restock #from_date").val(),
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

        $("#product-restock #from_date").daterangepicker({
            maxDate: $("#product-restock #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#product-restock .filter-product-restock").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#product-restock .filter-product-restock").on(
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
    $("#product-restock #from_date").val("");
    $("#product-restock #to_date").val("");
    $("#product-restock #from_date").attr("placeholder", date);
    $("#product-restock #to_date").attr("placeholder", date);

    // Event Listener saat tombol refresh diklik
    $("#product-restock #refresh").click(function () {
        $("#product-restock #from_date").val("");
        $("#product-restock #to_date").val("");
        $("#product-restock .table-datatables").DataTable().search("");
        $("#product-restock .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#product-restock #filter").click(function () {
        $("#product-restock .table-datatables").DataTable().ajax.reload();
    });

    // Event listener saat tombol select option diklik
    $("#product-restock .filter-payment select").change(function () {
        $("#product-restock .table-datatables").DataTable().ajax.reload();
    });

    $("#product-restock .filter-assessment select").change(function () {
        $("#product-restock .table-datatables").DataTable().ajax.reload();
    });

    $("#product-restock .filter-valid select").change(function () {
        $("#product-restock .table-datatables").DataTable().ajax.reload();
    });
});
