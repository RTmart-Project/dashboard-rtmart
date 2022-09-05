$(document).ready(function () {
    // DataTables
    dataTablesSurveyReport();

    function dataTablesSurveyReport() {
        $("#survey-report .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-8'<'filter-survey-report'>tl><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/rtsales/surveyreport/get",
                data: function (d) {
                    d.fromDate = $("#survey-report #from_date").val();
                    d.toDate = $("#survey-report #to_date").val();
                    d.filterValid = $(
                        "#survey-report .filter-valid select"
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
                    data: "CreatedDate",
                    name: "CreatedDate",
                    type: "date",
                },
                {
                    data: "Sales",
                    name: "Sales",
                },
                {
                    data: "TeamName",
                    name: "TeamName",
                },
                {
                    data: "StoreID",
                    name: "StoreID",
                },
                {
                    data: "StoreName",
                    name: "StoreName",
                },
                {
                    data: "PhoneNumber",
                    name: "PhoneNumber",
                },
                {
                    data: "ProductID",
                    name: "ProductID",
                },
                {
                    data: "ProductName",
                    name: "ProductName",
                },
                {
                    data: "PurchasePrice",
                    name: "PurchasePrice",
                },
                {
                    data: "SellingPrice",
                    name: "SellingPrice",
                },
                {
                    data: "Supplier",
                    name: "Supplier",
                },
                {
                    data: "Photo",
                    name: "Photo",
                    searchable: false,
                    orderable: false,
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "SurveyReport"
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
                        columns: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                        orthogonal: "export",
                    },
                },
            ],
            order: [2, "desc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
            aoColumnDefs: [
                {
                    aTargets: [10, 11],
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

    $("table").on("click", ".btn-photo", function () {
        const baseImg = $('meta[name="base-image"]').attr("content");
        const photo = $(this).data("photo");
        let div = "";
        $.each(photo, function (index, value) {
            div += `<div class="m-2">
                        <h5 class="mb-1 text-center">${value.TypePhoto.toUpperCase()}</h5>
                        <img src="${baseImg}/rtsales/visitsurvey/${
                value.UrlPhoto
            }" 
                            class="rounded" width="200" height="200" style="object-fit: cover">
                    </div>`;
        });
        $("#photo").html(div);
    });

    // Create element for DateRange Filter
    $("div.filter-survey-report").html(`<div class="input-group">
                            <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" readonly>
                            <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm" readonly>
                            <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                            <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-2">Refresh</button>
                            <div class="filter-valid ml-2">
                                <select class="form-control form-control-sm">
                                    <option selected disabled hidden>Filter Valid</option>
                                    <option value="">All</option>
                                    <option value="valid">Valid Checked</option>
                                    <option value="invalid">Valid Unchecked</option>
                                </select>
                            </div>
                        </div>`);

    // Setting Awal Daterangepicker
    $("#survey-report #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#survey-report #to_date").daterangepicker({
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

        $("#survey-report #to_date").daterangepicker({
            minDate: $("#survey-report #from_date").val(),
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

        $("#survey-report #from_date").daterangepicker({
            maxDate: $("#survey-report #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#survey-report .filter-survey-report").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#survey-report .filter-survey-report").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    const d = new Date();

    const dateNow = d.toISOString().split("T")[0];

    // Menyisipkan Placeholder Date
    $("#survey-report #from_date").val("");
    $("#survey-report #to_date").val("");
    $("#survey-report #from_date").attr("placeholder", dateNow);
    $("#survey-report #to_date").attr("placeholder", dateNow);

    // Event Listener saat tombol refresh diklik
    $("#survey-report #refresh").click(function () {
        $("#survey-report #from_date").val("");
        $("#survey-report #to_date").val("");
        $("#survey-report .table-datatables").DataTable().search("");
        $("#survey-report .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#survey-report #filter").click(function () {
        $("#survey-report .table-datatables").DataTable().ajax.reload();
    });

    $("#survey-report .filter-valid select").change(function () {
        $("#survey-report .table-datatables").DataTable().ajax.reload();
    });

    $("#survey-report table").on("change", ".check-isvalid", function () {
        const visitSurveyID = $(this).val();
        const checked = $(this).prop("checked");
        const checkbox = $(this);

        if (checked === true) {
            $.ajax({
                url: `/rtsales/surveyreport/update-valid/${visitSurveyID}/true`,
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
            $("#survey-report .table-datatables").DataTable().ajax.reload();
        } else {
            $.ajax({
                url: `/rtsales/surveyreport/update-valid/${visitSurveyID}/false`,
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
            $("#survey-report .table-datatables").DataTable().ajax.reload();
        }
    });
});
