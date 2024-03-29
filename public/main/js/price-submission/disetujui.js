$(document).ready(function () {
    // DataTables
    dataTablesPriceSubmission();

    function dataTablesPriceSubmission() {
        let roleID = $('meta[name="role-id"]').attr("content");
        $("#disetujui .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-disetujui'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/price-submission/get/S040",
                data: function (d) {
                    d.fromDate = $("#disetujui #from_date").val();
                    d.toDate = $("#disetujui #to_date").val();
                },
            },
            columns: [
                {
                    data: "StockOrderID",
                    name: "tmo.StockOrderID",
                },
                {
                    data: "DatePO",
                    name: "tmo.CreatedDate",
                    type: "date",
                },
                {
                    data: "ConfirmDate",
                    name: "ms_price_submission.ConfirmDate",
                    type: "date",
                },
                {
                    data: "DistributorName",
                    name: "ms_distributor.DistributorName",
                },
                {
                    data: "MerchantID",
                    name: "tmo.MerchantID",
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
                    data: "TotalPrice",
                    name: "tmo.TotalPrice",
                },
                {
                    data: "EstMarginTotalPrice",
                    name: "EstMarginTotalPrice",
                    searchable: false,
                },
                {
                    data: "EstPercentMarginTotalPrice",
                    name: "EstPercentMarginTotalPrice",
                    searchable: false,
                },
                {
                    data: "TotalTrxSubmission",
                    name: "TotalTrxSubmission",
                    searchable: false,
                },
                {
                    data: "EstMarginTotalTrxSubmission",
                    name: "EstMarginTotalTrxSubmission",
                    searchable: false,
                },
                {
                    data: "EstPercentMarginTotalTrxSubmission",
                    name: "EstPercentMarginTotalTrxSubmission",
                    searchable: false,
                },
                {
                    data: "PotonganBunga",
                    name: "PotonganBunga",
                    searchable: false,
                },
                {
                    data: "CostLogistic",
                    name: "CostLogistic",
                    searchable: false,
                },
                {
                    data: "FinalEstMarginSubmission",
                    name: "FinalEstMarginSubmission",
                    searchable: false,
                },
                {
                    data: "PercentFinalEstMarginSubmission",
                    name: "PercentFinalEstMarginSubmission",
                    searchable: false,
                },
                {
                    data: "CreatedBy",
                    name: "ms_price_submission.CreatedBy",
                },
                {
                    data: "Note",
                    name: "ms_price_submission.Note",
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
                            "Pengajuan Harga Disetujui"
                        );
                    },
                    action: exportDatatableHelper.newExportAction,
                    className: "btn-sm",
                    text: "Export",
                    titleAttr: "Excel",
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
                    aTargets: [7, 8, 10, 11, 13, 14, 15],
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
    $("div.filter-disetujui").html(`<div class="input-group">
                        <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                            readonly>
                        <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                            readonly>
                        <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                        <button type="button" name="refresh" id="refresh"
                            class="btn btn-sm btn-warning ml-2">Refresh</button>
                    </div>`);

    // Setting Awal Daterangepicker
    $("#disetujui #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#disetujui #to_date").daterangepicker({
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

        $("#disetujui #to_date").daterangepicker({
            minDate: $("#disetujui #from_date").val(),
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

        $("#disetujui #from_date").daterangepicker({
            maxDate: $("#disetujui #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#disetujui .filter-disetujui").on("change", "#from_date", function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $("#disetujui .filter-disetujui").on("change", "#to_date", function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $("#disetujui #from_date").val("");
    $("#disetujui #to_date").val("");
    $("#disetujui #from_date").attr("placeholder", "From Date");
    $("#disetujui #to_date").attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#disetujui #refresh").click(function () {
        $("#disetujui #from_date").val("");
        $("#disetujui #to_date").val("");
        $("#disetujui .table-datatables").DataTable().search("");
        $("#disetujui .table-datatables").DataTable().ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#disetujui #filter").click(function () {
        $("#disetujui .table-datatables").DataTable().ajax.reload();
    });
});
