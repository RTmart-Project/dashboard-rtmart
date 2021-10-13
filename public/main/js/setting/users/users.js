$(document).ready(function () {
    // DataTables
    dataTablesSettingUsers();

    function dataTablesSettingUsers() {
        $('#setting-users .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'<'filter-setting-users'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            "ajax": {
                url: "/setting/users/get",
                data: function (d) {
                    d.fromDate = $('#setting-users #from_date').val();
                    d.toDate = $('#setting-users #to_date').val();
                }
            },
            columns: [
                {
                    data: 'UserID',
                    name: 'UserID'
                },
                {
                    data: 'Email',
                    name: 'Email'
                },
                {
                    data: 'Name',
                    name: 'Name'
                },
                {
                    data: 'PhoneNumber',
                    name: 'PhoneNumber'
                },
                {
                    data: 'RoleID',
                    name: 'RoleID'
                },
                {
                    data: 'Depo',
                    name: 'Depo'
                },
                {
                    data: 'CreatedDate',
                    name: 'CreatedDate'
                },
                {
                    data: 'Action',
                    name: 'Action'
                }
            ],
            "order": [[ 6, "desc" ]],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('SettingUsers');
                },
                text: 'Export',
                titleAttr: 'Excel',
                exportOptions: {
                    modifier: {
                        page: 'all'
                    },
                    columns: [0, 1, 2, 3, 4, 5, 6],
                    orthogonal: 'export'
                },
            }],
            "lengthChange": false,
            "responsive": true,
            "autoWidth": false,
            "columnDefs": [{
                "targets": [7],
                "orderable": false
            }]
        });
    }

    // Create element for DateRange Filter
    $("div.filter-setting-users").html(`<div class="input-group">
                            <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                                readonly>
                            <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                                readonly>
                            <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                            <button type="button" name="refresh" id="refresh"
                                class="btn btn-sm btn-warning ml-2">Refresh</button>
                        </div>`);

    // Setting Awal Daterangepicker
    $('#setting-users #from_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // Setting Awal Daterangepicker
    $('#setting-users #to_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    var bCodeChange = false;

    function dateStartChange() {
        if (bCodeChange == true)
            return;
        else
            bCodeChange = true;

        $('#setting-users #to_date').daterangepicker({
            minDate: $("#setting-users #from_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        })
        bCodeChange = false;
    }

    function dateEndChange() {
        if (bCodeChange == true)
            return;
        else
            bCodeChange = true;

        $('#setting-users #from_date').daterangepicker({
            maxDate: $("#setting-users #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        })
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $('#setting-users .filter-setting-users').on('change', '#from_date', function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $('#setting-users .filter-setting-users').on('change', '#to_date', function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $('#setting-users #from_date').val('');
    $('#setting-users #to_date').val('');
    $('#setting-users #from_date').attr("placeholder", "From Date");
    $('#setting-users #to_date').attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#setting-users #refresh").click(function () {
        $('#setting-users #from_date').val('');
        $('#setting-users #to_date').val('');
        $('#setting-users .table-datatables').DataTable().search('');
        $('#setting-users .table-datatables').DataTable().ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#setting-users #filter").click(function () {
        $('#setting-users .table-datatables').DataTable().ajax.reload();
    });

    $("#setting-users table").on('click', '.reset-password', function (e) {
        e.preventDefault();
        const name = $(this).data("user-name");
        const userId = $(this).data("user-id");
        $.confirm({
            title: 'Reset Password!',
            content: `Yakin ingin reset password <b>${name}</b> ?`,
            closeIcon: true,
            buttons: {
                Yakin: {
                    btnClass: 'btn-red',
                    draggable: true,
                    dragWindowGap: 0,
                    action: function () {
                        window.location = '/setting/users/reset-password/' + userId
                    }
                },
                tidak: function () {
                }
            }
        });
    });
});