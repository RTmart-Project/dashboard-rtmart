$(document).ready(function () {
    // DataTables
    dataTablesDalamProses();

    function dataTablesDalamProses() {
        $("#dalam-proses .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-dalam-proses'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/distribution/restock/get/S023",
                data: function (d) {
                    d.fromShipmentDate = $("#dalam-proses #from_date").val();
                    d.toShipmentDate = $("#dalam-proses #to_date").val();
                },
            },
            columns: [
                {
                    data: "StockOrderID",
                    name: "tx_merchant_order.StockOrderID",
                },
                {
                    data: "ShipmentDate",
                    name: "tx_merchant_order.ShipmentDate",
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
                    data: "Partner",
                    name: "ms_merchant_account.Partner",
                },
                {
                    data: "OwnerFullName",
                    name: "ms_merchant_account.OwnerFullName",
                },
                {
                    data: "PhoneNumber",
                    name: "ms_merchant_account.PhoneNumber",
                },
                {
                    data: "StoreAddress",
                    name: "ms_merchant_account.StoreAddress",
                },
                {
                    data: "Invoice",
                    name: "Invoice",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "Action",
                    name: "Action",
                    orderable: false,
                    searchable: false,
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "RestockDalamProses"
                        );
                    },
                    action: exportDatatableHelper.newExportAction,
                    text: "Export",
                    titleAttr: "Excel",
                    exportOptions: {
                        modifier: {
                            page: "all",
                        },
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                        orthogonal: "export",
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
    $("div.filter-dalam-proses").html(`<div class="input-group">
                            <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" readonly>
                            <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm" readonly>
                            <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                            <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-2">Refresh</button>
                        </div>`);

    // Setting Awal Daterangepicker
    $("#dalam-proses #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#dalam-proses #to_date").daterangepicker({
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

        $("#dalam-proses #to_date").daterangepicker({
            minDate: $("#dalam-proses #from_date").val(),
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

        $("#dalam-proses #from_date").daterangepicker({
            maxDate: $("#dalam-proses #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#dalam-proses .filter-dalam-proses").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#dalam-proses .filter-dalam-proses").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    // Menyisipkan Placeholder Date
    $("#dalam-proses #from_date").val("");
    $("#dalam-proses #to_date").val("");
    $("#dalam-proses #from_date").attr("placeholder", "From Date");
    $("#dalam-proses #to_date").attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#dalam-proses #refresh").click(function () {
        $("#dalam-proses #from_date").val("");
        $("#dalam-proses #to_date").val("");
        $("#dalam-proses .table-datatables").DataTable().search("");
        $("#dalam-proses .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#dalam-proses #filter").click(function () {
        $("#dalam-proses .table-datatables").DataTable().ajax.reload();
    });
});
