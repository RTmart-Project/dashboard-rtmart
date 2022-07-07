$(document).ready(function () {
    // DataTables
    dataTablesPurchaseStockAllProduct();

    function dataTablesPurchaseStockAllProduct() {
        $("#purchase-stock-all-product .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-purchase-stock-all-product'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            ajax: {
                url: "/stock/purchase/all-product/get",
                data: function (d) {
                    d.fromDate = $(
                        "#purchase-stock-all-product #from_date"
                    ).val();
                    d.toDate = $("#purchase-stock-all-product #to_date").val();
                    d.filterTipe = $(
                        "#purchase-stock-all-product .filter-tipe select"
                    ).val();
                },
            },
            columns: [
                {
                    data: "Type",
                    name: "ms_stock_purchase.Type",
                },
                {
                    data: "PurchaseID",
                    name: "ms_stock_purchase.PurchaseID",
                },
                {
                    data: "DistributorName",
                    name: "ms_distributor.DistributorName",
                },
                {
                    data: "InvestorName",
                    name: "ms_investor.InvestorName",
                },
                {
                    data: "SupplierName",
                    name: "ms_suppliers.SupplierName",
                },
                {
                    data: "PurchaseDate",
                    name: "ms_stock_purchase.PurchaseDate",
                    type: "date",
                },
                {
                    data: "ProductName",
                    name: "ms_product.ProductName",
                },
                {
                    data: "ProductLabel",
                    name: "ms_stock_purchase_detail.ProductLabel",
                },
                {
                    data: "Qty",
                    name: "ms_stock_purchase_detail.Qty",
                },
                {
                    data: "PurchasePrice",
                    name: "ms_stock_purchase_detail.PurchasePrice",
                },
                {
                    data: "SubTotalPrice",
                    name: "SubTotalPrice",
                    searchable: false,
                },
                {
                    data: "GrandTotal",
                    name: "GrandTotal",
                    searchable: false,
                },
                {
                    data: "CreatedBy",
                    name: "ms_stock_purchase.CreatedBy",
                },
                {
                    data: "StatusName",
                    name: "ms_status_stock.StatusName",
                },
                {
                    data: "StatusBy",
                    name: "ms_stock_purchase.StatusBy",
                },
                {
                    data: "InvoiceNumber",
                    name: "ms_stock_purchase.InvoiceNumber",
                },
                {
                    data: "InvoiceFile",
                    name: "ms_stock_purchase.InvoiceFile",
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "PurchaseStockAllProduct"
                        );
                    },
                    text: "Export",
                    className: "btn-sm",
                    titleAttr: "Excel",
                    exportOptions: {
                        modifier: {
                            page: "all",
                        },
                        columns: [
                            0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14,
                            15, 16,
                        ],
                        orthogonal: "export",
                    },
                },
            ],
            aoColumnDefs: [
                {
                    aTargets: [9, 10, 11],
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
            order: [5, "desc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }

    // Create element for DateRange Filter
    $("div.filter-purchase-stock-all-product").html(`<div class="input-group">
                      <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" readonly>
                      <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm" readonly>
                      <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                      <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-2">Refresh</button>
                      <div class="filter-tipe ml-2">
                          <select class="form-control form-control-sm">
                              <option selected disabled hidden>Filter Tipe</option>
                              <option value="">Semua</option>
                              <option value="inbound">Inbound</option>
                              <option value="retur">Retur</option>
                          </select>
                      </div>
                    </div>`);

    // Setting Awal Daterangepicker
    $("#purchase-stock-all-product #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#purchase-stock-all-product #to_date").daterangepicker({
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

        $("#purchase-stock-all-product #to_date").daterangepicker({
            minDate: $("#purchase-stock-all-product #from_date").val(),
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

        $("#purchase-stock-all-product #from_date").daterangepicker({
            maxDate: $("#purchase-stock-all-product #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#purchase-stock-all-product .filter-purchase-stock-all-product").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#purchase-stock-all-product .filter-purchase-stock-all-product").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    // Menyisipkan Placeholder Date
    $("#purchase-stock-all-product #from_date").val("");
    $("#purchase-stock-all-product #to_date").val("");
    $("#purchase-stock-all-product #from_date").attr(
        "placeholder",
        "From Date"
    );
    $("#purchase-stock-all-product #to_date").attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#purchase-stock-all-product #refresh").click(function () {
        $("#purchase-stock-all-product #from_date").val("");
        $("#purchase-stock-all-product #to_date").val("");
        $("#purchase-stock-all-product .table-datatables")
            .DataTable()
            .search("");
        $("#purchase-stock-all-product .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#purchase-stock-all-product #filter").click(function () {
        $("#purchase-stock-all-product .table-datatables")
            .DataTable()
            .ajax.reload();
    });

    $("#purchase-stock-all-product .filter-tipe select").change(function () {
        $("#purchase-stock-all-product .table-datatables")
            .DataTable()
            .ajax.reload();
    });
});
