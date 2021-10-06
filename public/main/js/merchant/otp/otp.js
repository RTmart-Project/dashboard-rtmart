$(document).ready(function () {
    // DataTables
    dataTablesOtp();

    function dataTablesOtp() {
        $('#merchant-otp .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'<'filter-merchant-otp'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            "ajax": {
                url: "/merchant/otp/get",
                data: function (d) {
                    d.fromDate = $('#merchant-otp #from_date').val();
                    d.toDate = $('#merchant-otp #to_date').val();
                }
            },
            columns: [
                {
                    data: 'PhoneNumber',
                    name: 'PhoneNumber'
                },
                {
                    data: 'OTP',
                    name: 'OTP'
                },
                {
                    data: 'IsVerified',
                    name: 'IsVerified'
                },
                {
                    data: 'SendOn',
                    name: 'SendOn'
                },
                {
                    data: 'ReceiveOn',
                    name: 'ReceiveOn'
                }
            ],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('OtpMerchant');
                },
                text: 'Export',
                titleAttr: 'Excel',
                exportOptions: {
                    modifier: {
                        page: 'all'
                    },
                    columns: [0, 1],
                    orthogonal: 'export'
                },
            }],
            "lengthChange": false,
            "responsive": true,
            "autoWidth": false
        });
    }

    // Create element for DateRange Filter
    $("div.filter-merchant-otp").html(`<div class="input-group">
                            <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                                readonly>
                            <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                                readonly>
                            <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                            <button type="button" name="refresh" id="refresh"
                                class="btn btn-sm btn-warning ml-2">Refresh</button>
                        </div>`);

    // Setting Awal Daterangepicker
    $('#merchant-otp #from_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // Setting Awal Daterangepicker
    $('#merchant-otp #to_date').daterangepicker({
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

        $('#merchant-otp #to_date').daterangepicker({
            minDate: $("#merchant-otp #from_date").val(),
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

        $('#merchant-otp #from_date').daterangepicker({
            maxDate: $("#merchant-otp #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        })
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $('#merchant-otp .filter-merchant-otp').on('change', '#from_date', function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $('#merchant-otp .filter-merchant-otp').on('change', '#to_date', function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $('#merchant-otp #from_date').val('');
    $('#merchant-otp #to_date').val('');
    $('#merchant-otp #from_date').attr("placeholder", "From Date");
    $('#merchant-otp #to_date').attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#merchant-otp #refresh").click(function () {
        $('#merchant-otp #from_date').val('');
        $('#merchant-otp #to_date').val('');
        $('#merchant-otp .table-datatables').DataTable().search('');
        $('#merchant-otp .table-datatables').DataTable().ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#merchant-otp #filter").click(function () {
        $('#merchant-otp .table-datatables').DataTable().ajax.reload();
    });
});