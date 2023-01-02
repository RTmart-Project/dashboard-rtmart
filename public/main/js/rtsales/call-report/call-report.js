$(document).ready(function () {
    // DataTables
    dataTablesCallReport();

    function dataTablesCallReport() {
        $("#call-report .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-call-report'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/rtsales/callreport/get",
                data: function (d) {
                    d.fromDate = $("#call-report #from_date").val();
                    d.toDate = $("#call-report #to_date").val();
                },
            },
            columns: [
                {
                    data: "StartDate",
                    name: "StartDate",
                    type: "date",
                    searchable: false,
                },
                {
                    data: "EndDate",
                    name: "EndDate",
                    type: "date",
                    searchable: false,
                },
                {
                    data: "Sales",
                    name: "Sales",
                },
                {
                    data: "Tim",
                    name: "Tim",
                },
                {
                    data: "TargetCall",
                    name: "TargetCall",
                    searchable: false
                },
                {
                    data: "Actual",
                    name: "Actual",
                    searchable: false
                },
                {
                    data: "Duration",
                    name: "Duration",
                    searchable: false
                },
                {
                    data: "CheckIn",
                    name: "CheckIn",
                    searchable: false
                },
                {
                    data: "CheckOut",
                    name: "CheckOut",
                    searchable: false
                },
                {
                    data: "Omzet",
                    name: "Omzet",
                    searchable: false
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "CallReport"
                        );
                    },
                    action: exportDatatableHelper.newExportAction,
                    text: "Export",
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
            order: [0, "desc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }

    // Create element for DateRange Filter
    $("div.filter-call-report").html(`<div class="input-group">
                            <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                                readonly>
                            <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                                readonly>
                            <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                            <button type="button" name="refresh" id="refresh"
                                class="btn btn-sm btn-warning ml-2">Refresh</button>
                        </div>`);

    // Setting Awal Daterangepicker
    $("#call-report #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#call-report #to_date").daterangepicker({
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

        $("#call-report #to_date").daterangepicker({
            minDate: $("#call-report #from_date").val(),
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

        $("#call-report #from_date").daterangepicker({
            maxDate: $("#call-report #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#call-report .filter-call-report").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#call-report .filter-call-report").on("change", "#to_date", function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $("#call-report #from_date").val("");
    $("#call-report #to_date").val("");
    $("#call-report #from_date").attr("placeholder", "From Date");
    $("#call-report #to_date").attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#call-report #refresh").click(function () {
        $("#call-report #from_date").val("");
        $("#call-report #to_date").val("");
        $("#call-report .table-datatables").DataTable().search("");
        $("#call-report .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#call-report #filter").click(function () {
        $("#call-report .table-datatables").DataTable().ajax.reload();
    });
});
