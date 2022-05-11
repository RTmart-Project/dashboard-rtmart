$(document).ready(function () {
    // DataTables
    dataTablesOpnameStock();

    function dataTablesOpnameStock() {
        $("#opname-stock .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-opname-stock'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            ajax: {
                url: "/stock/opname/get",
                data: function (d) {
                    d.fromDate = $("#opname-stock #from_date").val();
                    d.toDate = $("#opname-stock #to_date").val();
                },
            },
            columns: [
                {
                    data: "StockOpnameID",
                    name: "ms_stock_opname.StockOpnameID",
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
                    data: "OpnameDate",
                    name: "ms_stock_opname.OpnameDate",
                    type: "date",
                },
                {
                    data: "OfficerOpname",
                    name: "OfficerOpname",
                },
                {
                    data: "Notes",
                    name: "ms_stock_opname.Notes",
                },
                {
                    data: "Detail",
                    name: "Detail",
                    searchable: false,
                    orderable: false,
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "StockOpname"
                        );
                    },
                    text: "Export",
                    className: "btn-sm",
                    titleAttr: "Excel",
                    exportOptions: {
                        modifier: {
                            page: "all",
                        },
                        columns: [0, 1, 2, 3, 4],
                        orthogonal: "export",
                    },
                },
            ],
            order: [3, "desc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }

    // Create element for DateRange Filter
    $("div.filter-opname-stock").html(`<div class="input-group">
                        <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                            readonly>
                        <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                            readonly>
                        <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                        <button type="button" name="refresh" id="refresh"
                            class="btn btn-sm btn-warning ml-2">Refresh</button>
                    </div>`);

    // Setting Awal Daterangepicker
    $("#opname-stock #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#opname-stock #to_date").daterangepicker({
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

        $("#opname-stock #to_date").daterangepicker({
            minDate: $("#opname-stock #from_date").val(),
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

        $("#opname-stock #from_date").daterangepicker({
            maxDate: $("#opname-stock #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#opname-stock .filter-opname-stock").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#opname-stock .filter-opname-stock").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    // Menyisipkan Placeholder Date
    $("#opname-stock #from_date").val("");
    $("#opname-stock #to_date").val("");
    $("#opname-stock #from_date").attr("placeholder", "From Date");
    $("#opname-stock #to_date").attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#opname-stock #refresh").click(function () {
        $("#opname-stock #from_date").val("");
        $("#opname-stock #to_date").val("");
        $("#opname-stock .table-datatables").DataTable().search("");
        $("#opname-stock .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#opname-stock #filter").click(function () {
        $("#opname-stock .table-datatables").DataTable().ajax.reload();
    });
});
