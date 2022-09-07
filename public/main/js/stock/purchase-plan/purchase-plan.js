$(document).ready(function () {
    // DataTables
    dataTablesPurchasePlan();

    function dataTablesPurchasePlan() {
        $("#purchase-plan .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-6'<'filter-purchase-plan'>tl><'col-sm-12 col-md-5'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            ajax: {
                url: "/stock/plan-purchase",
                data: function (d) {
                    d.fromDate = $("#purchase-plan #from_date").val();
                    d.toDate = $("#purchase-plan #to_date").val();
                },
            },
            columns: [
                {
                    data: "PurchasePlanID",
                    name: "ms_purchase_plan.PurchasePlanID",
                },
                {
                    data: "InvestorName",
                    name: "ms_investor.InvestorName",
                },
                {
                    data: "PlanDate",
                    name: "ms_purchase_plan.PlanDate",
                    type: "date",
                },
                {
                    data: "CreatedBy",
                    name: "ms_purchase_plan.CreatedBy",
                },
                {
                    data: "StatusName",
                    name: "ms_status_stock.StatusName",
                },
                {
                    data: "ConfirmBy",
                    name: "ms_purchase_plan.ConfirmBy",
                },
                {
                    data: "ConfirmDate",
                    name: "ms_purchase_plan.ConfirmDate",
                    type: "date",
                },
                {
                    data: "Detail",
                    name: "Detail",
                    searchable: false,
                    orderable: false,
                },
                {
                    data: "Action",
                    name: "Action",
                    searchable: false,
                    orderable: false,
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "PurchasePlan"
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
            order: [2, "desc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }

    // Create element for DateRange Filter
    $("div.filter-purchase-plan").html(`<div class="input-group">
                      <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" readonly>
                      <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm" readonly>
                      <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                      <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-2">Refresh</button>
                    </div>`);

    // Setting Awal Daterangepicker
    $("#purchase-plan #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#purchase-plan #to_date").daterangepicker({
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

        $("#purchase-plan #to_date").daterangepicker({
            minDate: $("#purchase-plan #from_date").val(),
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

        $("#purchase-plan #from_date").daterangepicker({
            maxDate: $("#purchase-plan #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#purchase-plan .filter-purchase-plan").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#purchase-plan .filter-purchase-plan").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    // Menyisipkan Placeholder Date
    $("#purchase-plan #from_date").val("");
    $("#purchase-plan #to_date").val("");
    $("#purchase-plan #from_date").attr("placeholder", "From Date");
    $("#purchase-plan #to_date").attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#purchase-plan #refresh").click(function () {
        $("#purchase-plan #from_date").val("");
        $("#purchase-plan #to_date").val("");
        $("#purchase-plan .table-datatables").DataTable().search("");
        $("#purchase-plan .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#purchase-plan #filter").click(function () {
        $("#purchase-plan .table-datatables").DataTable().ajax.reload();
    });

    $("#purchase-plan .filter-tipe select").change(function () {
        $("#purchase-plan .table-datatables").DataTable().ajax.reload();
    });
});
