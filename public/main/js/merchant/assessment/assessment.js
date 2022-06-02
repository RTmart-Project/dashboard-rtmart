$(document).ready(function () {
    // DataTables
    dataTablesMerchantAssessment();

    function dataTablesMerchantAssessment() {
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
                },
            },
            columns: [
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
                    data: "NumberIDCard",
                    name: "ms_merchant_assessment.NumberIDCard",
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
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
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
            order: [0, "desc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }

    // Create element for DateRange Filter
    $("div.filter-merchant-assessment").html(`
                      <div class="row">
                          <div class="col-12 col-md-8">
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
});
