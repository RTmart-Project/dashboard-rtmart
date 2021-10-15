$(document).ready(function () {
       
    // DataTables
    dataTablesProductUOM();

    function dataTablesProductUOM() {

        $('#product-uom .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            "ajax": {
                url: "/master/product/uom/get/"
            },
            columns: [
                {
                    data: 'ProductUOMID',
                    name: 'ProductUOMID'
                },
                {
                    data: 'ProductUOMName',
                    name: 'ProductUOMName'
                },
                {
                    data: 'Action',
                    name: 'Action'
                }
            ],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('ProductUOM');
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