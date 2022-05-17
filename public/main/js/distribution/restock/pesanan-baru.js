$(document).ready(function () {
    // DataTables
    dataTablesPesananBaru();

    function dataTablesPesananBaru() {
        $("#pesanan-baru .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-pesanan-baru'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/distribution/restock/get/S009",
                data: function (d) {
                    d.fromDate = $("#pesanan-baru #from_date").val();
                    d.toDate = $("#pesanan-baru #to_date").val();
                    d.paymentMethodId = $(
                        "#pesanan-baru .select-filter-custom select"
                    ).val();
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
                            "RestockPesananBaru"
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
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                        orthogonal: "export",
                    },
                },
            ],
            aoColumnDefs: [
                {
                    aTargets: [8],
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
    $("div.filter-pesanan-baru").html(`<div class="input-group">
                            <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" readonly>
                            <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm" readonly>
                            <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                            <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-2">Refresh</button>
                            <div class="select-filter-custom ml-2">
                                <select>
                                    <option value="">All</option>
                                </select>
                            </div>
                        </div>`);

    // Setting Awal Daterangepicker
    $("#pesanan-baru #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#pesanan-baru #to_date").daterangepicker({
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

        $("#pesanan-baru #to_date").daterangepicker({
            minDate: $("#pesanan-baru #from_date").val(),
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

        $("#pesanan-baru #from_date").daterangepicker({
            maxDate: $("#pesanan-baru #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#pesanan-baru .filter-pesanan-baru").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#pesanan-baru .filter-pesanan-baru").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    // Menyisipkan Placeholder Date
    $("#pesanan-baru #from_date").val("");
    $("#pesanan-baru #to_date").val("");
    $("#pesanan-baru #from_date").attr("placeholder", "From Date");
    $("#pesanan-baru #to_date").attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#pesanan-baru #refresh").click(function () {
        $("#pesanan-baru #from_date").val("");
        $("#pesanan-baru #to_date").val("");
        $("#pesanan-baru .table-datatables").DataTable().search("");
        $("#pesanan-baru .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#pesanan-baru #filter").click(function () {
        $("#pesanan-baru .table-datatables").DataTable().ajax.reload();
    });

    // Load PaymentMethod ID and Name for filter
    $.ajax({
        type: "get",
        url: "/payment/method/get",
        success: function (data) {
            let option;
            for (const item of data) {
                option += `<option value="${item.PaymentMethodID}">${item.PaymentMethodName}</option>`;
            }
            $("#pesanan-baru .select-filter-custom select").append(option);
            $("#telah-dikonfirmasi .select-filter-custom select").append(
                option
            );
            $("#dalam-proses .select-filter-custom select").append(option);
            $("#telah-dikirim .select-filter-custom select").append(option);
            $("#telah-selesai .select-filter-custom select").append(option);
            $("#telah-dibatalkan .select-filter-custom select").append(option);
            customDropdownFilter.createCustomDropdowns();
        },
    });

    // Event listener saat tombol select option diklik
    $("#pesanan-baru .select-filter-custom select").change(function () {
        $("#pesanan-baru .table-datatables").DataTable().ajax.reload();
    });
});
