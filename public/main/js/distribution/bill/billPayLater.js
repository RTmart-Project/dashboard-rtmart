$(document).ready(function () {
    // DataTables
    dataTablesBillPayLater();

    function dataTablesBillPayLater() {
        $("#bill-paylater .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-bill-paylater'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/distribution/bill/get",
                data: function (d) {
                    d.fromDate = $("#bill-paylater #from_date").val();
                    d.toDate = $("#bill-paylater #to_date").val();
                    d.filterIsPaid = $(
                        "#bill-paylater .filter-isPaid select"
                    ).val();
                },
            },
            columns: [
                {
                    data: "DeliveryOrderID",
                    name: "tx_merchant_delivery_order.DeliveryOrderID",
                },
                {
                    data: "StockOrderID",
                    name: "tx_merchant_delivery_order.StockOrderID",
                },
                {
                    data: "StoreName",
                    name: "ms_merchant_account.StoreName",
                },
                {
                    data: "PhoneNumber",
                    name: "ms_merchant_account.PhoneNumber",
                },
                {
                    data: "CreatedDate",
                    name: "tx_merchant_delivery_order.CreatedDate",
                },
                {
                    data: "FinishDate",
                    name: "tx_merchant_delivery_order.FinishDate",
                },
                {
                    data: "DueDate",
                    name: "DueDate",
                },
                {
                    data: "RemainingDay",
                    name: "RemainingDay",
                },
                {
                    data: "StatusOrder",
                    name: "ms_status_order.StatusOrder",
                },
                {
                    data: "IsPaid",
                    name: "tx_merchant_delivery_order.IsPaid",
                },
                {
                    data: "PaymentDate",
                    name: "tx_merchant_delivery_order.PaymentDate",
                },
                {
                    data: "PaymentNominal",
                    name: "tx_merchant_delivery_order.PaymentNominal",
                },
                {
                    data: "PaymentSlip",
                    name: "tx_merchant_delivery_order.PaymentSlip",
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
                            "BillPayLaterRTmart"
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
            order: [4, "desc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
            aoColumnDefs: [
                {
                    aTargets: [11],
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
        });
    }

    // Create element for DateRange Filter
    $("div.filter-bill-paylater").html(`<div class="input-group">
                    <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" readonly>
                    <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm" readonly>
                    <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                    <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-2">Refresh</button>
                    <div class="filter-isPaid ml-2">
                        <select class="form-control form-control-sm">
                            <option selected disabled hidden>Status Pelunasan</option>
                            <option value="">Semua</option>
                            <option value="paid">Sudah Lunas</option>
                            <option value="unpaid">Belum Lunas</option>
                        </select>
                    </div>
                  </div>`);

    // Setting Awal Daterangepicker
    $("#bill-paylater #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#bill-paylater #to_date").daterangepicker({
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

        $("#bill-paylater #to_date").daterangepicker({
            minDate: $("#bill-paylater #from_date").val(),
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

        $("#bill-paylater #from_date").daterangepicker({
            maxDate: $("#bill-paylater #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#bill-paylater .filter-bill-paylater").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#bill-paylater .filter-bill-paylater").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    // Menyisipkan Placeholder Date
    $("#bill-paylater #from_date").val("");
    $("#bill-paylater #to_date").val("");
    $("#bill-paylater #from_date").attr("placeholder", "From Date");
    $("#bill-paylater #to_date").attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#bill-paylater #refresh").click(function () {
        $("#bill-paylater #from_date").val("");
        $("#bill-paylater #to_date").val("");
        $("#bill-paylater .table-datatables").DataTable().search("");
        // $('#bill-paylater .select-filter-custom select').val('').change();
        // $('#bill-paylater .select-filter-custom select option[value=]').attr('selected', 'selected');
        $("#bill-paylater .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#bill-paylater #filter").click(function () {
        $("#bill-paylater .table-datatables").DataTable().ajax.reload();
    });

    $("#bill-paylater .filter-isPaid select").change(function () {
        $("#bill-paylater .table-datatables").DataTable().ajax.reload();
    });
});
