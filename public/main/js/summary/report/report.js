$(document).ready(function () {
    const csrf = $('meta[name="csrf_token"]').attr("content");

    const d = new Date();

    const dateNow = d.toISOString().split("T")[0];

    d.setDate(d.getDate() - 92);
    const dateMin = d.toISOString().split("T")[0];

    let distributorID = "";
    let salesCode = "";
    summaryReportData(dateNow, dateNow, distributorID, salesCode);

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
    $("#summary-report #from_date").val(dateNow);
    $("#summary-report #to_date").val(dateNow);
    $("#summary-report #from_date").attr("placeholder", dateNow);
    $("#summary-report #to_date").attr("placeholder", dateNow);

    $("#refresh").on("click", function () {
        $("#from_date").val(dateNow);
        $("#to_date").val(dateNow);
        $("#distributor").val("");
        $("#distributor").selectpicker("refresh");
        $("#sales").val("");
        $("#sales").selectpicker("refresh");

        let startDate = $("#from_date").val();
        let endDate = $("#to_date").val();
        let distributorID = $("#distributor").val();
        let salesCode = $("#sales").val();
        summaryReportData(startDate, endDate, distributorID, salesCode);
    });

    $("#filter").on("click", function () {
        let startDate = $("#from_date").val();
        let endDate = $("#to_date").val();
        let distributorID = $("#distributor").val();
        let salesCode = $("#sales").val();
        // console.log(startDate, endDate, distributorID, salesCode);
        summaryReportData(startDate, endDate, distributorID, salesCode);
    });

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
            success: function (result) {
                console.log(result);
            },
        });
    }
});
