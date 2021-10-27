$(document).ready(function () {
    // DataTables
    dataTablesDistributorAccount();

    function dataTablesDistributorAccount() {
        $('#distributor-account .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'<'filter-distributor-account'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            "ajax": {
                url: "/distributor/account/get",
                data: function (d) {
                    d.fromDate = $('#distributor-account #from_date').val();
                    d.toDate = $('#distributor-account #to_date').val();
                }
            },
            columns: [
                {
                    data: 'DistributorID',
                    name: 'DistributorID'
                },
                {
                    data: 'DistributorName',
                    name: 'DistributorName'
                },
                {
                    data: 'Email',
                    name: 'Email'
                },
                {
                    data: 'Address',
                    name: 'Address'
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
            "order": [[ 4, "desc" ]],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('DistributorAccounts');
                },
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
            "lengthChange": false,
            "responsive": true,
            "autoWidth": false,
            "columnDefs": [{
                "targets": [5],
                "orderable": false
            }]
        });
    }

    // Create element for DateRange Filter
    $("div.filter-distributor-account").html(`<div class="input-group">
                            <input type="text" name="from_date" id="from_date" class="form-control form-control-sm"
                                readonly>
                            <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm"
                                readonly>
                            <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                            <button type="button" name="refresh" id="refresh"
                                class="btn btn-sm btn-warning ml-2">Refresh</button>
                        </div>`);

    // Setting Awal Daterangepicker
    $('#distributor-account #from_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // Setting Awal Daterangepicker
    $('#distributor-account #to_date').daterangepicker({
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

        $('#distributor-account #to_date').daterangepicker({
            minDate: $("#distributor-account #from_date").val(),
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

        $('#distributor-account #from_date').daterangepicker({
            maxDate: $("#distributor-account #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        })
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $('#distributor-account .filter-distributor-account').on('change', '#from_date', function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $('#distributor-account .filter-distributor-account').on('change', '#to_date', function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $('#distributor-account #from_date').val('');
    $('#distributor-account #to_date').val('');
    $('#distributor-account #from_date').attr("placeholder", "From Date");
    $('#distributor-account #to_date').attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#distributor-account #refresh").click(function () {
        $('#distributor-account #from_date').val('');
        $('#distributor-account #to_date').val('');
        $('#distributor-account .table-datatables').DataTable().search('');
        $('#distributor-account .table-datatables').DataTable().ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#distributor-account #filter").click(function () {
        $('#distributor-account .table-datatables').DataTable().ajax.reload();
    });

});