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
                    name: "tmdo.DeliveryOrderID",
                },
                {
                    data: "UrutanDO",
                    name: "UrutanDO",
                    searchable: false,
                },
                {
                    data: "StockOrderID",
                    name: "tmdo.StockOrderID",
                },
                {
                    data: "DistributorName",
                    name: "ms_distributor.DistributorName",
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
                    name: "tmdo.CreatedDate",
                },
                {
                    data: "FinishDate",
                    name: "tmdo.FinishDate",
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
                    data: "BillNominal",
                    name: "BillNominal",
                    searchable: false,
                },
                {
                    data: "StatusOrder",
                    name: "ms_status_order.StatusOrder",
                },
                {
                    data: "IsPaid",
                    name: "tmdo.IsPaid",
                },
                {
                    data: "PaymentDate",
                    name: "tmdo.PaymentDate",
                },
                {
                    data: "PaymentNominal",
                    name: "tmdo.PaymentNominal",
                },
                {
                    data: "PaymentSlip",
                    name: "tmdo.PaymentSlip",
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
                        columns: [
                            0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14,
                        ],
                        orthogonal: "export",
                    },
                },
            ],
            order: [7, "asc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
            aoColumnDefs: [
                {
                    aTargets: [10, 14],
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

    new AutoNumeric(".autonumeric", {
        allowDecimalPadding: false,
        decimalCharacter: ",",
        digitGroupSeparator: ".",
        unformatOnSubmit: true,
    });

    $("#bill-paylater").on("click", ".btn-payment", function () {
        const deliveryOrderID = $(this).data("do-id");
        const storeName = $(this).data("store-name");

        $("#form-pelunasan").attr(
            "action",
            `/distribution/bill/update/${deliveryOrderID}`
        );

        $("#modal-payment")
            .modal("show")
            .on("shown.bs.modal", function () {
                $("#info").html(
                    `Update Pelunasan <b>${deliveryOrderID}</b> dari <b>${storeName}</b>`
                );
            });
    });

    $("#payment_slip").change(function () {
        $("#img_output").removeClass("d-none");
    });

    let Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 4000,
    });

    $(".btn-pelunasan").click(function () {
        const form = $(this).parent().prev();
        const paymentDate = form.find("#payment_date").val();
        const nominal = form.find("#nominal").val();
        const paymentSlip = form.find("#payment_slip").val();

        let next = true;

        if (!paymentDate) {
            Toast.fire({
                icon: "error",
                title: "Harap isi Tanggal Pelunasan!",
            });
            return (next = false);
        }
        if (!nominal) {
            Toast.fire({
                icon: "error",
                title: "Harap isi Nominal Bayar!",
            });
            return (next = false);
        }
        if (!paymentSlip) {
            Toast.fire({
                icon: "error",
                title: "Harap isi Bukti Bayar!",
            });
            return (next = false);
        }

        if (next == true) {
            $("#modal-payment").modal("hide");
            $("#modalKonfirmasi").modal("show");
        }
    });

    $("#bill-paylater table").on("click", ".lihat-bukti", function (e) {
        e.preventDefault();
        const urlImg = $(this).attr("href");
        const storeName = $(this).data("store-name");
        const deliveryOrderID = $(this).data("do-id");
        $.dialog({
            title: `${deliveryOrderID} - ${storeName}`,
            content: `<img  style="object-fit: contain; height: 350px; width: 100%;" src="${urlImg}">`,
        });
    });
});
