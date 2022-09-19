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

    let distributorID = "";
    let salesCode = "";
    summaryReportData(startDateMonth, dateNow, distributorID, salesCode);

    // Setting Awal Daterangepicker
    $("#summary-report #from_date").daterangepicker({
        minDate: dateMin,
        maxDate: dateNow,
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#summary-report #to_date").daterangepicker({
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

        $("#summary-report #to_date").daterangepicker({
            minDate: $("#summary-report #from_date").val(),
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

        $("#summary-report #from_date").daterangepicker({
            minDate: dateMin,
            maxDate: $("#summary-report #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#summary-report").on("change", "#from_date", function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $("#summary-report").on("change", "#to_date", function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $("#summary-report #from_date").val(startDateMonth);
    $("#summary-report #to_date").val(dateNow);
    $("#summary-report #from_date").attr("placeholder", startDateMonth);
    $("#summary-report #to_date").attr("placeholder", dateNow);

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
        $(".overlay").removeClass("d-none");
        summaryReportData(startDate, endDate, distributorID, salesCode);
    });

    $("#filter").on("click", function () {
        let startDate = $("#from_date").val();
        let endDate = $("#to_date").val();
        let distributorID = $("#distributor").val();
        let salesCode = $("#sales").val();

        const today = new Date().toJSON().slice(0, 10).replace(/-/g, "-");

        const date1 = new Date(startDate);
        const date2 = new Date(endDate);
        const dateDiff =
            (date2.getTime() - date1.getTime()) / (1000 * 3600 * 24);

        if (dateDiff > 92) {
            Toast.fire({
                icon: "error",
                title: " Rentang filter tanggal maksimal 92 hari!",
            });
            return false;
        }
        $(".overlay").removeClass("d-none");

        summaryReportData(startDate, endDate, distributorID, salesCode);
    });

    function createLink(type, startDate, endDate, distributorID, salesCode) {
        return `/summary/reportDetail/${type}?startDate=${startDate}&endDate=${endDate}&distributorID=${distributorID}&salesCode=${salesCode}`;
    }

    function summaryReportData(startDate, endDate, distributorID, salesCode) {
        $.ajax({
            url: "/summary/report/data",
            headers: {
                "X-CSRF-TOKEN": csrf,
            },
            data: {
                startDate,
                endDate,
                distributorID,
                salesCode,
            },
            type: "post",
            success: function (res) {
                // Link PO Summary
                const linkTotalValuePO = createLink(
                    "totalValuePO",
                    startDate,
                    endDate,
                    distributorID,
                    salesCode
                );
                $("#total-value-po-link").prop("href", linkTotalValuePO);

                const linkCountPO = createLink(
                    "countPO",
                    startDate,
                    endDate,
                    distributorID,
                    salesCode
                );
                $("#count-total-po-link").prop("href", linkCountPO);

                const linkCountMerchantPO = createLink(
                    "countMerchantPO",
                    startDate,
                    endDate,
                    distributorID,
                    salesCode
                );
                $("#count-merchant-po-link").prop("href", linkCountMerchantPO);

                // Link DO Summary
                const linkTotalValueDO = createLink(
                    "totalValueDO",
                    startDate,
                    endDate,
                    distributorID,
                    salesCode
                );
                $("#total-value-do-link").prop("href", linkTotalValueDO);

                const linkCountDO = createLink(
                    "countDO",
                    startDate,
                    endDate,
                    distributorID,
                    salesCode
                );
                $("#count-total-do-link").prop("href", linkCountDO);

                const linkCountMerchantDO = createLink(
                    "countMerchantDO",
                    startDate,
                    endDate,
                    distributorID,
                    salesCode
                );
                $("#count-merchant-do-link").prop("href", linkCountMerchantDO);

                // PO Summary
                $("#total-value-po").html(
                    res.PO.TotalValuePO != null
                        ? "Rp " + thousands_separators(res.PO.TotalValuePO)
                        : 0
                );
                $("#count-total-po").html(
                    res.PO.CountTotalPO != null ? res.PO.CountTotalPO : 0
                );
                $("#count-merchant-po").html(
                    res.PO.CountMerchantPO != null ? res.PO.CountMerchantPO : 0
                );
                $("#value-margin-estimasi").html(
                    `${
                        res.PO.ValueMargin != null &&
                        res.PO.PercentMarginEstimasiBeforeDisc != null
                            ? "Rp " +
                              thousands_separators(res.PO.ValueMargin) +
                              " (" +
                              res.PO.PercentMarginEstimasiBeforeDisc +
                              "%)"
                            : 0
                    }`
                );
                $("#voucher-po").html(
                    res.PO.VoucherPO != null
                        ? thousands_separators(res.PO.VoucherPO)
                        : 0
                );
                $("#margin-estimasi").html(
                    `${
                        res.PO.ValueMarginEstimasi != null &&
                        res.PO.PercentMarginEstimasi != null
                            ? "Rp " +
                              thousands_separators(res.PO.ValueMarginEstimasi) +
                              " (" +
                              res.PO.PercentMarginEstimasi +
                              "%)"
                            : 0
                    }`
                );

                // DO Summary
                $("#total-value-do").html(
                    res.DO.TotalValueDO != null
                        ? "Rp " + thousands_separators(res.DO.TotalValueDO)
                        : 0
                );
                $("#count-total-do").html(
                    res.DO.CountTotalDO != null ? res.DO.CountTotalDO : 0
                );
                $("#count-merchant-do").html(
                    res.DO.CountMerchantDO != null ? res.DO.CountMerchantDO : 0
                );
                $("#value-margin").html(
                    `${
                        res.DO.ValueMargin != null &&
                        res.DO.PercentMarginRealBeforeDisc != null
                            ? "Rp " +
                              thousands_separators(res.DO.ValueMargin) +
                              " (" +
                              res.DO.PercentMarginRealBeforeDisc +
                              "%)"
                            : 0
                    }`
                );
                $("#voucher-do").html(
                    res.DO.VoucherDO != null
                        ? thousands_separators(res.DO.VoucherDO)
                        : 0
                );
                $("#margin-real").html(
                    `${
                        res.DO.ValueMarginReal != null &&
                        res.DO.PercentMarginReal != null
                            ? "Rp " +
                              thousands_separators(res.DO.ValueMarginReal) +
                              " (" +
                              res.DO.PercentMarginReal +
                              "%)"
                            : 0
                    }`
                );
                $(".overlay").addClass("d-none");
            },
        });
    }
});
