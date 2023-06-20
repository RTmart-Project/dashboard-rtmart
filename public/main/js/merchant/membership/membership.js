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
                    searchable: true,
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
                    data: "NumberIDCard",
                    name: "ms_merchant_account.NumberIDCard",
                },
                {
                    data: "UsernameIDCard",
                    name: "ms_merchant_account.UsernameIDCard",
                },
                {
                    data: "BirthDate",
                    name: "ms_merchant_account.BirthDate",
                },
                {
                    data: "StoreAddress",
                    name: "ms_merchant_account.StoreAddress",
                },
                {
                    data: "StatusNameCrowdo",
                    name: "StatusCrowdo.StatusName",
                },
                {
                    data: "MembershipCoupleSubmitDate",
                    name: "ms_merchant_account.MembershipCoupleSubmitDate",
                },
                {
                    data: "ActionDate",
                    name: "ActionDate",
                    orderable: true,
                },
                {
                    data: "action_by",
                    name: "ms_history_membership.action_by",
                    orderable: false,
                },
                // {
                //     data: "ValidationNoteMembershipCouple",
                //     name: "ms_merchant_account.ValidationNoteMembershipCouple",
                // },
                {
                    data: "rejected_reason",
                    name: "rejected_reason",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "StatusName",
                    name: "StatusMembership.StatusName",
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
                    <img class="rounded" width="300" height="200" style="object-fit: cover" src="${baseImg}rtsales/merchantassessment/${res.PhotoIDCard}">
                    <h6 class="my-1 text-center">${res.UsernameIDCard}</h6>
                    <h6 class="text-center">${res.NumberIDCard}</h6>
                    </div>
                    ${dataCouple}
                    <div class="m-2">
                    <h5 class="mb-1 text-center">FOTO TOKO</h5>
                    <img class="rounded" width="300" height="200" style="object-fit: cover" src="${baseImg}rtsales/merchantassessment/${res.StorePhotoMembership}">
                    </div>
                `;
                $("#merchant").html(store);
                $("#photo").html(div);
                // if (res.ValidationStatusMembershipCouple === 1) {
                //     const confirmBtn = `
                //         <button class="btn btn-sm btn-success mr-1 btn-terima" data-merchant-id="${merchantID}" data-store="${store}">Terima</button>
                //         <button class="btn btn-sm btn-danger ml-1 btn-tolak" data-merchant-id="${merchantID}" data-store="${store}">Tolak</button>
                //     `;
                //     $("#confirm").html(confirmBtn);
                // } else {
                //     $("#confirm").html("");
                // }

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
                tidak: function () { },
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
                tidak: function () { },
            },
        });
    });

    $("table").on("click", ".btn-update-crowdo", function () {
        const merchantID = $(this).data("merchant-id");
        const store = $(this).data("store");
        const statusMembership = $(this).data("status-membership");

        let formCrowdo = `
            <form action="/merchant/membership/updateCrowdo/${merchantID}" method="post">
                <input type="hidden" name="_token" value="${csrf}">
                <div class="form-group">
                    <label for="note">Status :</label>
                    <select class="form-control" name="status-crowdo" id="status-crowdo" required>
                        <option value="" selected hidden disabled>-- Pilih Status --</option>
                        <option value="5" ${statusMembership === 1 ? 'disabled' : ''}>Submitted</option>
                        <option value="6" ${statusMembership === null || statusMembership !== 1 ? 'disabled' : ''}>Approved</option>
                        <option value="7" ${statusMembership === null || statusMembership !== 1 ? 'disabled' : ''}>Rejected</option>
                    </select>
                </div>
                <div id="data-crowdo" class="form-row"></div>
                <div class="modal-footer justify-content-end pb-0">
                    <button type="submit" class="btn btn-warning">Update</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                </div>
            </form>
        `;
        
        $("#form-crowdo").html(formCrowdo);
        $("#form-crowdo").on("change", "#status-crowdo", function () {
            const statusCrowdo = $(this).val();
            let dataCrowdo = `
                <div class="form-row col-12">
                    <div class="form-group col-12 col-md-6">
                        <label for="note">Partner :</label>
                        <select class="form-control" name="partner" id="partner" required>
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="amount">Amount</label>
                        <input type="number" class="form-control autonumeric" name="amount" id="amount" min="1" required/>
                    </div>
                </div>
                <div class="form-row col-12">
                    <div class="form-group col-12 col-md-3">
                        <label for="batch">Batch</label>
                        <input type="number" class="form-control" name="batch" id="batch" min="1" required/>
                    </div>
                    <div class="form-group col-12 col-md-3">
                        <label for="pmpNumber">No. PMP</label>
                        <input type="number" class="form-control" name="pmpNumber" id="pmpNumber" min="1" required/>
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="action_date">Approved Date</label>
                        <input type="date" class="form-control" name="action_date" id="action_date" required/>
                    </div>
                </div>
            `;

            // get partner
            $.ajax({
                type: "get",
                url: "/partner/get",
                success: function (data) {
                    let option;

                    data.forEach((d) => {
                        option += `<option value=${d.PartnerID}>${d.Name}</option>`;
                    })

                    $('#partner').html(`<option value="null" selected disabled>-- Pilih Partner --</option>` + option);
                },
            });

            if (statusCrowdo == 6) {
                $("#data-crowdo").html(dataCrowdo);
            } else if (statusCrowdo == 7) {
                let rejected = `
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="note">Partner :</label>
                            <select class="form-control" name="partner" id="partner" required>
                            </select>
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
                            <label for="action_date">Rejected Date</label>
                            <input type="date" class="form-control" name="action_date" id="action_date" required/>
                        </div>
                    </div>
                    <div class="col-12 col-md-12" id="rejected-checkbox">
                    </div>
                `;

                $("#data-crowdo").html(rejected);
                // get partner
                $.ajax({
                    type: "get",
                    url: "/partner/get",
                    success: function (data) {
                        let option;

                        data.forEach((d) => {
                            option += `<option value=${d.PartnerID}>${d.Name}</option>`;
                        })

                        $('#partner').html(`<option value="null" selected disabled>-- Pilih Partner --</option>` + option);
                    },
                });

                $.ajax({
                    type: "get",
                    url: "/merchant/membership/rejected-reason",
                    success: function (data) {
                        let checkbox;

                        data.forEach((d) => {
                            checkbox += `
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="rejected_id[]" value="${d.status_name}" id="rejected_${d.id}">
                                    <label for="rejected_${d.id}" class="m-0 form-check-label">${d.status_name}</label>
                                </div>
                            `;
                        })

                        var div = checkbox.split('undefined');

                        $('#rejected-checkbox').html(
                            `<div class="form-group">
                                <label>Rejected Reason</label>
                            </div>`+
                            div[1]
                        );
                    },
                });

                $(document).on('click', 'input[type="checkbox"][value="Alasan Lainnya"]', function () {
                    if ($(this).is(':checked')) {
                        $(this).removeAttr('name');

                        if ($("#data-crowdo .rejected_reason").length == 0) {
                            let rejectReason = `
                                <div class="col-12 col-md-12 rejected_reason">
                                    <div class="form-group">
                                        <label for="rejected_reason">Alasan:</label>
                                        <textarea class="form-control" name="rejected_reason" id="rejected_reason" required/></textarea>
                                    </div>
                                </div>
                            `;

                            $("#data-crowdo").append(rejectReason);
                        }
                    } else {
                        $(this).attr('name', 'rejected_id[]');
                        $("#data-crowdo .rejected_reason").remove();
                    }
                });

                $("form").on("submit", function (event) {
                    // Define an empty array to store the checked checkbox values
                    let checkedValues = [];

                    // Loop through each checkbox
                    $('#rejected-checkbox input[type=checkbox]').each(function () {
                        // Check if the checkbox is checked
                        if ($(this).is(':checked')) {
                            // Get the value of the checked checkbox and add it to the array
                            checkedValues.push($(this).val());
                        }
                    });

                    if (checkedValues.length == 0) {
                        event.preventDefault();
                        iziToast.error({
                            title: 'Gagal',
                            message: 'Alasan ditolak wajib diisi!',
                            position: 'topRight',
                            timeout: 5000,
                        });
                    }
                })
            } else {
                let submitted = `
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="note">Partner :</label>
                            <select class="form-control" name="partner" id="partner" required>
                            </select>
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
                            <label for="action_date">Submit Date</label>
                            <input type="date" class="form-control" name="action_date" id="action_date" required/>
                        </div>
                    </div>
                `;

                $("#data-crowdo").html(submitted);
            }
        });

        $("#store").html(`${merchantID} - ${store}`);
        $("#modal-crowdo").modal("show");
    });
});
