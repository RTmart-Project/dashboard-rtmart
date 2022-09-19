$(document).ready(function () {
    let Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 4000,
    });

    const csrf = $('meta[name="csrf_token"]').attr("content");

    const d = new Date();

    const month = d.getMonth() + 1;
    const year = d.getFullYear();
    const startDateMonth = `${year}-${String(month).padStart(2, "0")}-01`;

    const dateNow = d.toISOString().split("T")[0];

    d.setDate(d.getDate() - 92);
    const dateMin = d.toISOString().split("T")[0];

    let filterBy = "";
    let distributorID = "";
    let salesCode = "";

    summaryMerchantData(
        startDateMonth,
        dateNow,
        filterBy,
        distributorID,
        salesCode
    );

    function summaryMerchantData(
        startDate,
        endDate,
        filterBy,
        distributorID,
        salesCode
    ) {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": csrf,
            },
        });
        $("#summary-merchant-table .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-7'<'filter-summary-merchant-table'>tl><'col-sm-12 col-md-4'f><'col-12 col-md-1 text-center'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/summary/merchant/data",
                type: "POST",
                data: {
                    startDate: startDate,
                    endDate: endDate,
                    filterBy: filterBy,
                    distributorID: distributorID,
                    salesCode: salesCode,
                },
            },
            columns: [
                {
                    data: "MerchantID",
                    name: "SummaryMerchant.MerchantID",
                },
                {
                    data: "StoreName",
                    name: "SummaryMerchant.StoreName",
                },
                {
                    data: "DistributorName",
                    name: "SummaryMerchant.DistributorName",
                },
                {
                    data: "Sales",
                    name: "Sales",
                },
                {
                    data: "TotalPO",
                    name: "SummaryMerchant.TotalPO",
                    searchable: false,
                },
                {
                    data: "TotalDO",
                    name: "SummaryMerchant.TotalDO",
                    searchable: false,
                },
                {
                    data: "GrossMargin",
                    name: "SummaryMerchant.GrossMargin",
                    searchable: false,
                },
                {
                    data: "PercentGrossMargin",
                    name: "SummaryMerchant.PercentGrossMargin",
                    searchable: false,
                },
                {
                    data: "NettMargin",
                    name: "SummaryMerchant.NettMargin",
                    searchable: false,
                },
                {
                    data: "PercentNettMargin",
                    name: "SummaryMerchant.PercentNettMargin",
                    searchable: false,
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "SummaryMerchant"
                        );
                    },
                    action: exportDatatableHelper.newExportAction,
                    text: "Export",
                    titleAttr: "Excel",
                    className: "btn-sm rounded",
                    exportOptions: {
                        modifier: {
                            page: "all",
                        },
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                        orthogonal: "export",
                    },
                },
            ],
            aoColumnDefs: [
                {
                    aTargets: [4, 5, 6, 8],
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
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }

    // Setting Awal Daterangepicker
    $("#summary-merchant #from_date").daterangepicker({
        minDate: dateMin,
        maxDate: dateNow,
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#summary-merchant #to_date").daterangepicker({
        minDate: dateMin,
        maxDate: dateNow,
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

        $("#summary-merchant #to_date").daterangepicker({
            minDate: $("#summary-merchant #from_date").val(),
            maxDate: dateNow,
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

        $("#summary-merchant #from_date").daterangepicker({
            minDate: dateMin,
            maxDate: $("#summary-merchant #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#summary-merchant").on("change", "#from_date", function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $("#summary-merchant").on("change", "#to_date", function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $("#summary-merchant #from_date").val(startDateMonth);
    $("#summary-merchant #to_date").val(dateNow);
    $("#summary-merchant #from_date").attr("placeholder", startDateMonth);
    $("#summary-merchant #to_date").attr("placeholder", dateNow);

    $("#filter-tanggal-po").on("click", function () {
        let startDate = $("#from_date").val();
        let endDate = $("#to_date").val();
        let filterBy = "DatePO";
        let distributorID = $("#distributor").val();
        let salesCode = $("#sales").val();

        $("#summary-merchant-table .table-datatables").DataTable().destroy();
        summaryMerchantData(
            startDate,
            endDate,
            filterBy,
            distributorID,
            salesCode
        );
    });

    $("#filter-tanggal-do").on("click", function () {
        let startDate = $("#from_date").val();
        let endDate = $("#to_date").val();
        let filterBy = "DateDO";
        let distributorID = $("#distributor").val();
        let salesCode = $("#sales").val();

        $("#summary-merchant-table .table-datatables").DataTable().destroy();
        summaryMerchantData(
            startDate,
            endDate,
            filterBy,
            distributorID,
            salesCode
        );
    });

    $("#refresh").on("click", function () {
        $("#from_date").val(startDateMonth);
        $("#to_date").val(dateNow);
        $("#distributor").val("");
        $("#distributor").selectpicker("refresh");
        $("#sales").val("");
        $("#sales").selectpicker("refresh");

        let startDate = $("#from_date").val();
        let endDate = $("#to_date").val();
        let distributorID = $("#distributor").val();
        let salesCode = $("#sales").val();

        $("#summary-merchant-table .table-datatables").DataTable().destroy();
        summaryMerchantData(
            startDate,
            endDate,
            filterBy,
            distributorID,
            salesCode
        );
    });
});
