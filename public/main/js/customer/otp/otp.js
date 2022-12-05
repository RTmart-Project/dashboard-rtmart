$(document).ready(function () {
    // DataTables
    dataTablesOtp();

    function dataTablesOtp() {
        $('#customer-otp .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'<'filter-customer-otp'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            "ajax": {
                url: "/customer/otp/get",
                data: function (d) {
                    d.fromDate = $('#customer-otp #from_date').val();
                    d.toDate = $('#customer-otp #to_date').val();
                }
            },
            columns: [
                {
                    data: 'PhoneNumber',
                    name: 'ms_verification.PhoneNumber'
                },
                {
                    data: 'OTP',
                    name: 'OTP',
                    searchable: false
                },
                {
                    data: 'IsVerified',
                    name: 'IsVerified',
                    searchable: false
                },
                {
                    data: 'SendOn',
                    name: 'SendOn',
                    type: 'date',
                    searchable: false
                },
                {
                    data: 'ReceiveOn',
                    name: 'ReceiveOn',
                    type: 'date',
                    searchable: false
                }
            ],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('OtpCustomer');
                },
                action: exportDatatableHelper.newExportAction,
                text: 'Export',
                titleAttr: 'Excel',
                exportOptions: {
                    modifier: {
                        page: 'all'
                    },
                    columns: [0, 1, 2, 3, 4],
                    orthogonal: 'export'
                },
            }],
            "order": [3, 'desc'],
            "lengthChange": false,
            "responsive": true,
            "autoWidth": false
        });
    }

    // Create element for DateRange Filter
    $("div.filter-customer-otp").html(`<div class="input-group">
                            <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                                readonly>
                            <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                                readonly>
                            <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                            <button type="button" name="refresh" id="refresh"
                                class="btn btn-sm btn-warning ml-2">Refresh</button>
                        </div>`);

    // Setting Awal Daterangepicker
    $('#customer-otp #from_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // Setting Awal Daterangepicker
    $('#customer-otp #to_date').daterangepicker({
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

        $('#customer-otp #to_date').daterangepicker({
            minDate: $("#customer-otp #from_date").val(),
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

        $('#customer-otp #from_date').daterangepicker({
            maxDate: $("#customer-otp #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        })
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $('#customer-otp .filter-customer-otp').on('change', '#from_date', function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $('#customer-otp .filter-customer-otp').on('change', '#to_date', function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $('#customer-otp #from_date').val('');
    $('#customer-otp #to_date').val('');
    $('#customer-otp #from_date').attr("placeholder", "From Date");
    $('#customer-otp #to_date').attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#customer-otp #refresh").click(function () {
        $('#customer-otp #from_date').val('');
        $('#customer-otp #to_date').val('');
        $('#customer-otp .table-datatables').DataTable().search('');
        $('#customer-otp .table-datatables').DataTable().ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#customer-otp #filter").click(function () {
        $('#customer-otp .table-datatables').DataTable().ajax.reload();
    });
});