$(document).ready(function () {
    const csrf = $('meta[name="csrf_token"]').attr("content");

    const d = new Date();

    const month = d.getMonth() + 1;
    const year = d.getFullYear();
    const startDateMonth = `${year}-${String(month).padStart(2, "0")}-01`;

    const dateNow = d.toISOString().split("T")[0];
    const dayNameNow = new Date(dateNow).toLocaleString("en-us", {
        weekday: "long",
    });

    callPlanData([dayNameNow], startDateMonth, dateNow);
    $("#call-plan #visit_day").val([dayNameNow]);

    function callPlanData(visitDayName, startDate, endDate) {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": csrf,
            },
        });
        $("#call-plan-table .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-7'<'filter-call-plan-table'>tl><'col-sm-12 col-md-4'f><'col-12 col-md-1 text-center'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/rtsales/callplan",
                type: "POST",
                data: {
                    visitDayName: visitDayName,
                    startDate: startDate,
                    endDate: endDate,
                },
            },
            columns: [
                {
                    data: "VisitDayName",
                    name: "ms_visit_plan.VisitDayName",
                },
                {
                    data: "Sales",
                    name: "Sales",
                },
                {
                    data: "StoreID",
                    name: "ms_visit_plan.StoreID",
                },
                {
                    data: "MerchantID",
                    name: "ms_store.MerchantID",
                },
                {
                    data: "StoreName",
                    name: "ms_store.StoreName",
                },
                {
                    data: "Partners",
                    name: "ms_partner.Name",
                    searchable: false,
                    orderable: false,
                },
                {
                    data: "Grade",
                    name: "ms_store.Grade",
                },
                {
                    data: "Latitude",
                    name: "ms_store.Latitude",
                },
                {
                    data: "Longitude",
                    name: "ms_store.Longitude",
                },
                {
                    data: "StoreAddress",
                    name: "ms_store.StoreAddress",
                },
                {
                    data: "PhoneNumber",
                    name: "ms_store.PhoneNumber",
                },
                {
                    data: "StoreType",
                    name: "ms_store.StoreType",
                },
                {
                    data: "Sorting",
                    name: "ms_visit_plan.Sorting",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "Distance",
                    name: "ms_visit_plan_sort.Distance",
                },
                {
                    data: "TotalPO",
                    name: "TotalPO",
                    searchable: false,
                },
                {
                    data: "TotalDO",
                    name: "TotalDO",
                    searchable: false,
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "CallPlan"
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
                        columns: [
                            0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14,
                            15,
                        ],
                        orthogonal: "export",
                    },
                },
            ],
            aoColumnDefs: [
                {
                    aTargets: [14, 15],
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
            // order: [12, "desc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }

    // Setting Awal Daterangepicker
    $("#call-plan #from_date").daterangepicker({
        maxDate: dateNow,
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#call-plan #to_date").daterangepicker({
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

        $("#call-plan #to_date").daterangepicker({
            minDate: $("#call-plan #from_date").val(),
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

        $("#call-plan #from_date").daterangepicker({
            maxDate: $("#call-plan #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#call-plan").on("change", "#from_date", function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $("#call-plan").on("change", "#to_date", function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $("#call-plan #from_date").val(startDateMonth);
    $("#call-plan #to_date").val(dateNow);
    $("#call-plan #from_date").attr("placeholder", startDateMonth);
    $("#call-plan #to_date").attr("placeholder", dateNow);

    $("#filter").on("click", function () {
        const visitDay = $("#visit_day").val();
        const startDate = $("#from_date").val();
        const endDate = $("#to_date").val();

        $("#call-plan-table .table-datatables").DataTable().destroy();
        callPlanData(visitDay, startDate, endDate);
    });

    $("#refresh").on("click", function () {
        $("#visit_day").val([dayNameNow]);
        $("#visit_day").selectpicker("refresh");
        $("#from_date").val(startDateMonth);
        $("#to_date").val(dateNow);

        $("#call-plan-table .table-datatables").DataTable().destroy();
        callPlanData([dayNameNow], startDateMonth, dateNow);

        // Setting Awal Daterangepicker
        $("#call-plan #from_date").daterangepicker({
            maxDate: dateNow,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });

        // Setting Awal Daterangepicker
        $("#call-plan #to_date").daterangepicker({
            maxDate: dateNow,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
    });
});
