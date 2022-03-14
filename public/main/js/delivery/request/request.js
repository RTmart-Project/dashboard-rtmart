$(document).ready(function () {
    // DataTables
    dataTablesDeliveryRequest();

    function dataTablesDeliveryRequest() {
        $("#delivery-request .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-delivery-request'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/delivery/request/get",
                data: function (d) {
                    d.fromDate = $("#delivery-request #from_date").val();
                    d.toDate = $("#delivery-request #to_date").val();
                },
            },
            columns: [
                {
                    data: "Empty",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "Checkbox",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "DeliveryOrderID",
                    name: "tx_merchant_delivery_order.DeliveryOrderID",
                },
                {
                    data: "Area",
                    name: "Area",
                },
                {
                    data: "CreatedDate",
                    name: "tx_merchant_delivery_order.CreatedDate",
                    type: "date",
                },
                {
                    data: "Products",
                    name: "Products",
                    searchable: false,
                },
                {
                    data: "DistributorName",
                    name: "DistributorName",
                },
                {
                    data: "MerchantID",
                    name: "MerchantID",
                },
                {
                    data: "StoreName",
                    name: "StoreName",
                },
                {
                    data: "Sales",
                    name: "Sales",
                },
                {
                    data: "PhoneNumber",
                    name: "PhoneNumber",
                },
                {
                    data: "Grade",
                    name: "Grade",
                },
                {
                    data: "Partner",
                    name: "Partner",
                },
                {
                    data: "StoreAddress",
                    name: "StoreAddress",
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "DeliveryRequest"
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
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
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
    $("div.filter-delivery-request").html(`<div class="input-group">
                          <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" readonly>
                          <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm" readonly>
                          <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                          <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-2">Refresh</button>
                      </div>`);

    // Setting Awal Daterangepicker
    $("#delivery-request #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#delivery-request #to_date").daterangepicker({
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

        $("#delivery-request #to_date").daterangepicker({
            minDate: $("#delivery-request #from_date").val(),
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

        $("#delivery-request #from_date").daterangepicker({
            maxDate: $("#delivery-request #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#delivery-request .filter-delivery-request").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#delivery-request .filter-delivery-request").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    // Menyisipkan Placeholder Date
    $("#delivery-request #from_date").val("");
    $("#delivery-request #to_date").val("");
    $("#delivery-request #from_date").attr("placeholder", "From Date");
    $("#delivery-request #to_date").attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#delivery-request #refresh").click(function () {
        $("#delivery-request #from_date").val("");
        $("#delivery-request #to_date").val("");
        $("#delivery-request .table-datatables").DataTable().search("");
        $("#delivery-request .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#delivery-request #filter").click(function () {
        $("#delivery-request .table-datatables").DataTable().ajax.reload();
    });
});
