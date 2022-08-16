$(document).ready(function () {
    // DataTables
    dataTablesInboundStockPromo();

    function dataTablesInboundStockPromo() {
        $("#inbound-stock-promo .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-inbound-stock-promo'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            ajax: {
                url: "/stock-promo/inbound",
                data: function (d) {
                    d.fromDate = $("#inbound-stock-promo #from_date").val();
                    d.toDate = $("#inbound-stock-promo #to_date").val();
                    // d.filterTipe = $(
                    //     "#inbound-stock-promo .filter-tipe select"
                    // ).val();
                },
            },
            columns: [
                {
                    data: "Type",
                    name: "ms_stock_promo_inbound.Type",
                },
                {
                    data: "StockPromoInboundID",
                    name: "ms_stock_promo_inbound.StockPromoInboundID",
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
                    data: "InboundDate",
                    name: "ms_stock_promo_inbound.InboundDate",
                    type: "date",
                },
                {
                    data: "CreatedBy",
                    name: "ms_stock_promo_inbound.CreatedBy",
                },
                {
                    data: "StatusName",
                    name: "ms_status_stock.StatusName",
                },
                {
                    data: "StatusBy",
                    name: "ms_stock_promo_inbound.StatusBy",
                },
                {
                    data: "InvoiceNumber",
                    name: "ms_stock_promo_inbound.InvoiceNumber",
                },
                {
                    data: "InvoiceFile",
                    name: "ms_stock_promo_inbound.InvoiceFile",
                },
                {
                    data: "Action",
                    name: "Action",
                    searchable: false,
                    orderable: false,
                },
                // {
                //     data: "Confirmation",
                //     name: "Confirmation",
                //     searchable: false,
                //     orderable: false,
                // },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "InboundStockPromo"
                        );
                    },
                    text: "Export",
                    className: "btn-sm",
                    titleAttr: "Excel",
                    exportOptions: {
                        modifier: {
                            page: "all",
                        },
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                        orthogonal: "export",
                    },
                },
            ],
            aoColumnDefs: [
                {
                    aTargets: [6],
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
    $("div.filter-inbound-stock-promo").html(`<div class="input-group">
                      <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" readonly>
                      <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm" readonly>
                      <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                      <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-2">Refresh</button>
                    </div>`);

    // Setting Awal Daterangepicker
    $("#inbound-stock-promo #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#inbound-stock-promo #to_date").daterangepicker({
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

        $("#inbound-stock-promo #to_date").daterangepicker({
            minDate: $("#inbound-stock-promo #from_date").val(),
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

        $("#inbound-stock-promo #from_date").daterangepicker({
            maxDate: $("#inbound-stock-promo #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#inbound-stock-promo .filter-inbound-stock-promo").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#inbound-stock-promo .filter-inbound-stock-promo").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    // Menyisipkan Placeholder Date
    $("#inbound-stock-promo #from_date").val("");
    $("#inbound-stock-promo #to_date").val("");
    $("#inbound-stock-promo #from_date").attr("placeholder", "From Date");
    $("#inbound-stock-promo #to_date").attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#inbound-stock-promo #refresh").click(function () {
        $("#inbound-stock-promo #from_date").val("");
        $("#inbound-stock-promo #to_date").val("");
        $("#inbound-stock-promo .table-datatables").DataTable().search("");
        $("#inbound-stock-promo .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#inbound-stock-promo #filter").click(function () {
        $("#inbound-stock-promo .table-datatables").DataTable().ajax.reload();
    });

    $("#inbound-stock-promo .filter-tipe select").change(function () {
        $("#inbound-stock-promo .table-datatables").DataTable().ajax.reload();
    });
});
