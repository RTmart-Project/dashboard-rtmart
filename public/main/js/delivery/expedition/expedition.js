$(document).ready(function () {
    // DataTables
    dataTablesExpedition();

    function dataTablesExpedition() {
        $("#expedition .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-7'<'filter-expedition'>tl><'col-sm-12 col-md-1'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/delivery/expedition/get",
                data: function (d) {
                    d.fromDate = $("#expedition #from_date").val();
                    d.toDate = $("#expedition #to_date").val();
                },
            },
            columns: [
                {
                    data: "MerchantExpeditionID",
                    name: "expd.MerchantExpeditionID",
                },
                {
                    data: "DistributorName",
                    name: "ms_distributor.DistributorName",
                },
                {
                    data: "CreatedDate",
                    name: "expd.CreatedDate",
                    type: "data",
                },
                {
                    data: "CountDO",
                    name: "CountDO",
                    searchable: false,
                },
                {
                    data: "DriverName",
                    name: "DriverName",
                },
                {
                    name: "HelperName",
                    data: "HelperName",
                },
                {
                    data: "VehicleName",
                    name: "ms_vehicle.VehicleName",
                },
                {
                    data: "VehicleLicensePlate",
                    name: "expd.VehicleLicensePlate",
                },
                {
                    data: "StatusOrder",
                    name: "ms_status_order.StatusOrder",
                },
                {
                    data: "Detail",
                    name: "Detail",
                    orderable: false,
                    searchable: false,
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "DeliveryOrder"
                        );
                    },
                    action: exportDatatableHelper.newExportAction,
                    text: "Export",
                    titleAttr: "Excel",
                    className: "btn-sm",
                    exportOptions: {
                        modifier: {
                            page: "all",
                        },
                        columns: [0, 1, 2, 3, 4, 5, 6, 7],
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
    $("div.filter-expedition").html(`<div class="input-group">
                        <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                            readonly>
                        <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                            readonly>
                        <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                        <button type="button" name="refresh" id="refresh"
                            class="btn btn-sm btn-warning ml-2">Refresh</button>
                    </div>`);

    // Setting Awal Daterangepicker
    $("#expedition #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#expedition #to_date").daterangepicker({
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

        $("#expedition #to_date").daterangepicker({
            minDate: $("#expedition #from_date").val(),
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

        $("#expedition #from_date").daterangepicker({
            maxDate: $("#expedition #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#expedition .filter-expedition").on("change", "#from_date", function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $("#expedition .filter-expedition").on("change", "#to_date", function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $("#expedition #from_date").val("");
    $("#expedition #to_date").val("");
    $("#expedition #from_date").attr("placeholder", "From Date");
    $("#expedition #to_date").attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#expedition #refresh").click(function () {
        $("#expedition #from_date").val("");
        $("#expedition #to_date").val("");
        $("#expedition .table-datatables").DataTable().search("");
        // $('#expedition .select-filter-custom select').val('').change();
        // $('#expedition .select-filter-custom select option[value=]').attr('selected', 'selected');
        $("#expedition .table-datatables").DataTable().ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#expedition #filter").click(function () {
        $("#expedition .table-datatables").DataTable().ajax.reload();
    });
});
