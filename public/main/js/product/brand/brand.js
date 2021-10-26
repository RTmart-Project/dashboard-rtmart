$(document).ready(function () {
       
    // DataTables
    dataTablesProductBrands();

    function dataTablesProductBrands() {

        $('#product-brand .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            "ajax": {
                url: "/master/product/brand/get/"
            },
            columns: [
                {
                    data: 'BrandID',
                    name: 'BrandID'
                },
                {
                    data: 'Brand',
                    name: 'Brand'
                },
                {
                    data: 'BrandImage',
                    name: 'BrandImage'
                },
                {
                    data: 'Action',
                    name: 'Action'
                }
            ],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('ProductBrands');
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
            "aoColumnDefs": [
                {
                    "aTargets": [3],
                    "orderable": false
                }
            ],
            "lengthChange": false,
            "responsive": true,
            "autoWidth": false
        });
    }
});