$(document).ready(function () {
    // DataTables
    dataTablesMerchantAccount();

    function dataTablesMerchantAccount() {
        $('#merchant-account .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-7'<'filter-merchant-account'>tl><'col-sm-12 col-md-1'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            "ajax": {
                url: "/merchant/account/get",
                data: function (d) {
                    d.fromDate = $('#merchant-account #from_date').val();
                    d.toDate = $('#merchant-account #to_date').val();
                    d.distributorId = $('#merchant-account .select-filter-custom select').val();
                }
            },
            columns: [
                {
                    data: 'MerchantID',
                    name: 'MerchantID'
                },
                {
                    data: 'StoreName',
                    name: 'StoreName'
                },
                {
                    data: 'PhoneNumber',
                    name: 'PhoneNumber'
                },
                {
                    data: 'CreatedDate',
                    name: 'CreatedDate'
                },
                {
                    data: 'StoreAddress',
                    name: 'StoreAddress'
                },
                {
                    data: 'ReferralCode',
                    name: 'ReferralCode'
                },
                {
                    data: 'DistributorName',
                    name: 'DistributorName'
                },
                {
                    data: 'Action',
                    name: 'Action'
                }
            ],
            "order": [[ 3, "desc" ]],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('MerchantAccount');
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
            "columnDefs": [{
                "targets": [7],
                "orderable": false
            }],
            "lengthChange": false,
            "responsive": true,
            "autoWidth": false
        });
    }

    // Create element for DateRange Filter
    $("div.filter-merchant-account").html(`<div class="input-group">
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

    // Setting Awal Daterangepicker
    $('#merchant-account #from_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // Setting Awal Daterangepicker
    $('#merchant-account #to_date').daterangepicker({
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

        $('#merchant-account #to_date').daterangepicker({
            minDate: $("#merchant-account #from_date").val(),
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

        $('#merchant-account #from_date').daterangepicker({
            maxDate: $("#merchant-account #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        })
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $('#merchant-account .filter-merchant-account').on('change', '#from_date', function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $('#merchant-account .filter-merchant-account').on('change', '#to_date', function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $('#merchant-account #from_date').val('');
    $('#merchant-account #to_date').val('');
    $('#merchant-account #from_date').attr("placeholder", "From Date");
    $('#merchant-account #to_date').attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#merchant-account #refresh").click(function () {
        $('#merchant-account #from_date').val('');
        $('#merchant-account #to_date').val('');
        $('#merchant-account .table-datatables').DataTable().search('');
        $('#merchant-account .select-filter-custom select').val('').change();
        $('#merchant-account .select-filter-custom select option[value=]').attr('selected', 'selected');
        $('#merchant-account .table-datatables').DataTable().ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#merchant-account #filter").click(function () {
        $('#merchant-account .table-datatables').DataTable().ajax.reload();
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
            $('#merchant-account .select-filter-custom select').append(option);
            customDropdownFilter.createCustomDropdowns();
            // $('#merchant-account .select-filter-custom select').val("All").change();
        }
    });

    // Event listener saat tombol select option diklik
    $("#merchant-account .select-filter-custom select").change(function () {
        $('#merchant-account .table-datatables').DataTable().ajax.reload();
        // console.log($('#merchant-account .select-filter-custom select').val())
    });

});