$(document).ready(function () {
    // DataTables
    dataTablesMerchant();

    function dataTablesMerchant() {
        $("#distributor-merchant .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-7'<'filter-distributor-merchant'>tl><'col-sm-12 col-md-1'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/distribution/merchant/get",
                data: function (d) {
                    d.fromDate = $("#distributor-merchant #from_date").val();
                    d.toDate = $("#distributor-merchant #to_date").val();
                    d.distributorId = $(
                        "#distributor-merchant .select-filter-custom select"
                    ).val();
                },
            },
            columns: [
                {
                    data: "MerchantID",
                    name: "ms_merchant_account.MerchantID",
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
                    data: "Partner",
                    name: "ms_merchant_account.Partner",
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
                            "MerchantGrade"
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
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                        orthogonal: "export",
                    },
                },
            ],
            aoColumnDefs: [
                {
                    aTargets: [8, 9],
                    mRender: function (data, type, full) {
                        if (type === "export") {
                            return "'" + data;
                        } else {
                            return data;
                        }
                    },
                },
            ],
            order: [7, "desc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }

    // Create element for DateRange Filter
    let depo = $('meta[name="depo"]').attr("content");
    if (depo == "ALL") {
        $("div.filter-distributor-merchant").html(`<div class="input-group">
                          <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                              readonly>
                          <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                              readonly>
                          <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                          <button type="button" name="refresh" id="refresh"
                              class="btn btn-sm btn-warning ml-2">Refresh</button>
                          <div class="select-filter-custom ml-2">
                              <select>
                                  <option value="">All</option>
                              </select>
                          </div>
                      </div>`);
    } else {
        $("div.filter-distributor-merchant").html(`<div class="input-group">
                          <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                              readonly>
                          <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                              readonly>
                          <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                          <button type="button" name="refresh" id="refresh"
                              class="btn btn-sm btn-warning ml-2">Refresh</button>
                      </div>`);
    }

    // Setting Awal Daterangepicker
    $("#distributor-merchant #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#distributor-merchant #to_date").daterangepicker({
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

        $("#distributor-merchant #to_date").daterangepicker({
            minDate: $("#distributor-merchant #from_date").val(),
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

        $("#distributor-merchant #from_date").daterangepicker({
            maxDate: $("#distributor-merchant #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#distributor-merchant .filter-distributor-merchant").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#distributor-merchant .filter-distributor-merchant").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    // Menyisipkan Placeholder Date
    $("#distributor-merchant #from_date").val("");
    $("#distributor-merchant #to_date").val("");
    $("#distributor-merchant #from_date").attr("placeholder", "From Date");
    $("#distributor-merchant #to_date").attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#distributor-merchant #refresh").click(function () {
        $("#distributor-merchant #from_date").val("");
        $("#distributor-merchant #to_date").val("");
        $("#distributor-merchant .table-datatables").DataTable().search("");
        // $('#distributor-merchant .select-filter-custom select').val('').change();
        // $('#distributor-merchant .select-filter-custom select option[value=]').attr('selected', 'selected');
        $("#distributor-merchant .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#distributor-merchant #filter").click(function () {
        $("#distributor-merchant .table-datatables").DataTable().ajax.reload();
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
            $("#distributor-merchant .select-filter-custom select").append(option);
            customDropdownFilter.createCustomDropdowns();
        },
    });

    // Event listener saat tombol select option diklik
    $("#distributor-merchant .select-filter-custom select").change(function () {
        $("#distributor-merchant .table-datatables").DataTable().ajax.reload();
        // console.log($('#distributor-merchant .select-filter-custom select').val())
    });

    let csrf = $('meta[name="csrf_token"]').attr("content");

    // Event listener saat tombol edit diklik
    $("table").on("click", ".edit-grade", function (e) {
        e.preventDefault();

        // get grade from distributor id
        const distributor = $(this).data("distributor-id");
        const merchantId = $(this).data("merchant-id");
        const gradeId = $(this).data("grade-id");
        const storeName = $(this).data("store-name");
        const ownerName = $(this).data("owner-name");
        $.ajax({
            type: "GET",
            url: "/merchant/account/grade/get/" + distributor,
            dataType: "JSON",
            success: function (res) {
                if (res) {
                    let option = "";
                    $.each(res, function (index, value) {
                        if (gradeId == value.GradeID) {
                            option += `<option value="${value.GradeID}" selected>${value.Grade}</option>`;
                        } else {
                            option += `<option value="${value.GradeID}">${value.Grade}</option>`;
                        }
                    });
                    $("#grade").html(option);
                    $("#grade").selectpicker("refresh");
                }
            },
        });
        $.confirm({
            title: "Ubah Grade",
            content: `<p>Ubah grade pada <b>${storeName}</b> milik <b>${ownerName}</b><br></p>
                          <label class="mt-2 mb-0">Grade:</label>
                          <form action="/distribution/merchant/grade/update/${merchantId}" method="post">
                              <input type="hidden" name="_token" value="${csrf}">
                              <input type="hidden" name="distributor" value="${distributor}">
                              <select class="form-control selectpicker border" data-container=".jconfirm" name="grade" id="grade" title="Pilih Grade">
                                
                              </select>
                          </form>`,
            closeIcon: true,
            buttons: {
                simpan: {
                    btnClass: "btn-success",
                    draggable: true,
                    dragWindowGap: 0,
                    action: function () {
                        let grade = this.$content.find("#grade").val();
                        if (!grade) {
                            $.alert(
                                "Grade toko tidak boleh kosong",
                                "Ubah Grade"
                            );
                            return false;
                        }
                        this.$content.find("form").submit();
                    },
                },
                tidak: function () {},
            },
        });
    });
});
