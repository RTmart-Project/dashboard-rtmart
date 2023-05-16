$(document).ready(function () {
    const csrf = $('meta[name="csrf_token"]').attr("content");
    const roleID = $('meta[name="role-id"]').attr("content");

    merchantMembership();

    function merchantMembership(
        startDate = null,
        endDate = null,
        filterStatus = null
    ) {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": csrf,
            },
        });
        $("#merchant-membership-table .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-7'<'filter-merchant-membership-table'>tl><'col-sm-12 col-md-4'f><'col-12 col-md-1 text-center'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/merchant/membership/partner/data",
                type: "POST",
                data: {
                    startDate: startDate,
                    endDate: endDate,
                    filterStatus: filterStatus,
                },
            },
            columns: [
                {
                    data: "merchant_id",
                    name: "ms_history_membership.merchant_id",
                    searchable: true,
                },
                {
                    data: "StoreName",
                    name: "ms_merchant_account.StoreName",
                    searchable: true,
                },
                {
                    data: "OwnerFullName",
                    name: "ms_merchant_account.OwnerFullName",
                    searchable: true,
                },
                {
                    data: "NumberIDCard",
                    name: "ms_merchant_account.NumberIDCard",
                    searchable: true,
                },
                {
                    data: "UsernameIDCard",
                    name: "ms_merchant_account.UsernameIDCard",
                    searchable: true,
                },
                {
                    data: "BirthDate",
                    name: "ms_merchant_account.BirthDate",
                    searchable: false,
                },
                {
                    data: "StoreAddress",
                    name: "ms_merchant_account.StoreAddress",
                    searchable: true,
                },
                {
                    data: "StatusName",
                    name: "StatusMembership.StatusName",
                    searchable: false,
                },
                {
                    data: "batch_number",
                    name: "ms_history_membership.batch_number",
                    searchable: false,
                },
                {
                    data: "VirtualAccountNumber",
                    name: "tx_merchant_funding.VirtualAccountNumber",
                    searchable: true,
                },
                {
                    data: "StatusPaymentName",
                    name: "StatusPaymentName",
                    searchable: false,
                },
                {
                    data: "Action",
                    name: "Action",
                    searchable: false,
                    orderable: false,
                },
                {
                    data: "Disclaimer",
                    name: "Disclaimer",
                    searchable: false,
                    orderable: false,
                }
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "MerchantMembership"
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
                            0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13,
                        ],
                        orthogonal: "export",
                    },
                },
            ],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }

    // Setting Awal Daterangepicker
    $("#merchant-membership #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#merchant-membership #to_date").daterangepicker({
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

        $("#merchant-membership #to_date").daterangepicker({
            minDate: $("#merchant-membership #from_date").val(),
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

        $("#merchant-membership #from_date").daterangepicker({
            maxDate: $("#merchant-membership #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#from_date").on("change", function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $("#to_date").on("change", function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $("#merchant-membership #from_date").val("");
    $("#merchant-membership #to_date").val("");
    $("#merchant-membership #from_date").attr("placeholder", "From Date");
    $("#merchant-membership #to_date").attr("placeholder", "To Date");

    $("#filter").on("click", function () {
        const startDate = $("#from_date").val();
        const endDate = $("#to_date").val();
        const status = $("#status").val();
        $("#merchant-membership-table .table-datatables").DataTable().destroy();
        merchantMembership(startDate, endDate, status);
    });

    $("#refresh").on("click", function () {
        $("#from_date").val("");
        $("#to_date").val("");
        $("#status").val("");
        $("#status").selectpicker("refresh");
        $("#merchant-membership-table .table-datatables").DataTable().destroy();
        merchantMembership();
    });

    $("table").on("click", ".btn-photo", function () {
        const baseImg = $('meta[name="base-image"]').attr("content");
        const merchantID = $(this).data("merchant-id");
        const store = $(this).data("store");
        $.ajax({
            type: "get",
            url: `/merchant/membership/photo/${merchantID}`,
            success: function (res) {
                let dataCouple = "";
                if (
                    res.AsIDCardCouple != null &&
                    res.PhotoIDCardCouple != null &&
                    res.NumberIDCardCouple != null &&
                    res.UsernameIDCardCouple != null
                ) {
                    dataCouple = `<div class="m-2">
                    <h5 class="mb-1 text-center">${res.AsIDCardCouple == "none" ||
                            res.AsIDCardCouple == null
                            ? res.UsernameIDCardCouple
                            : res.AsIDCardCouple.toUpperCase()
                        }</h5>
                    <img class="rounded" width="300" height="200" style="object-fit: cover" src="${baseImg}rtsales/merchantassessment/${res.PhotoIDCardCouple}">
                    <h6 class="my-1 text-center">${res.UsernameIDCardCouple}</h6>
                    <h6 class="text-center">${res.NumberIDCardCouple}</h6>
                  </div>`;
                }

                const div = `
                <div class="m-2">
                    <h5 class="mb-1 text-center">
                    ${res.AsIDCard == "none" || res.AsIDCard == null
                        ? res.UsernameIDCard
                        : res.AsIDCard.toUpperCase()
                    }
                    </h5>
                  <img
                    class="rounded" width="300" height="200" style="object-fit: cover" src="${baseImg}rtsales/merchantassessment/${res.PhotoIDCard}">
                  <h6 class="my-1 text-center">${res.UsernameIDCard}</h6>
                  <h6 class="text-center">${res.NumberIDCard}</h6>
                </div>
                ${dataCouple}
                <div class="m-2">
                  <h5 class="mb-1 text-center">FOTO TOKO</h5>
                  <img
                    class="rounded" width="300" height="200" style="object-fit: cover"
                    src="${baseImg}rtsales/merchantassessment/${res.StorePhotoMembership
                    }">
                </div>`;
                $("#merchant").html(store);
                $("#photo").html(div);
                if (res.ValidationStatusMembershipCouple === 1) {
                    const confirmBtn = `<button class="btn btn-sm btn-success mr-1 btn-terima" data-merchant-id="${merchantID}" data-store="${store}">Terima</button>
                                  <button class="btn btn-sm btn-danger ml-1 btn-tolak" data-merchant-id="${merchantID}" data-store="${store}">Tolak</button>`;
                    $("#confirm").html(confirmBtn);
                } else {
                    $("#confirm").html("");
                }
                $("#modal-photo").modal("show");
            },
        });
    });

    $("table").on("click", ".btn-update-crowdo", function () {
        const merchantID = $(this).data("merchant-id");
        const membershipID = $(this).data("membership-id");
        const statusPaymentID = $(this).data("status-payment-id");

        let formCrowdo = `
            <form action="/merchant/membership/updatePayment/${merchantID}/${membershipID}" method="post">
                <input type="hidden" name="_token" value="${csrf}">
                <div class="form-group">
                    <label for="note" class="m-0">Status :</label>
                    <select class="form-control" name="status_payment" id="status_payment" required>
                        <option value="" selected hidden disabled>-- Pilih Status --</option>
                        <option value="1" ${statusPaymentID == 1 ? "selected" : ""}>Belum Cair</option>
                        <option value="2" ${statusPaymentID == 2 ? "selected" : ""}>Belum Lunas</option>
                        <option value="3" ${statusPaymentID == 3 ? "selected" : ""}>Telah Lunas</option>
                    </select>
                </div>
                <div class="modal-footer justify-content-end pb-0">
                    <button type="submit" class="btn btn-warning">Update</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                </div>
            </form>
        `;

        $("#form-crowdo").html(formCrowdo);

        $("#store").html(`${merchantID}`);
        $("#modal-crowdo").modal("show");
    });
});
