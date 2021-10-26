$(document).ready(function () {
    // DataTables
    dataTablesMerchantRestockDetails();

    function dataTablesMerchantRestockDetails() {
        const urlMerchantRestockDetails = window.location.pathname; // return segment1/segment2/segment3/segment4
        const segmentUrl = urlMerchantRestockDetails.split( '/' );
        const stockOrderId = segmentUrl.pop();

        $('#merchant-restock-details .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            "ajax": {
                url: "/merchant/restock/detail/get/" + stockOrderId
            },
            columns: [
                {
                    data: 'ProductID',
                    name: 'ProductID'
                },
                {
                    data: 'ProductName',
                    name: 'ProductName'
                },
                {
                    data: 'PromisedQuantity',
                    name: 'PromisedQuantity'
                },
                {
                    data: 'Price',
                    name: 'Price'
                },
                {
                    data: 'Discount',
                    name: 'Discount'
                },
                {
                    data: 'Nett',
                    name: 'Nett'
                },
                {
                    data: 'SubTotalPrice',
                    name: 'SubTotalPrice'
                }
            ],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('RestockMerchantDetails');
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
            "aoColumnDefs": [
                {
                    "aTargets": [3, 4, 5, 6],
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
            ],
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    data;

                // Remove the formatting to get integer data for summation
                var intVal = function(i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i : 0;
                };

                var grandTotal = api
                    .column(6, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Update footer
                $(api.column(6).footer()).html(thousands_separators(grandTotal));
            }
        });
    }
});