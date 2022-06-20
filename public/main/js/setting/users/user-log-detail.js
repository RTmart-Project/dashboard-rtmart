$(document).ready(function () {
    // DataTables
    dataTablesSettingUsers();

    function dataTablesSettingUsers() {
        $("#user-log-detail .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-user-log-detail'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            ajax: {
                url: window.location.href,
                data: function (d) {
                    d.fromDate = $("#user-log-detail #from_date").val();
                    d.toDate = $("#user-log-detail #to_date").val();
                },
            },
            columns: [
                {
                    data: "UserID",
                    name: "ms_user_activity_log.UserID",
                },
                {
                    data: "Name",
                    name: "ms_user.Name",
                },
                {
                    data: "URL",
                    name: "ms_user_activity_log.URL",
                },
                {
                    data: "RouteName",
                    name: "ms_user_activity_log.RouteName",
                },
                {
                    data: "IPAddress",
                    name: "ms_user_activity_log.IPAddress",
                },
                {
                    data: "CreatedDate",
                    name: "ms_user_activity_log.CreatedDate",
                },
                {
                    data: "Browser",
                    name: "ms_user_activity_log.Browser",
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "UserLog"
                        );
                    },
                    className: "btn-sm",
                    text: "Export",
                    titleAttr: "Excel",
                    exportOptions: {
                        modifier: {
                            page: "all",
                        },
                        columns: [0, 1, 2, 3, 4, 5, 6],
                        orthogonal: "export",
                    },
                },
            ],
            order: [5, "desc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }

    // Create element for DateRange Filter
    $("div.filter-user-log-detail").html(`<div class="input-group">
                          <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                              readonly>
                          <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                              readonly>
                          <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                          <button type="button" name="refresh" id="refresh"
                              class="btn btn-sm btn-warning ml-2">Refresh</button>
                      </div>`);

    // Setting Awal Daterangepicker
    $("#user-log-detail #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#user-log-detail #to_date").daterangepicker({
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

        $("#user-log-detail #to_date").daterangepicker({
            minDate: $("#user-log-detail #from_date").val(),
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

        $("#user-log-detail #from_date").daterangepicker({
            maxDate: $("#user-log-detail #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#user-log-detail .filter-user-log-detail").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );
    // Disabled input from date ketika to date berubah
    $("#user-log-detail .filter-user-log-detail").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    const d = new Date();
    const date = `${d.getFullYear()}-${("0" + (d.getMonth() + 1)).slice(-2)}-${(
        "0" + d.getDate()
    ).slice(-2)}`;

    // Menyisipkan Placeholder Date
    $("#user-log-detail #from_date").val("");
    $("#user-log-detail #to_date").val("");
    $("#user-log-detail #from_date").attr("placeholder", date);
    $("#user-log-detail #to_date").attr("placeholder", date);

    // Event Listener saat tombol refresh diklik
    $("#user-log-detail #refresh").click(function () {
        $("#user-log-detail #from_date").val("");
        $("#user-log-detail #to_date").val("");
        $("#user-log-detail .table-datatables").DataTable().search("");
        $("#user-log-detail .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#user-log-detail #filter").click(function () {
        $("#user-log-detail .table-datatables").DataTable().ajax.reload();
    });
});
