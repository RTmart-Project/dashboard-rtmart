$(document).ready(function () {
    // DataTables
    dataTablesSemuaRestock();

    function dataTablesSemuaRestock() {
        $("#semua-restock .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-semua-restock'>tl><'col-sm-12 col-md-4 justify-content-end'f><'col-sm-6 col-md-3 text-center'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/distribution/restock/get/allRestockAndDO",
                data: function (d) {
                    d.fromDate = $("#semua-restock #from_date").val();
                    d.toDate = $("#semua-restock #to_date").val();
                },
            },
            columns: [
                {
                    data: "StockOrderID",
                    name: "tx_merchant_order.StockOrderID",
                },
                {
                    data: "CreatedDate",
                    name: "tx_merchant_order.CreatedDate",
                    type: "date",
                },
                {
                    data: "DistributorName",
                    name: "ms_distributor.DistributorName",
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
                    data: "Sales",
                    name: "Sales",
                },
                {
                    data: "TotalTrx",
                    name: "TotalTrx",
                },
                {
                    data: "PhoneNumber",
                    name: "ms_merchant_account.PhoneNumber",
                },
                {
                    data: "Partner",
                    name: "ms_merchant_account.Partner",
                },
                {
                    data: "StatusOrder",
                    name: "ms_status_order.StatusOrder",
                },
                {
                    data: "DeliveryOrderID",
                    name: "tx_merchant_delivery_order.DeliveryOrderID",
                },
                {
                    data: "TanggalDO",
                    name: "TanggalDO",
                    type: "date",
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
                    data: "TotalPrice",
                    name: "TotalPrice",
                },
                {
                    data: "StatusDO",
                    name: "StatusDO",
                },
                {
                    data: "Name",
                    name: "ms_user.Name",
                },
                {
                    data: "VehicleName",
                    name: "ms_vehicle.VehicleName",
                },
                {
                    data: "VehicleLicensePlate",
                    name: "tx_merchant_delivery_order.VehicleLicensePlate",
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "PO-Restock"
                        );
                    },
                    action: exportDatatableHelper.newExportAction,
                    text: "Export PO",
                    titleAttr: "Excel",
                    className: "btn-sm mr-1 rounded",
                    excelStyles: [
                        {
                            cells: "A2:I2",
                            style: {
                                fill: {
                                    pattern: {
                                        color: "92D04F",
                                    },
                                },
                            },
                        },
                    ],
                    exportOptions: {
                        modifier: {
                            page: "all",
                        },
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                        orthogonal: "export",
                    },
                },
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "DO-Restock"
                        );
                    },
                    action: exportDatatableHelper.newExportAction,
                    text: "Export DO",
                    titleAttr: "Excel",
                    className: "btn-sm ml-1 rounded",
                    excelStyles: [
                        {
                            cells: "A2:I2",
                            style: {
                                fill: {
                                    pattern: {
                                        color: "92D04F",
                                    },
                                },
                            },
                        },
                        {
                            cells: "J2:S2",
                            style: {
                                fill: {
                                    pattern: {
                                        color: "25B0F0",
                                    },
                                },
                            },
                        },
                    ],
                    exportOptions: {
                        modifier: {
                            page: "all",
                        },
                        columns: [
                            0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14,
                            15, 16, 17, 18,
                        ],
                        orthogonal: "export",
                    },
                },
            ],
            aoColumnDefs: [
                {
                    aTargets: [6, 14, 15],
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
    $("div.filter-semua-restock").html(`<div class="input-group">
                            <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" readonly>
                            <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm" readonly>
                            <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                            <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-2">Refresh</button>
                        </div>`);

    // Setting Awal Daterangepicker
    $("#semua-restock #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#semua-restock #to_date").daterangepicker({
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

        $("#semua-restock #to_date").daterangepicker({
            minDate: $("#semua-restock #from_date").val(),
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

        $("#semua-restock #from_date").daterangepicker({
            maxDate: $("#semua-restock #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#semua-restock .filter-semua-restock").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#semua-restock .filter-semua-restock").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    // Menyisipkan Placeholder Date
    $("#semua-restock #from_date").val("");
    $("#semua-restock #to_date").val("");
    $("#semua-restock #from_date").attr("placeholder", "From Date");
    $("#semua-restock #to_date").attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#semua-restock #refresh").click(function () {
        $("#semua-restock #from_date").val("");
        $("#semua-restock #to_date").val("");
        $("#semua-restock .table-datatables").DataTable().search("");
        $("#semua-restock .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#semua-restock #filter").click(function () {
        $("#semua-restock .table-datatables").DataTable().ajax.reload();
    });
});
