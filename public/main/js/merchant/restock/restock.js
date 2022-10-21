$(document).ready(function () {
    // DataTables
    dataTablesRestock();

    function dataTablesRestock() {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf_token"]').attr("content"),
            },
        });

        $("#merchant-restock .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-9'<'filter-merchant-restock'>tl><'col-sm-12 col-md-2'f><'col-sm-12 col-md-1 d-flex justify-content-end h-100'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/merchant/restock/get",
                method: "POST",
                data: function (d) {
                    d.fromDate = $("#merchant-restock #from_date").val();
                    d.toDate = $("#merchant-restock #to_date").val();
                    d.paymentMethodId = $(
                        "#merchant-restock .filter-payment select"
                    ).val();
                    d.filterAssessment = $(
                        "#merchant-restock .filter-assessment select"
                    ).val();
                    d.filterValid = $(
                        "#merchant-restock .filter-valid select"
                    ).val();
                },
            },
            columns: [
                {
                    data: "StockOrderID",
                    name: "Restock.StockOrderID",
                },
                {
                    data: "CreatedDate",
                    name: "Restock.CreatedDate",
                    type: "date",
                },
                {
                    data: "MerchantID",
                    name: "Restock.MerchantID",
                },
                {
                    data: "StoreName",
                    name: "Restock.StoreName",
                },
                {
                    data: "OwnerFullName",
                    name: "Restock.OwnerFullName",
                },
                {
                    data: "StoreAddress",
                    name: "Restock.StoreAddress",
                },
                {
                    data: "NumberIDCard",
                    name: "Restock.NumberIDCard",
                },
                {
                    data: "Ket",
                    name: "Ket",
                    searchable: false,
                    orderable: false,
                },
                {
                    data: "TurnoverAverage",
                    name: "Restock.TurnoverAverage",
                },
                {
                    data: "Grade",
                    name: "Restock.Grade",
                },
                {
                    data: "Partners",
                    name: "Partners",
                },
                {
                    data: "PhoneNumber",
                    name: "Restock.PhoneNumber",
                },
                {
                    data: "DistributorName",
                    name: "Restock.DistributorName",
                },
                {
                    data: "PaymentMethodName",
                    name: "Restock.PaymentMethodName",
                },
                {
                    data: "StatusOrder",
                    name: "Restock.StatusOrder",
                },
                {
                    data: "IsValid",
                    name: "Restock.IsValid",
                },
                {
                    data: "ValidationNotes",
                    name: "Restock.ValidationNotes",
                },
                {
                    data: "TotalPrice",
                    name: "Restock.TotalPrice",
                },
                {
                    data: "DiscountPrice",
                    name: "Restock.DiscountPrice",
                },
                {
                    data: "DiscountVoucher",
                    name: "Restock.DiscountVoucher",
                },
                {
                    data: "ServiceChargeNett",
                    name: "Restock.ServiceChargeNett",
                },
                {
                    data: "DeliveryFee",
                    name: "Restock.DeliveryFee",
                },
                {
                    data: "TotalAmount",
                    name: "TotalAmount",
                },
                {
                    data: "MarginEstimation",
                    name: "MarginEstimation",
                    searchable: false,
                },
                {
                    data: "MarginEstimationPercentage",
                    name: "MarginEstimationPercentage",
                },
                {
                    data: "MarginReal",
                    name: "MarginReal",
                    searchable: false,
                },
                {
                    data: "MarginRealPercentage",
                    name: "MarginRealPercentage",
                },
                {
                    data: "TotalMargin",
                    name: "TotalMargin",
                    searchable: false,
                },
                {
                    data: "TotalMarginPercentage",
                    name: "TotalMarginPercentage",
                },
                {
                    data: "Notes",
                    name: "Notes",
                },
                {
                    data: "ReferralCode",
                    name: "Restock.ReferralCode",
                },
                {
                    data: "SalesName",
                    name: "Restock.SalesName",
                },
                {
                    data: "Invoice",
                    name: "Invoice",
                    orderable: false,
                    searchable: false,
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
                            "RestockMerchant"
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
                            15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27,
                            28, 29, 30, 31,
                        ],
                        orthogonal: "export",
                    },
                },
            ],
            order: [1, "desc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
            aoColumnDefs: [
                {
                    aTargets: [8, 17, 18, 19, 20, 21, 22, 23, 25, 27],
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
                {
                    aTargets: [6],
                    mRender: function (data, type, full) {
                        if (type === "export") {
                            return "'" + data;
                        } else {
                            return data;
                        }
                    },
                },
            ],
        });
    }

    // Create element for DateRange Filter
    $("div.filter-merchant-restock").html(`<div class="input-group">
                            <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" readonly>
                            <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm" readonly>
                            <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                            <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-2">Refresh</button>
                            <div class="filter-payment ml-2">
                                <select class="form-control form-control-sm">
                                    <option selected disabled hidden>Filter Pembayaran</option>
                                    <option value="">All</option>
                                </select>
                            </div>
                            <div class="filter-assessment ml-2">
                                <select class="form-control form-control-sm">
                                    <option selected disabled hidden>Filter Assessment</option>
                                    <option value="">All</option>
                                    <option value="already-assessed">Sudah Assessment</option>
                                    <option value="not-assessed">Belum Assessment</option>
                                </select>
                            </div>
                            <div class="filter-valid ml-2">
                                <select class="form-control form-control-sm">
                                    <option selected disabled hidden>Filter Valid KTP</option>
                                    <option value="">All</option>
                                    <option value="valid">Valid Checked</option>
                                    <option value="invalid">Valid Unchecked</option>
                                </select>
                            </div>
                        </div>`);

    // Setting Awal Daterangepicker
    $("#merchant-restock #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#merchant-restock #to_date").daterangepicker({
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

        $("#merchant-restock #to_date").daterangepicker({
            minDate: $("#merchant-restock #from_date").val(),
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

        $("#merchant-restock #from_date").daterangepicker({
            maxDate: $("#merchant-restock #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#merchant-restock .filter-merchant-restock").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#merchant-restock .filter-merchant-restock").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    const d = new Date();
    const date = `${d.getFullYear()}-${("0" + (d.getMonth() + 1)).slice(-2)}-${(
        "0" + d.getDate()
    ).slice(-2)}`;

    // Menyisipkan Placeholder Date
    $("#merchant-restock #from_date").val("");
    $("#merchant-restock #to_date").val("");
    $("#merchant-restock #from_date").attr("placeholder", date);
    $("#merchant-restock #to_date").attr("placeholder", date);

    // Event Listener saat tombol refresh diklik
    $("#merchant-restock #refresh").click(function () {
        $("#merchant-restock #from_date").val("");
        $("#merchant-restock #to_date").val("");
        $("#merchant-restock .table-datatables").DataTable().search("");
        // $('#merchant-restock .select-filter-custom select').val('').change();
        // $('#merchant-restock .select-filter-custom select option[value=]');
        $("#merchant-restock .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#merchant-restock #filter").click(function () {
        $("#merchant-restock .table-datatables").DataTable().ajax.reload();
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
            $("#merchant-restock .filter-payment select").append(option);
            $("#product-restock .filter-payment select").append(option);
            // customDropdownFilter.createCustomDropdowns();
        },
    });

    // Event listener saat tombol select option diklik
    $("#merchant-restock .filter-payment select").change(function () {
        $("#merchant-restock .table-datatables").DataTable().ajax.reload();
    });

    $("#merchant-restock .filter-assessment select").change(function () {
        $("#merchant-restock .table-datatables").DataTable().ajax.reload();
    });

    $("#merchant-restock .filter-valid select").change(function () {
        $("#merchant-restock .table-datatables").DataTable().ajax.reload();
    });
});
