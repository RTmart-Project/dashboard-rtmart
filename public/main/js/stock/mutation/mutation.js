$(document).ready(function () {
    // DataTables
    dataTablesPurchaseStock();

    function dataTablesPurchaseStock() {
        $("#mutation-stock .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-mutation-stock'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            ajax: {
                url: "/stock/mutation/get",
                data: function (d) {
                    d.fromDate = $("#mutation-stock #from_date").val();
                    d.toDate = $("#mutation-stock #to_date").val();
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
                    data: "Detail",
                    name: "Detail",
                    orderable: false,
                    searchable: false,
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "MutationStock"
                        );
                    },
                    text: "Export",
                    className: "btn-sm",
                    titleAttr: "Excel",
                    exportOptions: {
                        modifier: {
                            page: "all",
                        },
                        columns: [0, 1, 2, 3, 4, 5, 6],
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
    $("div.filter-mutation-stock").html(`<div class="input-group">
                      <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" readonly>
                      <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm" readonly>
                      <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                      <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-2">Refresh</button>
                    </div>`);

    // Setting Awal Daterangepicker
    $("#mutation-stock #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#mutation-stock #to_date").daterangepicker({
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

        $("#mutation-stock #to_date").daterangepicker({
            minDate: $("#mutation-stock #from_date").val(),
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

        $("#mutation-stock #from_date").daterangepicker({
            maxDate: $("#mutation-stock #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#mutation-stock .filter-mutation-stock").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#mutation-stock .filter-mutation-stock").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    // Menyisipkan Placeholder Date
    $("#mutation-stock #from_date").val("");
    $("#mutation-stock #to_date").val("");
    $("#mutation-stock #from_date").attr("placeholder", "From Date");
    $("#mutation-stock #to_date").attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#mutation-stock #refresh").click(function () {
        $("#mutation-stock #from_date").val("");
        $("#mutation-stock #to_date").val("");
        $("#mutation-stock .table-datatables").DataTable().search("");
        $("#mutation-stock .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#mutation-stock #filter").click(function () {
        $("#mutation-stock .table-datatables").DataTable().ajax.reload();
    });
});
