$(document).ready(function () {
    // DataTables
    dataTablesVoucherList();

    function dataTablesVoucherList() {
        $('#voucher-list .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            "ajax": {
                url: "/voucher/list/get"
            },
            columns: [
                {
                    data: 'VoucherCode',
                    name: 'ms_voucher.VoucherCode'
                },
                {
                    data: 'VoucherName',
                    name: 'ms_voucher.VoucherName'
                },
                {
                    data: 'VoucherTypeName',
                    name: 'ms_voucher_type.VoucherTypeName'
                },
                {
                    data: 'PercentageValue',
                    name: 'ms_voucher.PercentageValue'
                },
                {
                    data: 'MaxNominalValue',
                    name: 'ms_voucher.MaxNominalValue'
                },
                {
                    data: 'ValidityPeriod',
                    name: 'ValidityPeriod'
                },
                {
                    data: 'IsActive',
                    name: 'ms_voucher.IsActive'
                },
                {
                    data: 'IsFor',
                    name: 'ms_voucher.IsFor'
                },
                {
                    data: 'Detail',
                    name: 'Detail',
                    orderable: false, 
                    searchable: false
                },
                {
                    data: 'Action',
                    name: 'Action',
                    orderable: false, 
                    searchable: false
                }
            ],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('VoucherList');
                },
                text: 'Export',
                titleAttr: 'Excel',
                exportOptions: {
                    modifier: {
                        page: 'all'
                    },
                    columns: [0, 1, 3, 4, 5],
                    orthogonal: 'export'
                },
            }],
            "lengthChange": false,
            "responsive": true,
            "autoWidth": false,
            "aoColumnDefs": [
                {
                    "aTargets": [4],
                    "mRender": function (data, type, full) {
                        if (type === 'export') {
                            return data;
                        } else {
                            if (data == null || data == "") {
                                return data;
                            } else {
                                const currencySeperatorFormat = thousands_separators(data)
                                return currencySeperatorFormat;
                            }
                        }
                    }
                }
            ]
        });
    }
});