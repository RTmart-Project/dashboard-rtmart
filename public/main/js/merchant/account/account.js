$(document).ready(function () {
    // DataTables
    dataTablesMerchantAccount();

    function dataTablesMerchantAccount() {
        let roleID = $('meta[name="role-id"]').attr("content");

        $("#merchant-account .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-9'<'filter-merchant-account'>tl><l><'col-sm-12 col-md-2'f><'col-sm-12 col-md-1 d-flex justify-content-end h-100'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/merchant/account/get",
                data: function (d) {
                    d.fromDate = $("#merchant-account #from_date").val();
                    d.toDate = $("#merchant-account #to_date").val();
                    d.distributorId = $(
                        "#merchant-account .filter-depo select"
                    ).val();
                    d.filterAssessment = $(
                        "#merchant-account .filter-assessment select"
                    ).val();
                    d.filterBlock = $(
                        "#merchant-account .filter-block select"
                    ).val();
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
                    data: "Partners",
                    name: "ms_partner.Name",
                    orderable: false,
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
                    data: "Grade",
                    name: "ms_distributor_grade.Grade",
                },
                {
                    data: "CreatedDate",
                    name: "ms_merchant_account.CreatedDate",
                    type: "date",
                },
                {
                    data: "Latitude",
                    name: "ms_merchant_account.Latitude",
                },
                {
                    data: "Longitude",
                    name: "ms_merchant_account.Longitude",
                },
                {
                    data: "StoreAddress",
                    name: "ms_merchant_account.StoreAddress",
                },
                {
                    data: "ReferralCode",
                    name: "ms_merchant_account.ReferralCode",
                },
                {
                    data: "SalesName",
                    name: "ms_sales.SalesName",
                },
                {
                    data: "DistributorName",
                    name: "ms_distributor.DistributorName",
                },
                {
                    data: "StatusBlock",
                    name: "StatusBlock",
                },
                {
                    data: "BlockedMessage",
                    name: "ms_merchant_account.BlockedMessage",
                },
                {
                    data: "Action",
                    name: "Action",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "Product",
                    name: "Product",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "Assessment",
                    name: "Assessment",
                    orderable: false,
                    searchable: false,
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "MerchantAccount"
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
            order: [6, "desc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
            aoColumnDefs: [
                {
                    aTargets: [15],
                    visible: roleID == "IT" ? true : false,
                },
                {
                    aTargets: [7, 8],
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
    $("div.filter-merchant-account").html(`
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="input-group">
                                    <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                                        readonly>
                                    <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                                        readonly>
                                    <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                                    <button type="button" name="refresh" id="refresh"
                                    class="btn btn-sm btn-warning ml-2">Refresh</button>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 d-flex justify-content-center" style="gap: 3px;">
                                <div class="filter-depo mr-1">
                                    <select class="form-control form-control-sm">
                                        <option value="" selected hidden disabled>Filter Depo</option>
                                        <option value="">All</option>
                                    </select>
                                </div>
                                <div class="filter-assessment ml-1">
                                    <select class="form-control form-control-sm">
                                        <option value="" selected hidden disabled>Filter Assessment</option>
                                        <option value="">All</option>
                                        <option value="already-assessed">Sudah Assessment</option>
                                        <option value="not-assessed">Belum Assessment</option>
                                    </select>
                                </div>
                                <div class="filter-block ml-1">
                                    <select class="form-control form-control-sm">
                                        <option value="" selected hidden disabled>Filter Block</option>
                                        <option value="">All</option>
                                        <option value="blocked">Blocked</option>
                                        <option value="unblocked">Not Blocked</option>
                                    </select>
                                </div>
                            </div>
                        </div>`);

    // Setting Awal Daterangepicker
    $("#merchant-account #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#merchant-account #to_date").daterangepicker({
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

        $("#merchant-account #to_date").daterangepicker({
            minDate: $("#merchant-account #from_date").val(),
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

        $("#merchant-account #from_date").daterangepicker({
            maxDate: $("#merchant-account #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#merchant-account .filter-merchant-account").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#merchant-account .filter-merchant-account").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    // Menyisipkan Placeholder Date
    $("#merchant-account #from_date").val("");
    $("#merchant-account #to_date").val("");
    $("#merchant-account #from_date").attr("placeholder", "From Date");
    $("#merchant-account #to_date").attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#merchant-account #refresh").click(function () {
        $("#merchant-account #from_date").val("");
        $("#merchant-account #to_date").val("");
        $("#merchant-account .table-datatables").DataTable().search("");
        // $('#merchant-account .select-filter-custom select').val('').change();
        // $('#merchant-account .select-filter-custom select option[value=]').attr('selected', 'selected');
        $("#merchant-account .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#merchant-account #filter").click(function () {
        $("#merchant-account .table-datatables").DataTable().ajax.reload();
    });

    // Load Distributor ID and Name for filter
    $.ajax({
        type: "get",
        url: "/distributor/account/get",
        success: function (data) {
            let option;
            const dataDistributor = data.data;
            for (const item of dataDistributor) {
                option += `<option value="${item.DistributorID}">${item.DistributorName}</option>`;
            }
            $("#merchant-account .filter-depo select").append(option);
            // customDropdownFilter.createCustomDropdowns();
            // $('#merchant-account .select-filter-custom select').val("All").change();
        },
    });

    // Event listener saat tombol select option diklik
    $("#merchant-account .filter-depo select").change(function () {
        $("#merchant-account .table-datatables").DataTable().ajax.reload();
    });

    $("#merchant-account .filter-assessment select").change(function () {
        $("#merchant-account .table-datatables").DataTable().ajax.reload();
    });

    $("#merchant-account .filter-block select").change(function () {
        $("#merchant-account .table-datatables").DataTable().ajax.reload();
    });

    let csrf = $('meta[name="csrf_token"]').attr("content");

    $("#merchant-account").on("click", ".btn-update-block", function (e) {
        e.preventDefault();
        const merchantID = $(this).data("merchant-id");
        const storeName = $(this).data("store-name");
        const isBlocked = $(this).data("is-blocked");

        let text = "";
        if (isBlocked == 1) {
            text = "membuka block";
        } else {
            text = "mem-block";
        }

        $.confirm({
            title: "Update Block",
            content: `Apakah ingin <b>${text}</b> toko <b>${merchantID} - ${storeName}</b>?<br>
              <form action="/merchant/account/update-block/${merchantID}" method="post">
                <input type="hidden" name="_token" value="${csrf}">
                <label class="mt-2 mb-0">Catatan:</label>
                <input type="text" class="form-control price" autocomplete="off" 
                  name="block_notes" placeholder="Tambahkan Catatan (opsional)">
              </form>`,
            closeIcon: true,
            buttons: {
                simpan: {
                    btnClass: "btn-success",
                    draggable: true,
                    dragWindowGap: 0,
                    action: function () {
                        this.$content.find("form").submit();
                    },
                },
                batal: function () {},
            },
        });
    });
});
