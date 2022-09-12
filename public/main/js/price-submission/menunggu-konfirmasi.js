$(document).ready(function () {
    // DataTables
    dataTablesPriceSubmission();

    function dataTablesPriceSubmission() {
        let roleID = $('meta[name="role-id"]').attr("content");
        $("#menunggu-konfirmasi .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-menunggu-konfirmasi'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/price-submission/get/S039",
                data: function (d) {
                    d.fromDate = $("#menunggu-konfirmasi #from_date").val();
                    d.toDate = $("#menunggu-konfirmasi #to_date").val();
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
                    data: "Detail",
                    name: "Detail",
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
                            "Pengajuan Harga Menunggu Konfirmasi"
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
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
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
                {
                    aTargets: [10],
                    visible: roleID == "CEO" || roleID == "IT" ? true : false,
                },
            ],
            order: [1, "desc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }

    // Create element for DateRange Filter
    $("div.filter-menunggu-konfirmasi").html(`<div class="input-group">
                          <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                              readonly>
                          <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                              readonly>
                          <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                          <button type="button" name="refresh" id="refresh"
                              class="btn btn-sm btn-warning ml-2">Refresh</button>
                      </div>`);

    // Setting Awal Daterangepicker
    $("#menunggu-konfirmasi #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#menunggu-konfirmasi #to_date").daterangepicker({
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

        $("#menunggu-konfirmasi #to_date").daterangepicker({
            minDate: $("#menunggu-konfirmasi #from_date").val(),
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

        $("#menunggu-konfirmasi #from_date").daterangepicker({
            maxDate: $("#menunggu-konfirmasi #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#menunggu-konfirmasi .filter-menunggu-konfirmasi").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#menunggu-konfirmasi .filter-menunggu-konfirmasi").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    // Menyisipkan Placeholder Date
    $("#menunggu-konfirmasi #from_date").val("");
    $("#menunggu-konfirmasi #to_date").val("");
    $("#menunggu-konfirmasi #from_date").attr("placeholder", "From Date");
    $("#menunggu-konfirmasi #to_date").attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#menunggu-konfirmasi #refresh").click(function () {
        $("#menunggu-konfirmasi #from_date").val("");
        $("#menunggu-konfirmasi #to_date").val("");
        $("#menunggu-konfirmasi .table-datatables").DataTable().search("");
        $("#menunggu-konfirmasi .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#menunggu-konfirmasi #filter").click(function () {
        $("#menunggu-konfirmasi .table-datatables").DataTable().ajax.reload();
    });

    $("#menunggu-konfirmasi table").on("click", ".btn-approve", function (e) {
        e.preventDefault();
        const priceSubmissionID = $(this).data("price-submission-id");
        const stockOrderID = $(this).data("stock-order-id");
        $.confirm({
            title: "Setujui Pengajuan!",
            content: `Yakin ingin menyetujui pengajuan <b>${stockOrderID}</b> ?`,
            closeIcon: true,
            buttons: {
                Yakin: {
                    btnClass: "btn-success",
                    draggable: true,
                    dragWindowGap: 0,
                    action: function () {
                        window.location = `/price-submission/confirm/${priceSubmissionID}/approve`;
                    },
                },
                tidak: function () {},
            },
        });
    });

    $("#menunggu-konfirmasi table").on("click", ".btn-reject", function (e) {
        e.preventDefault();
        const priceSubmissionID = $(this).data("price-submission-id");
        const stockOrderID = $(this).data("stock-order-id");
        $.confirm({
            title: "Tolak Pengajuan!",
            content: `Yakin ingin menolak pengajuan <b>${stockOrderID}</b> ?`,
            closeIcon: true,
            buttons: {
                Yakin: {
                    btnClass: "btn-red",
                    draggable: true,
                    dragWindowGap: 0,
                    action: function () {
                        window.location = `/price-submission/confirm/${priceSubmissionID}/reject`;
                    },
                },
                tidak: function () {},
            },
        });
    });
});
