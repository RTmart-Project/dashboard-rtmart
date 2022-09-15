$(document).ready(function () {
    // DataTables
    dataTablesPriceSubmission();

    function dataTablesPriceSubmission() {
        let roleID = $('meta[name="role-id"]').attr("content");
        $("#ditolak .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-ditolak'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/price-submission/get/S041",
                data: function (d) {
                    d.fromDate = $("#ditolak #from_date").val();
                    d.toDate = $("#ditolak #to_date").val();
                },
            },
            columns: [
                {
                    data: "StockOrderID",
                    name: "tx_merchant_order.StockOrderID",
                },
                {
                    data: "DatePO",
                    name: "tx_merchant_order.CreatedDate",
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
                    data: "TotalPrice",
                    name: "tx_merchant_order.TotalPrice",
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
                            "Pengajuan Harga Ditolak"
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
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13],
                        orthogonal: "export",
                    },
                },
            ],
            aoColumnDefs: [
                {
                    aTargets: [6, 7, 9, 10],
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
    $("div.filter-ditolak").html(`<div class="input-group">
                        <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                            readonly>
                        <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                            readonly>
                        <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                        <button type="button" name="refresh" id="refresh"
                            class="btn btn-sm btn-warning ml-2">Refresh</button>
                    </div>`);

    // Setting Awal Daterangepicker
    $("#ditolak #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#ditolak #to_date").daterangepicker({
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

        $("#ditolak #to_date").daterangepicker({
            minDate: $("#ditolak #from_date").val(),
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

        $("#ditolak #from_date").daterangepicker({
            maxDate: $("#ditolak #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#ditolak .filter-ditolak").on("change", "#from_date", function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $("#ditolak .filter-ditolak").on("change", "#to_date", function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $("#ditolak #from_date").val("");
    $("#ditolak #to_date").val("");
    $("#ditolak #from_date").attr("placeholder", "From Date");
    $("#ditolak #to_date").attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#ditolak #refresh").click(function () {
        $("#ditolak #from_date").val("");
        $("#ditolak #to_date").val("");
        $("#ditolak .table-datatables").DataTable().search("");
        $("#ditolak .table-datatables").DataTable().ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#ditolak #filter").click(function () {
        $("#ditolak .table-datatables").DataTable().ajax.reload();
    });
});
