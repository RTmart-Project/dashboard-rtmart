$(document).ready(function () {
       
    // DataTables
    dataTablesProductLists();

    function dataTablesProductLists() {

        $('#product-list .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            "ajax": {
                url: "/master/product/list/get/"
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
                    data: 'Brand',
                    name: 'Brand'
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
                },
                {
                    data: 'Action',
                    name: 'Action'
                }
            ],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('ProductLists');
                },
                text: 'Export',
                titleAttr: 'Excel',
                exportOptions: {
                    modifier: {
                        page: 'all'
                    },
                    columns: [0, 1, 3, 4, 5, 6, 7, 8],
                    orthogonal: 'export'
                },
            }],
            "lengthChange": false,
            "responsive": true,
            "autoWidth": false,
            "aoColumnDefs": [
                {
                    "aTargets": [8],
                    "mRender": function (data, type, full) {
                        if (type === 'export') {
                            return data;
                        } else {
                            if (data == null || data == "") {
                                return data;
                            } else {
                                var currencySeperatorFormat = thousands_separators(data)
                                return currencySeperatorFormat;
                            }
                        }
                    }
                },
                {
                    "aTargets": [9],
                    "orderable": false
                }
            ]
        });
    }

    $('#product-list table').on('click', '.lihat-gambar', function (e) {
        e.preventDefault();
        const urlImg = $(this).attr("href");
        const productName = $(this).data("product-name");
        $.dialog({
            title: `${productName}`,
            content: `<img  style="object-fit: contain; height: 350px; width: 100%;" src="${urlImg}">`,
        });
    });
});