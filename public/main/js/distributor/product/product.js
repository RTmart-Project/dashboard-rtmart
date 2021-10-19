$(document).ready(function () {
    // DataTables
    dataTablesDistributorProductDetails();

    function dataTablesDistributorProductDetails() {
        const urlDistributorProductDetails = window.location.pathname; // return segment1/segment2/segment3/segment4
        const segmentUrl = urlDistributorProductDetails.split( '/' );
        const distributorId = segmentUrl.pop();

        $('#distributor-product-details .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            "ajax": {
                url: "/distributor/account/product/get/" + distributorId
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
                    data: 'Grade',
                    name: 'Grade'
                },
                {
                    data: 'Price',
                    name: 'Price'
                }
            ],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('DistributorProductDetails');
                },
                text: 'Export',
                titleAttr: 'Excel',
                exportOptions: {
                    modifier: {
                        page: 'all'
                    },
                    columns: [0, 1, 2, 3],
                    orthogonal: 'export'
                },
            }],
            "lengthChange": false,
            "responsive": true,
            "autoWidth": false,
            "aoColumnDefs": [
                {
                    "aTargets": [3],
                    "mRender": function (data, type, full) {
                        if (type === 'export') {
                            return data;
                        } else {
                            const currencySeperatorFormat = thousands_separators(data)
                            return currencySeperatorFormat;
                        }
                    }
                }
            ]
        });
    }
});