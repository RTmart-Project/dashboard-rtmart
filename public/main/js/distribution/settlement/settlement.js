$(document).ready(function () {
    const d = new Date();
    const date = d.toISOString().split("T")[0];

    // DataTables
    dataTablesSettlement();
    summarySettlement();

    function dataTablesSettlement(
        fromDate = "",
        toDate = "",
        distributor = "",
        filterBy = ""
    ) {
        let roleID = $('meta[name="role-id"]').attr("content");
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf_token"]').attr("content"),
            },
        });

        $("#settlement .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-7'<'filter-settlement'>tl><'col-sm-12 col-md-4 justify-content-end'f><'col-sm-6 col-md-1 text-center'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/distribution/settlement/data",
                type: "POST",
                data: {
                    fromDate: fromDate,
                    toDate: toDate,
                    distributor: distributor,
                    filterBy: filterBy,
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
                    data: "Sales",
                    name: "Sales",
                    searchable: false,
                },
                {
                    data: "CreatedDate",
                    name: "tmdo.CreatedDate",
                    type: "date",
                },
                {
                    data: "FinishDate",
                    name: "tmdo.FinishDate",
                    type: "date",
                },
                {
                    data: "TotalSettlement",
                    name: "TotalSettlement",
                    searchable: false,
                },
                {
                    data: "StatusOrder",
                    name: "ms_status_order.StatusOrder",
                },
                {
                    data: "StatusSettlementName",
                    name: "ms_status_settlement.StatusSettlementName",
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
                {
                    data: "Confirmation",
                    name: "Confirmation",
                    searchable: false,
                    orderable: false,
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "DataSetoran"
                        );
                    },
                    action: exportDatatableHelper.newExportAction,
                    text: "Export",
                    titleAttr: "Excel",
                    className: "btn-sm mr-1 rounded",
                    excelStyles: [
                        {
                            cells: "A2:L2",
                            style: {
                                fill: {
                                    pattern: {
                                        color: "92D04F",
                                    },
                                },
                            },
                        },
                    ],
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
                    aTargets: [9, 13],
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
                    aTargets: [16],
                    visible: roleID == "AD" ? false : true,
                },
            ],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });

        // Create element for DateRange Filter
        $("div.filter-settlement").html(`
          <div class="input-group">
              <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" readonly>
              <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm" readonly>
              ${
                  roleID != "AD"
                      ? `<select class="form-control form-control-sm ml-2 selectpicker border" id="filter_distributor"
                            title="Pilih Depo" multiple name="distributor">
                        </select>`
                      : ``
              }
              
              <div class="dropdown">
                  <button class="btn btn-primary btn-sm dropdown-toggle ml-2" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Filter
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                      <a class="dropdown-item" id="filter-tanggal-kirim">Tanggal Kirim</a>
                      <a class="dropdown-item" id="filter-tanggal-selesai">Tanggal Selesai</a>
                  </div>
              </div>
              <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-2">Refresh</button>
          </div>`);

        // Load Distributor ID and Name for filter
        $.ajax({
            type: "get",
            url: "/distributor/account/get",
            success: function (data) {
                let option;
                const dataDistributor = data.data;
                for (const item of dataDistributor) {
                    let selected = "";
                    for (const depo of distributor) {
                        if (item.DistributorID == depo) {
                            selected = "selected";
                        }
                    }
                    option += `<option value="${item.DistributorID}" ${selected}>${item.DistributorName}</option>`;
                }
                $("#settlement #filter_distributor").append(option);
                $("#settlement #filter_distributor").selectpicker("refresh");
            },
        });

        // Setting Awal Daterangepicker
        $("#settlement #from_date").daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });

        // Setting Awal Daterangepicker
        $("#settlement #to_date").daterangepicker({
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

            $("#settlement #to_date").daterangepicker({
                minDate: $("#settlement #from_date").val(),
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

            $("#settlement #from_date").daterangepicker({
                maxDate: $("#settlement #to_date").val(),
                singleDatePicker: true,
                showDropdowns: true,
                locale: {
                    format: "YYYY-MM-DD",
                },
            });
            bCodeChange = false;
        }

        // Disabled input to date ketika from date berubah
        $("#settlement .filter-settlement").on(
            "change",
            "#from_date",
            function () {
                dateStartChange();
            }
        );
        // Disabled input from date ketika to date berubah
        $("#settlement .filter-settlement").on(
            "change",
            "#to_date",
            function () {
                dateEndChange();
            }
        );

        // Menyisipkan Placeholder Date
        $("#settlement #from_date").val(fromDate);
        $("#settlement #to_date").val(toDate);
        $("#settlement #from_date").attr("placeholder", date);
        $("#settlement #to_date").attr("placeholder", date);
    }

    function summarySettlement(
        fromDate = "",
        toDate = "",
        distributor = "",
        filterBy = ""
    ) {
        $.ajax({
            type: "post",
            data: {
                fromDate: fromDate,
                toDate: toDate,
                distributor: distributor,
                filterBy: filterBy,
            },
            url: "/distribution/settlement/summary",
            success: function (res) {
                const difference =
                    res.TotalMustSettlement - res.TotalDoneSettlement;
                $("#nominal-must-settle").html(
                    res.TotalMustSettlement === null
                        ? `Rp 0`
                        : `Rp ${thousands_separators(res.TotalMustSettlement)}`
                );
                $("#nominal-done-settle").html(
                    res.TotalDoneSettlement === null
                        ? `Rp 0`
                        : `Rp ${thousands_separators(res.TotalDoneSettlement)}`
                );
                $("#difference").html(`Rp ${thousands_separators(difference)}`);
            },
        });
    }

    // Event Listener saat tombol refresh diklik
    $("#settlement").on("click", "#refresh", function () {
        $("#settlement #from_date").val("");
        $("#settlement #to_date").val("");
        $("#settlement #filter_distributor").val("");
        const filterBy = "";
        const startDate = "";
        const endDate = "";
        const distributor = "";
        $("#settlement .table-datatables").DataTable().destroy();
        dataTablesSettlement(startDate, endDate, distributor, filterBy);
        summarySettlement(startDate, endDate, distributor, filterBy);
        $("#settlement .table-datatables").DataTable().search("");
        $("#settlement .table-datatables").DataTable().ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#settlement").on("click", "#filter-tanggal-kirim", function () {
        const filterBy = "CreatedDate";
        const startDate = $("#settlement #from_date").val();
        const endDate = $("#settlement #to_date").val();
        const distributor = $("#settlement #filter_distributor").val();
        $("#settlement .table-datatables").DataTable().destroy();
        dataTablesSettlement(startDate, endDate, distributor, filterBy);
        summarySettlement(startDate, endDate, distributor, filterBy);
    });

    // Event listener saat tombol filter diklik
    $("#settlement").on("click", "#filter-tanggal-selesai", function () {
        const filterBy = "FinishDate";
        const startDate = $("#settlement #from_date").val();
        const endDate = $("#settlement #to_date").val();
        const distributor = $("#settlement #filter_distributor").val();
        $("#settlement .table-datatables").DataTable().destroy();
        dataTablesSettlement(startDate, endDate, distributor, filterBy);
        summarySettlement(startDate, endDate, distributor, filterBy);
    });

    $("#settlement").on("click", ".btn-settlement", function () {
        const deliveryOrderID = $(this).data("do-id");
        const storeName = $(this).data("store-name");
        const createdDate = $(this).data("created-date");
        const statusSettlement = $(this).data("status-settlement");
        const nominalMustSettle = $(this).data("must-settle");
        const paymentDate = $(this).data("payment-date");
        const nominal = $(this).data("nominal");
        const paymentSlip = $(this).data("payment-slip");
        const config = $(this).data("config");

        $("#form-setoran").attr(
            "action",
            `/distribution/settlement/update/${deliveryOrderID}`
        );

        $("#status_settlement").val(statusSettlement);
        $("#created_date").val(createdDate.substring(0, 10));
        $("#payment_date").val(paymentDate != "" ? paymentDate : date);
        $("#payment_date").attr("min", createdDate.substring(0, 10));
        $("#payment_date").attr("max", date);

        $("#nominal-settle").val(thousands_separators(nominalMustSettle));
        $("#nominal").val(nominal);
        if (paymentSlip != "") {
            $("#img_output").removeClass("d-none");
            $("#output").attr("src", config + paymentSlip);
        } else {
            $("#img_output").addClass("d-none");
        }

        $("#modal-settlement")
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

    $(".btn-setoran").click(function () {
        const form = $(this).parent().prev();
        const statusSettlement = form.find("#status_settlement").val();
        const createdDate = form.find("#created_date").val();
        const paymentDate = form.find("#payment_date").val();
        const nominal = form.find("#nominal").val();
        const paymentSlip = form.find("#payment_slip").val();

        let next = true;

        if (!paymentDate) {
            Toast.fire({
                icon: "error",
                title: "Harap isi Tanggal Setoran!",
            });
            return (next = false);
        }
        if (paymentDate < createdDate || paymentDate > date) {
            Toast.fire({
                icon: "error",
                title: "Tanggal Setoran Tidak Valid!",
            });
            $("#payment_date").val(date);
            return (next = false);
        }
        if (!nominal) {
            Toast.fire({
                icon: "error",
                title: "Harap isi Nominal Setoran!",
            });
            return (next = false);
        }
        if (nominal < 1) {
            Toast.fire({
                icon: "error",
                title: "Nominal Setoran harus lebih dari 0!",
            });
            return (next = false);
        }
        if (!paymentSlip && statusSettlement != 2) {
            Toast.fire({
                icon: "error",
                title: "Harap isi Bukti Setoran!",
            });
            return (next = false);
        }

        if (next == true) {
            $("#modal-settlement").modal("hide");
            $("#modalKonfirmasi").modal("show");
        }
    });

    $("#btn-submit").on("click", function (e) {
        $(this).prop("disabled", true);
        $(this).children().removeClass("d-none");
        $("#form-setoran").submit();
    });

    $("#settlement table").on("click", ".lihat-bukti", function (e) {
        e.preventDefault();
        const urlImg = $(this).attr("href");
        const storeName = $(this).data("store-name");
        const deliveryOrderID = $(this).data("do-id");
        $.dialog({
            title: `${deliveryOrderID} - ${storeName}`,
            content: `<img  style="object-fit: contain; height: 350px; width: 100%;" src="${urlImg}">`,
        });
    });

    $("#settlement table").on("click", ".btn-approve", function (e) {
        e.preventDefault();
        const deliveryOrderID = $(this).data("do-id");
        const storeName = $(this).data("store-name");

        $.confirm({
            title: "Terima!",
            content: `Yakin ingin menyetujui setoran <b>${deliveryOrderID}</b> - <b>${storeName}</b> ?`,
            closeIcon: true,
            buttons: {
                Yakin: {
                    btnClass: "btn-success",
                    draggable: true,
                    dragWindowGap: 0,
                    action: function () {
                        window.location =
                            "/distribution/settlement/confirm/" +
                            deliveryOrderID +
                            "/approve";
                    },
                },
                tidak: function () {},
            },
        });
    });

    $("#settlement table").on("click", ".btn-reject", function (e) {
        e.preventDefault();
        const deliveryOrderID = $(this).data("do-id");
        const storeName = $(this).data("store-name");

        $.confirm({
            title: "Tolak!",
            content: `Yakin ingin menolak setoran <b>${deliveryOrderID}</b> - <b>${storeName}</b> ?`,
            closeIcon: true,
            buttons: {
                Yakin: {
                    btnClass: "btn-red",
                    draggable: true,
                    dragWindowGap: 0,
                    action: function () {
                        window.location =
                            "/distribution/settlement/confirm/" +
                            deliveryOrderID +
                            "/reject";
                    },
                },
                tidak: function () {},
            },
        });
    });
});
