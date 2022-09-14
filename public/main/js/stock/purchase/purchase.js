$(document).ready(function () {
    // DataTables
    dataTablesPurchaseStock();

    function dataTablesPurchaseStock() {
        $("#purchase-stock .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-purchase-stock'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            ajax: {
                url: "/stock/purchase/get",
                data: function (d) {
                    d.fromDate = $("#purchase-stock #from_date").val();
                    d.toDate = $("#purchase-stock #to_date").val();
                    d.filterTipe = $(
                        "#purchase-stock .filter-tipe select"
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
                    data: "PurchasePlanID",
                    name: "ms_stock_purchase.PurchasePlanID",
                },
                {
                    data: "DistributorName",
                    name: "DistributorName",
                },
                {
                    data: "InvestorName",
                    name: "ms_investor.InvestorName",
                },
                {
                    data: "SupplierName",
                    name: "SupplierName",
                },
                {
                    data: "PurchaseDate",
                    name: "ms_stock_purchase.PurchaseDate",
                    type: "date",
                },
                {
                    data: "GrandTotal",
                    name: "GrandTotal",
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
                {
                    data: "Action",
                    name: "Action",
                    searchable: false,
                    orderable: false,
                },
                {
                    data: "Confirmation",
                    name: "Confirmation",
                    searchable: false,
                    orderable: false,
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "PurchaseStock"
                        );
                    },
                    text: "Export",
                    className: "btn-sm",
                    titleAttr: "Excel",
                    exportOptions: {
                        modifier: {
                            page: "all",
                        },
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                        orthogonal: "export",
                    },
                },
            ],
            aoColumnDefs: [
                {
                    aTargets: [7],
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
            order: [6, "desc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }

    // Create element for DateRange Filter
    $("div.filter-purchase-stock").html(`<div class="input-group">
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
    $("#purchase-stock #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#purchase-stock #to_date").daterangepicker({
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

        $("#purchase-stock #to_date").daterangepicker({
            minDate: $("#purchase-stock #from_date").val(),
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

        $("#purchase-stock #from_date").daterangepicker({
            maxDate: $("#purchase-stock #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#purchase-stock .filter-purchase-stock").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#purchase-stock .filter-purchase-stock").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    // Menyisipkan Placeholder Date
    $("#purchase-stock #from_date").val("");
    $("#purchase-stock #to_date").val("");
    $("#purchase-stock #from_date").attr("placeholder", "From Date");
    $("#purchase-stock #to_date").attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#purchase-stock #refresh").click(function () {
        $("#purchase-stock #from_date").val("");
        $("#purchase-stock #to_date").val("");
        $("#purchase-stock .table-datatables").DataTable().search("");
        $("#purchase-stock .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#purchase-stock #filter").click(function () {
        $("#purchase-stock .table-datatables").DataTable().ajax.reload();
    });

    $("#purchase-stock .filter-tipe select").change(function () {
        $("#purchase-stock .table-datatables").DataTable().ajax.reload();
    });
});
