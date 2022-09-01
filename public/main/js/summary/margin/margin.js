$(document).ready(function () {
    // DataTables
    dataTablesSummaryMargin();

    function dataTablesSummaryMargin() {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf_token"]').attr("content"),
            },
        });
        $("#summary-margin .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-summary-margin'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            ajax: {
                url: "/summary/margin/data",
                method: "POST",
                data: function (d) {
                    d.fromDate = $("#summary-margin #from_date").val();
                    d.toDate = $("#summary-margin #to_date").val();
                },
            },
            columns: [
                {
                    data: "DistributorName",
                    name: "ms_distributor.DistributorName",
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
                        columns: [0],
                        orthogonal: "export",
                    },
                },
            ],
            // aoColumnDefs: [
            //     {
            //         aTargets: [6],
            //         mRender: function (data, type, full) {
            //             if (type === "export") {
            //                 return data;
            //             } else {
            //                 if (data == null || data == "") {
            //                     return data;
            //                 } else {
            //                     const currencySeperatorFormat =
            //                         thousands_separators(data);
            //                     return currencySeperatorFormat;
            //                 }
            //             }
            //         },
            //     },
            // ],
            // order: [5, "desc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }

    // Create element for DateRange Filter
    $("div.filter-summary-margin").html(`<div class="input-group">
                      <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" readonly>
                      <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm" readonly>
                      <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                      <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-2">Refresh</button>
                    </div>`);

    // Setting Awal Daterangepicker
    $("#summary-margin #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#summary-margin #to_date").daterangepicker({
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

        $("#summary-margin #to_date").daterangepicker({
            minDate: $("#summary-margin #from_date").val(),
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

        $("#summary-margin #from_date").daterangepicker({
            maxDate: $("#summary-margin #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#summary-margin .filter-summary-margin").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#summary-margin .filter-summary-margin").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    const d = new Date();

    const firstDate = new Date(d.getFullYear(), d.getMonth(), 01)
        .toISOString()
        .split("T")[0];
    const dateNow = d.toISOString().split("T")[0];

    // Menyisipkan Placeholder Date
    $("#summary-margin #from_date").val("");
    $("#summary-margin #to_date").val("");
    $("#summary-margin #from_date").attr("placeholder", firstDate);
    $("#summary-margin #to_date").attr("placeholder", dateNow);

    // Event Listener saat tombol refresh diklik
    $("#summary-margin #refresh").click(function () {
        $("#summary-margin #from_date").val("");
        $("#summary-margin #to_date").val("");
        $("#summary-margin .table-datatables").DataTable().search("");
        $("#summary-margin .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#summary-margin #filter").click(function () {
        $("#summary-margin .table-datatables").DataTable().ajax.reload();
    });
});
