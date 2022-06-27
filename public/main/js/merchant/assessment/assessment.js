$(document).ready(function () {
    let csrf = $('meta[name="csrf_token"]').attr("content");

    // DataTables
    dataTablesMerchantAssessment();

    function dataTablesMerchantAssessment() {
        let roleID = $('meta[name="role-id"]').attr("content");

        $("#merchant-assessment .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-8'<'filter-merchant-assessment'>tl><l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/merchant/assessment/get",
                data: function (d) {
                    d.fromDate = $("#merchant-assessment #from_date").val();
                    d.toDate = $("#merchant-assessment #to_date").val();
                    d.filterValid = $(
                        "#merchant-assessment .filter-valid select"
                    ).val();
                },
            },
            columns: [
                {
                    data: "Empty",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "Checkbox",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "CreatedAt",
                    name: "ms_merchant_assessment.CreatedAt",
                },
                {
                    data: "StoreID",
                    name: "ms_merchant_assessment.StoreID",
                },
                {
                    data: "StoreName",
                    name: "ms_store.StoreName",
                },
                {
                    data: "PhoneNumber",
                    name: "ms_store.PhoneNumber",
                },
                {
                    data: "MerchantID",
                    name: "ms_merchant_assessment.MerchantID",
                },
                {
                    data: "MerchantName",
                    name: "MerchantName",
                },
                {
                    data: "MerchantNumber",
                    name: "MerchantNumber",
                },
                {
                    data: "CountPO",
                    name: "CountPO",
                    searchable: false,
                },
                {
                    data: "NumberIDCard",
                    name: "ms_merchant_assessment.NumberIDCard",
                },
                {
                    data: "NameIDCard",
                    name: "ms_merchant_assessment.NameIDCard",
                },
                {
                    data: "BirthDateIDCard",
                    name: "ms_merchant_assessment.BirthDateIDCard",
                },
                {
                    data: "Note",
                    name: "Note",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "TurnoverAverage",
                    name: "ms_merchant_assessment.TurnoverAverage",
                },
                {
                    data: "Transaction",
                    name: "Transaction",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "ReferralCode",
                    name: "ReferralCode",
                },
                {
                    data: "SalesName",
                    name: "SalesName",
                },
                {
                    data: "MerchantPhoto",
                    name: "MerchantPhoto",
                    searhable: false,
                    orderable: false,
                },
                {
                    data: "StruckPhoto",
                    name: "StruckPhoto",
                    searhable: false,
                    orderable: false,
                },
                {
                    data: "StockPhoto",
                    name: "StockPhoto",
                    searhable: false,
                    orderable: false,
                },
                {
                    data: "IdCardPhoto",
                    name: "IdCardPhoto",
                    searhable: false,
                    orderable: false,
                },
                {
                    data: "Action",
                    name: "Action",
                    searhable: false,
                    orderable: false,
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "MerchantAssessment"
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
                            2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16,
                            17,
                        ],
                        orthogonal: "export",
                    },
                },
            ],
            aoColumnDefs: [
                {
                    aTargets: [14],
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
                    aTargets: [10],
                    mRender: function (data, type, full) {
                        if (type === "export") {
                            return "'" + data;
                        } else {
                            return data;
                        }
                    },
                },
                {
                    aTargets: [22],
                    visible: roleID == "IT" || roleID == "FI" ? true : false,
                    // visible: false,
                },
            ],
            order: [2, "desc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }

    // Create element for DateRange Filter
    $("div.filter-merchant-assessment").html(`
                      <div class="row">
                          <div class="col-12 col-md-10">
                              <div class="input-group">
                                  <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                                      readonly>
                                  <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                                      readonly>
                                  <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                                  <button type="button" name="refresh" id="refresh"
                                  class="btn btn-sm btn-warning ml-2">Refresh</button>
                                  <div class="filter-valid ml-2">
                                    <select class="form-control form-control-sm">
                                        <option selected disabled hidden>Filter Valid</option>
                                        <option value="">All</option>
                                        <option value="valid">Valid Checked</option>
                                        <option value="invalid">Valid Unchecked</option>
                                    </select>
                                </div>
                              </div>
                          </div>
                      </div>`);

    // Setting Awal Daterangepicker
    $("#merchant-assessment #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#merchant-assessment #to_date").daterangepicker({
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

        $("#merchant-assessment #to_date").daterangepicker({
            minDate: $("#merchant-assessment #from_date").val(),
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

        $("#merchant-assessment #from_date").daterangepicker({
            maxDate: $("#merchant-assessment #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#merchant-assessment .filter-merchant-assessment").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#merchant-assessment .filter-merchant-assessment").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    // Menyisipkan Placeholder Date
    $("#merchant-assessment #from_date").val("");
    $("#merchant-assessment #to_date").val("");
    $("#merchant-assessment #from_date").attr("placeholder", "From Date");
    $("#merchant-assessment #to_date").attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#merchant-assessment #refresh").click(function () {
        $("#merchant-assessment #from_date").val("");
        $("#merchant-assessment #to_date").val("");
        $("#merchant-assessment .table-datatables").DataTable().search("");
        // $('#merchant-assessment .select-filter-custom select').val('').change();
        // $('#merchant-assessment .select-filter-custom select option[value=]').attr('selected', 'selected');
        $("#merchant-assessment .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#merchant-assessment #filter").click(function () {
        $("#merchant-assessment .table-datatables").DataTable().ajax.reload();
    });

    $("#merchant-assessment .filter-valid select").change(function () {
        $("#merchant-assessment .table-datatables").DataTable().ajax.reload();
    });

    $("#merchant-assessment table").on(
        "change",
        ".check-assessment",
        function () {
            const assessmentID = $(this).val();
            const checked = $(this).prop("checked");
            const checkbox = $(this);

            if (checked == true) {
                $.ajax({
                    url: `/merchant/assessment/checked/${assessmentID}`,
                    success: function (result) {
                        if (result.status == "success") {
                            iziToast.success({
                                title: "Berhasil",
                                message: result.message,
                                position: "topRight",
                            });
                        }
                        if (result.status == "failed") {
                            checkbox.prop("checked", false);
                            iziToast.error({
                                title: "Gagal",
                                message: result.message,
                                position: "topRight",
                            });
                        }
                    },
                });
                $("#merchant-assessment .table-datatables")
                    .DataTable()
                    .ajax.reload();
            } else if (checked == false) {
                $.ajax({
                    url: `/merchant/assessment/unchecked/${assessmentID}`,
                    success: function (result) {
                        if (result.status == "success") {
                            iziToast.success({
                                title: "Berhasil",
                                message: result.message,
                                position: "topRight",
                            });
                        }
                        if (result.status == "failed") {
                            checkbox.prop("checked", true);
                            iziToast.error({
                                title: "Gagal",
                                message: result.message,
                                position: "topRight",
                            });
                        }
                    },
                });
                $("#merchant-assessment .table-datatables")
                    .DataTable()
                    .ajax.reload();
            }
        }
    );

    $(".btn-download-ktp").click(function () {
        $.ajax({
            url: `/merchant/assessment/downloadKTP`,
            dataType: "binary",
            xhrFields: {
                responseType: "blob",
            },
            success: function (result) {
                const link = document.createElement("a");
                const fileName = "Assessment_KTP_Merchant.zip";

                link.href = URL.createObjectURL(result);
                link.download = fileName;
                link.click();
            },
        });
        return false;
    });
});
