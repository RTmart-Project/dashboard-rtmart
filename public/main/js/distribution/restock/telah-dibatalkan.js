$(document).ready(function () {
    // DataTables
    dataTablesTelahDibatalkan();

    function dataTablesTelahDibatalkan() {
        $("#telah-dibatalkan .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-telah-dibatalkan'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/distribution/restock/get/S011",
                data: function (d) {
                    d.fromDate = $("#telah-dibatalkan #from_date").val();
                    d.toDate = $("#telah-dibatalkan #to_date").val();
                    d.distributorId = $("#telah-dibatalkan .distributor-select select").val();
                    d.paymentMethodId = $("#telah-dibatalkan .payment-method select").val();
                },
            },
            columns: [
                {
                    data: "StockOrderID",
                    name: "tx_merchant_order.StockOrderID",
                },
                {
                    data: "CreatedDate",
                    name: "tx_merchant_order.CreatedDate",
                    type: "date",
                },
                {
                    data: "DistributorName",
                    name: "ms_distributor.DistributorName",
                },
                {
                    data: "IsValid",
                    name: "tx_merchant_order.IsValid",
                },
                {
                    data: "ValidationNotes",
                    name: "tx_merchant_order.ValidationNotes",
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
                    data: "Grade",
                    name: "ms_distributor_grade.Grade",
                },
                {
                    data: "Sales",
                    name: "Sales",
                },
                {
                    data: "Partner",
                    name: "ms_merchant_account.Partner",
                },
                {
                    data: "TotalTrx",
                    name: "TotalTrx",
                },
                {
                    data: "PaymentMethodName",
                    name: "ms_payment_method.PaymentMethodName",
                },
                {
                    data: "PhoneNumber",
                    name: "ms_merchant_account.PhoneNumber",
                },
                {
                    data: "StoreAddress",
                    name: "ms_merchant_account.StoreAddress",
                },
                {
                    data: "CancelReasonNote",
                    name: "tx_merchant_order.CancelReasonNote",
                },
                {
                    data: "Action",
                    name: "Action",
                    orderable: false,
                    searchable: false,
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "RestockTelahDibatalkan"
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
                        columns: [
                            0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14,
                        ],
                        orthogonal: "export",
                    },
                },
            ],
            aoColumnDefs: [
                {
                    aTargets: [10],
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
    $("div.filter-telah-dibatalkan").html(`
        <div class="input-group">
            <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" readonly>
            <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm" readonly>
            <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
            <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-2">Refresh</button>
            ${selectElement}
            <div class="select-filter-custom ml-2 payment-method">
                <select>
                    <option value="">All</option>
                </select>
            </div>
        </div>
    `);

    // Setting Awal Daterangepicker
    $("#telah-dibatalkan #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#telah-dibatalkan #to_date").daterangepicker({
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

        $("#telah-dibatalkan #to_date").daterangepicker({
            minDate: $("#telah-dibatalkan #from_date").val(),
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

        $("#telah-dibatalkan #from_date").daterangepicker({
            maxDate: $("#telah-dibatalkan #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#telah-dibatalkan .filter-telah-dibatalkan").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#telah-dibatalkan .filter-telah-dibatalkan").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    // Menyisipkan Placeholder Date
    $("#telah-dibatalkan #from_date").val("");
    $("#telah-dibatalkan #to_date").val("");
    $("#telah-dibatalkan #from_date").attr("placeholder", "From Date");
    $("#telah-dibatalkan #to_date").attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#telah-dibatalkan #refresh").click(function () {
        $("#telah-dibatalkan #from_date").val("");
        $("#telah-dibatalkan #to_date").val("");
        $("#telah-dibatalkan .table-datatables").DataTable().search("");
        $("#telah-dibatalkan .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#telah-dibatalkan #filter").click(function () {
        $("#telah-dibatalkan .table-datatables").DataTable().ajax.reload();
    });

    // Event listener saat tombol select option diklik
    $("#telah-dibatalkan .select-filter-custom select").change(function () {
        $("#telah-dibatalkan .table-datatables").DataTable().ajax.reload();
    });
});
