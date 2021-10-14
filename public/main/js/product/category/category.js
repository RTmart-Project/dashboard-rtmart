$(document).ready(function () {
       
    // DataTables
    dataTablesProductCategories();

    function dataTablesProductCategories() {

        $('#product-category .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            "ajax": {
                url: "/master/product/category/get/"
            },
            columns: [
                {
                    data: 'ProductCategoryID',
                    name: 'ProductCategoryID'
                },
                {
                    data: 'ProductCategoryName',
                    name: 'ProductCategoryName'
                }
            ],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('ProductCategories');
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
});