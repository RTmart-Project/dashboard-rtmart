$(document).ready(function () {
    // DataTables
    dataTablesPurchaseStockAllProduct();

    function dataTablesPurchaseStockAllProduct() {
        $("#mutation-stock-all-product .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-mutation-stock-all-product'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            ajax: {
                url: "/stock/mutation/all-product/get",
                data: function (d) {
                    d.fromDate = $(
                        "#mutation-stock-all-product #from_date"
                    ).val();
                    d.toDate = $("#mutation-stock-all-product #to_date").val();
                },
            },
            columns: [
                {
                    data: "StockMutationID",
                    name: "ms_stock_mutation.StockMutationID",
                },
                {
                    data: "MutationDate",
                    name: "ms_stock_mutation.MutationDate",
                    type: "date",
                },
                {
                    data: "PurchaseID",
                    name: "ms_stock_mutation.PurchaseID",
                },
                {
                    data: "FromDistributorName",
                    name: "FromDistributorName",
                },
                {
                    data: "ToDistributorName",
                    name: "ToDistributorName",
                },
                {
                    data: "CreatedBy",
                    name: "ms_stock_mutation.CreatedBy",
                },
                {
                    data: "Notes",
                    name: "ms_stock_mutation.Notes",
                    orderable: false,
                },
                {
                    data: "ProductID",
                    name: "ms_stock_mutation_detail.ProductID",
                },
                {
                    data: "ProductName",
                    name: "ms_product.ProductName",
                },
                {
                    data: "ProductLabel",
                    name: "ms_stock_mutation_detail.ProductLabel",
                },
                {
                    data: "Qty",
                    name: "ms_stock_mutation_detail.Qty",
                },
                {
                    data: "PurchasePrice",
                    name: "ms_stock_mutation_detail.PurchasePrice",
                },
                {
                    data: "ValueProduct",
                    name: "ValueProduct",
                    searchable: false,
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "MutationStockAllProduct"
                        );
                    },
                    text: "Export",
                    className: "btn-sm",
                    titleAttr: "Excel",
                    exportOptions: {
                        modifier: {
                            page: "all",
                        },
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
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
            order: [1, "desc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }

    // Create element for DateRange Filter
    $("div.filter-mutation-stock-all-product").html(`<div class="input-group">
                      <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" readonly>
                      <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm" readonly>
                      <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                      <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-2">Refresh</button>
                    </div>`);

    // Setting Awal Daterangepicker
    $("#mutation-stock-all-product #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#mutation-stock-all-product #to_date").daterangepicker({
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

        $("#mutation-stock-all-product #to_date").daterangepicker({
            minDate: $("#mutation-stock-all-product #from_date").val(),
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

        $("#mutation-stock-all-product #from_date").daterangepicker({
            maxDate: $("#mutation-stock-all-product #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#mutation-stock-all-product .filter-mutation-stock-all-product").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#mutation-stock-all-product .filter-mutation-stock-all-product").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    // Menyisipkan Placeholder Date
    $("#mutation-stock-all-product #from_date").val("");
    $("#mutation-stock-all-product #to_date").val("");
    $("#mutation-stock-all-product #from_date").attr(
        "placeholder",
        "From Date"
    );
    $("#mutation-stock-all-product #to_date").attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#mutation-stock-all-product #refresh").click(function () {
        $("#mutation-stock-all-product #from_date").val("");
        $("#mutation-stock-all-product #to_date").val("");
        $("#mutation-stock-all-product .table-datatables")
            .DataTable()
            .search("");
        $("#mutation-stock-all-product .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#mutation-stock-all-product #filter").click(function () {
        $("#mutation-stock-all-product .table-datatables")
            .DataTable()
            .ajax.reload();
    });
});
