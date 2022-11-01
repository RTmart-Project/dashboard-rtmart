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
                url: "/merchant/membership/data",
                type: "POST",
                data: {
                    startDate: startDate,
                    endDate: endDate,
                    filterStatus: filterStatus,
                },
            },
            columns: [
                {
                    data: "MerchantID",
                    name: "ms_merchant_account.MerchantID",
                },
                {
                    data: "StoreName",
                    name: "ms_merchant_account.StoreName",
                },
                {
                    data: "OwnerFullName",
                    name: "ms_merchant_account.OwnerFullName",
                },
                {
                    data: "PhoneNumber",
                    name: "ms_merchant_account.PhoneNumber",
                },
                {
                    data: "NumberIDCard",
                    name: "ms_merchant_account.NumberIDCard",
                },
                {
                    data: "UsernameIDCard",
                    name: "ms_merchant_account.UsernameIDCard",
                },
                {
                    data: "NumberIDCardCouple",
                    name: "ms_merchant_account.NumberIDCardCouple",
                },
                {
                    data: "UsernameIDCardCouple",
                    name: "ms_merchant_account.UsernameIDCardCouple",
                },
                {
                    data: "CountTrx",
                    name: "CountTrx",
                    searchable: false,
                },
                {
                    data: "SumTrx",
                    name: "SumTrx",
                    searchable: false,
                },
                {
                    data: "StoreAddress",
                    name: "ms_merchant_account.StoreAddress",
                },
                {
                    data: "AreaName",
                    name: "ms_area.AreaName",
                },
                {
                    data: "Subdistrict",
                    name: "ms_area.Subdistrict",
                },
                {
                    data: "City",
                    name: "ms_area.City",
                },
                {
                    data: "Province",
                    name: "ms_area.Province",
                },
                {
                    data: "PostalCode",
                    name: "ms_area.PostalCode",
                },
                {
                    data: "DistributorName",
                    name: "ms_distributor.DistributorName",
                },
                {
                    data: "Sales",
                    name: "Sales",
                },
                {
                    data: "StatusNameCrowdo",
                    name: "StatusCrowdo.StatusName",
                },
                {
                    data: "CrowdoLoanID",
                    name: "ms_merchant_account.CrowdoLoanID",
                },
                {
                    data: "CrowdoAmount",
                    name: "ms_merchant_account.CrowdoAmount",
                },
                {
                    data: "CrowdoBatch",
                    name: "ms_merchant_account.CrowdoBatch",
                },
                {
                    data: "CrowdoApprovedDate",
                    name: "ms_merchant_account.CrowdoApprovedDate",
                },
                {
                    data: "StatusName",
                    name: "StatusMembership.StatusName",
                },
                {
                    data: "MembershipCoupleSubmitDate",
                    name: "ms_merchant_account.MembershipCoupleSubmitDate",
                },
                {
                    data: "MembershipCoupleConfirmDate",
                    name: "ms_merchant_account.MembershipCoupleConfirmDate",
                },
                {
                    data: "MembershipCoupleConfirmBy",
                    name: "ms_merchant_account.MembershipCoupleConfirmBy",
                },
                {
                    data: "ValidationNoteMembershipCouple",
                    name: "ms_merchant_account.ValidationNoteMembershipCouple",
                },
                {
                    data: "Photo",
                    name: "Photo",
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
                            0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14,
                            15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27,
                        ],
                        orthogonal: "export",
                    },
                },
            ],
            aoColumnDefs: [
                {
                    aTargets: [4, 6],
                    mRender: function (data, type, full) {
                        if (type === "export") {
                            return "'" + data;
                        } else {
                            return data;
                        }
                    },
                },
                {
                    aTargets: [9, 20],
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
                    aTargets: [29],
                    visible: roleID == "IT" ? true : false,
                },
            ],
            order: [24, "desc"],
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
                    <h5 class="mb-1 text-center">${res.AsIDCardCouple.toUpperCase()}</h5>
                    <img
                      class="rounded" width="300" height="200" style="object-fit: cover"
                      src="${baseImg}rtsales/merchantassessment/${
                        res.PhotoIDCardCouple
                    }">
                    <h6 class="my-1 text-center">${
                        res.UsernameIDCardCouple
                    }</h6>
                    <h6 class="text-center">${res.NumberIDCardCouple}</h6>
                  </div>`;
                }

                const div = `
                <div class="m-2">
                  <h5 class="mb-1 text-center">${
                      res.AsIDCard == "none"
                          ? res.UsernameIDCard
                          : res.AsIDCard.toUpperCase()
                  }</h5>
                  <img
                    class="rounded" width="300" height="200" style="object-fit: cover"
                    src="${baseImg}rtsales/merchantassessment/${
                    res.PhotoIDCard
                }">
                  <h6 class="my-1 text-center">${res.UsernameIDCard}</h6>
                  <h6 class="text-center">${res.NumberIDCard}</h6>
                </div>
                ${dataCouple}
                <div class="m-2">
                  <h5 class="mb-1 text-center">FOTO TOKO</h5>
                  <img
                    class="rounded" width="300" height="200" style="object-fit: cover"
                    src="${baseImg}rtsales/merchantassessment/${
                    res.StorePhotoMembership
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

    $("#confirm").on("click", ".btn-terima", function (e) {
        e.preventDefault();
        const merchantID = $(this).data("merchant-id");
        const store = $(this).data("store");
        $.confirm({
            title: "Konfirmasi",
            content: `Apakah yakin ingin menerima pendaftaran membership <b>${merchantID} - ${store}</b>?
                    <form action="/merchant/membership/confirm/${merchantID}/approve" method="post">
                      <input type="hidden" name="_token" value="${csrf}">
                    </form>`,
            closeIcon: true,
            type: "green",
            typeAnimated: true,
            buttons: {
                ya: {
                    btnClass: "btn-success",
                    draggable: true,
                    dragWindowGap: 0,
                    action: function () {
                        this.$content.find("form").submit();
                    },
                },
                tidak: function () {},
            },
        });
    });

    $("#confirm").on("click", ".btn-tolak", function (e) {
        e.preventDefault();
        const merchantID = $(this).data("merchant-id");
        const store = $(this).data("store");
        $.confirm({
            title: "Konfirmasi",
            content: `Apakah yakin ingin menolak pendaftaran membership <b>${merchantID} - ${store}</b>?
                  <form action="/merchant/membership/confirm/${merchantID}/reject" method="post">
                    <input type="hidden" name="_token" value="${csrf}">
                    <label for="note">Catatan :</label>
                    <textarea class="form-control" rows="3" placeholder="Masukkan Catatan" id="note" name="note"></textarea>
                  </form>`,
            closeIcon: true,
            type: "red",
            typeAnimated: true,
            buttons: {
                ya: {
                    btnClass: "btn-danger",
                    draggable: true,
                    dragWindowGap: 0,
                    action: function () {
                        let note = this.$content.find("#note").val();
                        if (!note) {
                            $.alert("Harap isi Catatan", "Konfirmasi");
                            return false;
                        }
                        this.$content.find("form").submit();
                    },
                },
                tidak: function () {},
            },
        });
    });

    $("table").on("click", ".btn-update-crowdo", function () {
        const merchantID = $(this).data("merchant-id");
        const store = $(this).data("store");
        const statusCrowdo = $(this).data("status-crowdo");

        let formCrowdo = `<form action="/merchant/membership/updateCrowdo/${merchantID}" method="post">
                            <input type="hidden" name="_token" value="${csrf}">
                            <div class="form-group">
                                <label for="note" class="m-0">Status :</label>
                                <select class="form-control" name="status-crowdo" id="status-crowdo" required>
                                    <option value="" selected hidden disabled>-- Pilih Status --</option>
                                    <option value="5" ${
                                        statusCrowdo == 5 ? "selected" : ""
                                    }>Submitted</option>
                                    <option value="6" ${
                                        statusCrowdo == 6 ? "selected" : ""
                                    }>Approved</option>
                                    <option value="7" ${
                                        statusCrowdo == 7 ? "selected" : ""
                                    }>Rejected</option>
                                </select>
                            </div>
                            <div id="data-crowdo" class="row"></div>
                            <div class="modal-footer justify-content-end pb-0">
                                <button type="submit" class="btn btn-warning">Update</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                            </div>
                        </form>`;

        $("#form-crowdo").html(formCrowdo);
        $("#form-crowdo").on("change", "#status-crowdo", function () {
            const statusCrowdo = $(this).val();
            let dataCrowdo = `
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="loan_id">Loan ID</label>
                        <input type="text" class="form-control" name="loan_id" id="loan_id" required/>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" class="form-control autonumeric" name="amount" id="amount" min="1" required/>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="batch">Batch</label>
                        <input type="number" class="form-control" name="batch" id="batch" min="1" required/>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="approved_date">Approved Date</label>
                        <input type="date" class="form-control" name="approved_date" id="approved_date" required/>
                    </div>
                </div>
            `;
            if (statusCrowdo == 6) {
                $("#data-crowdo").html(dataCrowdo);
            } else {
                $("#data-crowdo").html("");
            }
        });
        $("#store").html(`${merchantID} - ${store}`);
        $("#modal-crowdo").modal("show");
    });

    // $("#merchant-membership-table").on(
    //     "click",
    //     ".btn-update-crowdo",
    //     function (e) {
    //         e.preventDefault();
    //         const merchantID = $(this).data("merchant-id");
    //         const store = $(this).data("store");
    //         const statusCrowdo = $(this).data("status-crowdo");
    //         $.confirm({
    //             title: "Update Status Crowdo",
    //             content: `<b>${merchantID} - ${store}</b>?
    //               <form action="/merchant/membership/updateCrowdo/${merchantID}" method="post">
    //                 <input type="hidden" name="_token" value="${csrf}">
    //                 <label for="note" class="m-0">Status :</label>
    //                 <select class="form-control" name="status-crowdo" id="status-crowdo">
    //                     <option value="" selected hidden disabled>-- Pilih Status --</option>
    //                     <option value="5" ${
    //                         statusCrowdo == 5 ? "selected" : ""
    //                     }>Submitted</option>
    //                     <option value="6" ${
    //                         statusCrowdo == 6 ? "selected" : ""
    //                     }>Approved</option>
    //                     <option value="7" ${
    //                         statusCrowdo == 7 ? "selected" : ""
    //                     }>Rejected</option>
    //                 </select>
    //               </form>`,
    //             closeIcon: true,
    //             typeAnimated: true,
    //             buttons: {
    //                 update: {
    //                     btnClass: "btn-warning",
    //                     draggable: true,
    //                     dragWindowGap: 0,
    //                     action: function () {
    //                         let status = this.$content
    //                             .find("#status-crowdo")
    //                             .val();
    //                         if (!status) {
    //                             $.alert(
    //                                 "Harap Pilih Status Crowdo",
    //                                 "Update Status Crowdo"
    //                             );
    //                             return false;
    //                         }
    //                         this.$content.find("form").submit();
    //                     },
    //                 },
    //                 batal: function () {},
    //             },
    //         });
    //     }
    // );
});
