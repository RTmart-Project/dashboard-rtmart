$(document).ready(function () {
    // DataTables
    dataTablesMerchantProductDetails();

    function dataTablesMerchantProductDetails() {
        const urlMerchantProductDetails = window.location.pathname; // return segment1/segment2/segment3/segment4
        const segmentUrl = urlMerchantProductDetails.split( '/' );
        const merchantId = segmentUrl.pop();

        $('#merchant-product-details .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            "ajax": {
                url: "/merchant/account/product/get/" + merchantId
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
                    data: 'ProductImage',
                    name: 'ProductImage'
                },
                {
                    data: 'ProductCategoryName',
                    name: 'ProductCategoryName'
                },
                {
                    data: 'ProductTypeName',
                    name: 'ProductTypeName'
                },
                {
                    data: 'ProductUOMName',
                    name: 'ProductUOMName'
                },
                {
                    data: 'ProductUOMDesc',
                    name: 'ProductUOMDesc'
                },
                {
                    data: 'Price',
                    name: 'Price'
                }
            ],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('MerchantProductDetails');
                },
                text: 'Export',
                titleAttr: 'Excel',
                exportOptions: {
                    modifier: {
                        page: 'all'
                    },
                    columns: [0, 1, 3, 4, 5, 6, 7],
                    orthogonal: 'export'
                },
            }],
            "lengthChange": false,
            "responsive": true,
            "autoWidth": false,
            "aoColumnDefs": [
                {
                    "aTargets": [7],
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